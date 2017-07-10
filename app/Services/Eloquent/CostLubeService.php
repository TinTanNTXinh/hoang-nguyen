<?php

namespace App\Services\Eloquent;

use App\Services\CostLubeServiceInterface;
use App\Repositories\CostLubeRepositoryInterface;
use App\Repositories\LubeRepositoryInterface;
use App\Repositories\TruckRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class CostLubeService implements CostLubeServiceInterface
{
    private $user;
    private $table_name;

    protected $costLubeRepo, $lubeRepo, $truckRepo;

    public function __construct(CostLubeRepositoryInterface $costLubeRepo
        , LubeRepositoryInterface $lubeRepo
        , TruckRepositoryInterface $truckRepo)
    {
        $this->costLubeRepo = $costLubeRepo;
        $this->lubeRepo     = $lubeRepo;
        $this->truckRepo    = $truckRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'cost_lube';
    }

    public function readAll()
    {
        $all = $this->costLubeRepo->allSkeleton()->get();

        $trucks = $this->truckRepo->allSkeleton()->get();

        $lube = $this->lubeRepo->findByApplyDate();

        return [
            'cost_lubes' => $all,
            'trucks'     => $trucks,
            'lube'       => $lube
        ];
    }

    public function readOne($id)
    {
        $one = $this->costLubeRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'      => $this->costLubeRepo->generateCode('COSTLUBE'),
                'type'      => 'LUBE',
                'vat'       => $data['vat'],
                'after_vat' => $data['after_vat'],

                'fuel_id'       => $data['fuel_id'],
                'quantum_liter' => $data['quantum_liter'],
                'refuel_date'   => DateTimeHelper::toStringDateTimeClientForDB($data['refuel_date']),

                'checkin_date'  => null,
                'checkout_date' => null,
                'total_day'     => null,

                'note'         => $data['note'],
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true,
                'truck_id'     => $data['truck_id'],
                'invoice_id'   => 0
            ];

            $one = $this->costLubeRepo->create($i_one);

            if (!$one) {
                DB::rollback();
                return false;
            }

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            return false;
        }
    }

    public function updateOne($data)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateUpdateOne($data['id']);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->costLubeRepo->find($data['id']);

            $i_one = [
                'type'      => 'LUBE',
                'vat'       => $data['vat'],
                'after_vat' => $data['after_vat'],

                'fuel_id'       => $data['fuel_id'],
                'quantum_liter' => $data['quantum_liter'],
                'refuel_date'   => DateTimeHelper::toStringDateTimeClientForDB($data['refuel_date']),

                'note'         => $data['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true,
                'truck_id'     => $data['truck_id']
            ];

            $one = $this->costLubeRepo->update($one, $i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function deactivateOne($id)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateDeactivateOne($id);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->costLubeRepo->deactivate($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function deleteOne($id)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];
        try {
            DB::beginTransaction();

            // Validate
            $validate_data = $this->validateDeleteOne($id);
            if (!$validate_data['status']) {
                return $validate_data;
            }

            $one = $this->costLubeRepo->destroy($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function searchOne($filter)
    {
        $from_date = $filter['from_date'];
        $to_date   = $filter['to_date'];
        $range     = $filter['range'];

        $filtered = $this->costLubeRepo->allSkeleton();

        $filtered = $this->costLubeRepo->filterFromDateToDate($filtered, 'costs.created_date', $from_date, $to_date);

        $filtered = $this->costLubeRepo->filterRangeDate($filtered, 'costs.created_date', $range);

        return [
            'cost_lubes' => $filtered->get()
        ];
    }

    /** ===== MY VALIDATE ===== */
    public function validateUpdateOne($id)
    {
        return $this->validateDeactivateOne($id);
    }

    public function validateDeactivateOne($id)
    {
        $msg_error = [];

        $one = $this->costLubeRepo->find($id);
        if ($one->invoice_id != 0) {
            array_push($msg_error, 'Không thể sửa hay xóa chi phí nhớt đã xuất phiếu thanh toán.');
        }

        return [
            'status' => count($msg_error) > 0 ? false : true,
            'errors' => $msg_error
        ];
    }

    public function validateDeleteOne($id)
    {
        return $this->validateDeactivateOne($id);
    }

    /** ===== MY FUNCTION ===== */

}