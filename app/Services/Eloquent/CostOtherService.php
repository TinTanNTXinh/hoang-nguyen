<?php

namespace App\Services\Eloquent;

use App\Services\CostOtherServiceInterface;
use App\Repositories\CostOtherRepositoryInterface;
use App\Repositories\TruckRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class CostOtherService implements CostOtherServiceInterface
{
    private $user;
    private $table_name;

    protected $costOtherRepo, $truckRepo;

    public function __construct(CostOtherRepositoryInterface $costOtherRepo
        , TruckRepositoryInterface $truckRepo)
    {
        $this->costOtherRepo = $costOtherRepo;
        $this->truckRepo     = $truckRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'cost_other';
    }

    public function readAll()
    {
        $all = $this->costOtherRepo->allSkeleton()->get();

        $trucks = $this->truckRepo->allSkeleton()->get();

        return [
            'cost_others' => $all,
            'trucks'      => $trucks
        ];
    }

    public function readOne($id)
    {
        $one = $this->costOtherRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'      => $this->costOtherRepo->generateCode('COSTOTHER'),
                'type'      => 'OTHER',
                'vat'       => 0,
                'after_vat' => $data['after_vat'],

                'fuel_id'       => null,
                'quantum_liter' => null,
                'refuel_date'   => null,

                'checkin_date'  => null,
                'checkout_date' => null,
                'total_day'     => null,

                'note'         => $data['note'],
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => DateTimeHelper::toStringDateTimeClientForDB($data['created_date']),
                'updated_date' => null,
                'active'       => true,
                'truck_id'     => $data['truck_id'],
                'invoice_id'   => 0
            ];

            $one = $this->costOtherRepo->create($i_one);

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

            $one = $this->costOtherRepo->find($data['id']);

            $i_one = [
                'type'      => 'OTHER',
                'vat'       => 0,
                'after_vat' => $data['after_vat'],

                'fuel_id'       => null,
                'quantum_liter' => null,
                'refuel_date'   => null,

                'note'         => $data['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => DateTimeHelper::toStringDateTimeClientForDB($data['created_date']),
                'active'       => true,
                'truck_id'     => $data['truck_id']
            ];

            $one = $this->costOtherRepo->update($one, $i_one);

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

            $one = $this->costOtherRepo->deactivate($id);

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

            $one = $this->costOtherRepo->destroy($id);

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

        $filtered = $this->costOtherRepo->allSkeleton();

        $filtered = $this->costOtherRepo->filterFromDateToDate($filtered, 'costs.created_date', $from_date, $to_date);

        $filtered = $this->costOtherRepo->filterRangeDate($filtered, 'costs.created_date', $range);

        return [
            'cost_others' => $filtered->get()
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

        $one = $this->costOtherRepo->find($id);
        if ($one->invoice_id != 0) {
            array_push($msg_error, 'Không thể sửa hay xóa chi phí khác đã xuất phiếu thanh toán.');
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