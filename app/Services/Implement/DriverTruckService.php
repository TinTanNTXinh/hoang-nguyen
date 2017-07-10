<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\DriverTruckServiceInterface;
use App\Repositories\DriverTruckRepositoryInterface;
use App\Repositories\DriverRepositoryInterface;
use App\Repositories\TruckRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class DriverTruckService implements DriverTruckServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $driverTruckRepo, $driverRepo, $truckRepo;

    public function __construct(AuthServiceInterface $authService
        , DriverTruckRepositoryInterface $driverTruckRepo
        , DriverRepositoryInterface $driverRepo
        , TruckRepositoryInterface $truckRepo)
    {
        $this->authService     = $authService;
        $this->driverTruckRepo = $driverTruckRepo;
        $this->driverRepo      = $driverRepo;
        $this->truckRepo       = $truckRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'driver_truck';
    }

    public function readAll()
    {
        $all = $this->driverTruckRepo->findAllActiveSkeleton();

        $drivers = $this->driverRepo->findAllActive();

        $trucks = $this->truckRepo->findAllActiveSkeleton();

        return [
            'driver_trucks' => $all,
            'drivers'       => $drivers,
            'trucks'        => $trucks
        ];
    }

    public function readOne($id)
    {
        $one = $this->driverTruckRepo->findOneActiveSkeleton($id);

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
                'driver_id'    => $data['driver_id'],
                'truck_id'     => $data['truck_id'],
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true
            ];

            // Deactive DriverTruck
            $this->driverTruckRepo->deactivateByDriverId($i_one['driver_id']);
            $this->driverTruckRepo->deactivateByTruckId($i_one['truck_id']);

            $one = $this->driverTruckRepo->createOne($i_one);

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

            $one = $this->driverTruckRepo->findOneActive($data['id']);

            $i_one = [
                'driver_id'    => $data['driver_id'],
                'truck_id'     => $data['truck_id'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true
            ];

            // Deactive DriverTruck
            $this->driverTruckRepo->deactivateByDriverId($i_one['driver_id']);
            $this->driverTruckRepo->deactivateByTruckId($i_one['truck_id']);

            $one = $this->driverTruckRepo->updateOne($one->id, $i_one);

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

            $one = $this->driverTruckRepo->deactivateOne($id);

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

            $one = $this->driverTruckRepo->destroyOne($id);

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
        $driver_id = $filter['driver_id'];
        $truck_id  = $filter['truck_id'];

        $filtered = $this->driverTruckRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_at', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_at', $range);

        if ($driver_id != 0)
            $filtered = $filtered->where('driver_id', $driver_id);

        if ($truck_id != 0)
            $filtered = $filtered->where('truck_id', $truck_id);

        return [
            'driver_trucks' => $filtered->values()
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

//        $skip_id = isset($data['id']) ? [$data['id']] : [];
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