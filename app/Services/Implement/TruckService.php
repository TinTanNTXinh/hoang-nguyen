<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\TruckServiceInterface;
use App\Repositories\TruckRepositoryInterface;
use App\Repositories\TruckTypeRepositoryInterface;
use App\Repositories\GarageRepositoryInterface;
use App\Repositories\FileRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class TruckService implements TruckServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $truckRepo, $truckTypeRepo, $garageRepo, $fileRepo;

    public function __construct(AuthServiceInterface $authService
        , TruckRepositoryInterface $truckRepo
        , TruckTypeRepositoryInterface $truckTypeRepo
        , GarageRepositoryInterface $garageRepo
        , FileRepositoryInterface $fileRepo)
    {
        $this->authService   = $authService;
        $this->truckRepo     = $truckRepo;
        $this->truckTypeRepo = $truckTypeRepo;
        $this->garageRepo    = $garageRepo;
        $this->fileRepo      = $fileRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'truck';
    }

    public function readAll()
    {
        $all = $this->truckRepo->findAllActiveSkeleton();

        $garages     = $this->garageRepo->findAllActive();
        $truck_types = $this->truckTypeRepo->findAllActiveSkeleton();
        $files       = $this->fileRepo->findAllActive();

        return [
            'trucks'      => $all,
            'garages'     => $garages,
            'truck_types' => $truck_types,
            'files'       => $files
        ];
    }

    public function readOne($id)
    {
        $one = $this->truckRepo->findOneActiveSkeleton($id);

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        $validates = $this->validateInput($data);
        if (!$validates['status'])
            return $validates;

        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            $i_one = [
                'code'                => $this->truckRepo->generateCode('TRUCK'),
                'area_code'           => $data['area_code'],
                'number_plate'        => $data['number_plate'],
                'trademark'           => $data['trademark'],
                'year_of_manufacture' => $data['year_of_manufacture'],
                'owner'               => $data['owner'],
                'length'              => $data['length'],
                'width'               => $data['width'],
                'height'              => $data['height'],
                'status'              => $data['status'],
                'note'                => $data['note'],
                'created_by'          => $this->user->id,
                'updated_by'          => 0,
                'created_date'        => date('Y-m-d'),
                'updated_date'        => null,
                'active'              => true,
                'truck_type_id'       => $data['truck_type_id'],
                'garage_id'           => $data['garage_id']
            ];

            $one = $this->truckRepo->createOne($i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            DB::commit();
            $result['id']     = $one->id;
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function updateOne($data)
    {
        $validates = $this->validateInput($data);
        if (!$validates['status'])
            return $validates;

        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            $one = $this->truckRepo->findOneActive($data['id']);

            $i_one = [
                'area_code'           => $data['area_code'],
                'number_plate'        => $data['number_plate'],
                'trademark'           => $data['trademark'],
                'year_of_manufacture' => $data['year_of_manufacture'],
                'owner'               => $data['owner'],
                'length'              => $data['length'],
                'width'               => $data['width'],
                'height'              => $data['height'],
                'status'              => $data['status'],
                'note'                => $data['note'],
                'updated_by'          => $this->user->id,
                'updated_date'        => date('Y-m-d'),
                'active'              => true,
                'truck_type_id'       => $data['truck_type_id'],
                'garage_id'           => $data['garage_id']
            ];

            $one = $this->truckRepo->updateOne($one->id, $i_one);

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

            $one = $this->truckRepo->deactivateOne($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Deactivate File
            $files = $this->fileRepo->findAllActiveByTableNameAndTableId('trucks', $id);
            $this->fileRepo->deactivateAllByIds($files->pluck('id')->toArray());

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

            $one = $this->truckRepo->destroyOne($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Delete File
            $files = $this->fileRepo->findAllActiveByTableNameAndTableId('trucks', $id);
            $this->fileRepo->destroyAllByIds($files->pluck('id')->toArray());

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
        $from_date     = $filter['from_date'];
        $to_date       = $filter['to_date'];
        $range         = $filter['range'];
        $garage_id     = $filter['garage_id'];
        $truck_id      = $filter['truck_id'];
        $truck_type_id = $filter['truck_type_id'];

        $filtered = $this->truckRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_date', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_date', $range);

        if ($garage_id != 0)
            $filtered = $filtered->where('garage_id', $garage_id);

        if ($truck_id != 0)
            $filtered = $filtered->where('id', $truck_id);

        if ($truck_type_id != 0)
            $filtered = $filtered->where('truck_type_id', $truck_type_id);

        return [
            'trucks' => $filtered->values()
        ];
    }

    /** ===== VALIDATE BASIC ===== */
    public function validateInput($data)
    {
        if (!$this->validateEmpty($data))
            return ['status' => false, 'errors' => 'Dữ liệu không hợp lệ.'];

        $msgs = $this->validateLogic($data);
        return $msgs;
    }

    public function validateEmpty($data)
    {
//        if (!$data['name']) return false;
        return true;
    }

    public function validateLogic($data)
    {
        $msg_error = [];

        $skip_id = isset($data['id']) ? [$data['id']] : [];

        if ($this->truckRepo->existsAreaCodeNumberPlate($data['area_code'], $data['number_plate'], $skip_id))
            array_push($msg_error, 'Số xe đã tồn tại.');
//
//        if ($data['code'] && $this->userRepo->existsValue('code', $data['code'], $skip_id))
//            array_push($msg_error, 'Mã đã tồn tại.');

//        if ($this->userRepo->existsValue('name', $data['name'], $skip_id))
//            array_push($msg_error, 'Tên đã tồn tại.');

        return [
            'status' => count($msg_error) > 0 ? false : true,
            'errors' => $msg_error
        ];
    }

    /** ===== VALIDATE ADVANCED ===== */
    public function validateUpdateOne($id)
    {
        return $this->validateDeactivateOne($id);
    }

    public function validateDeactivateOne($id)
    {
        $msg_error = [];

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