<?php

namespace App\Services\Eloquent;

use App\Services\DriverTruckServiceInterface;
use App\Repositories\DriverTruckRepositoryInterface;
use App\Repositories\DriverRepositoryInterface;
use App\Repositories\TruckRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class DriverTruckService implements DriverTruckServiceInterface
{
    private $user;
    private $table_name;

    protected $driverTruckRepo, $driverRepo, $truckRepo;

    public function __construct(DriverTruckRepositoryInterface $driverTruckRepo
        , DriverRepositoryInterface $driverRepo
        , TruckRepositoryInterface $truckRepo)
    {
        $this->driverTruckRepo = $driverTruckRepo;
        $this->driverRepo      = $driverRepo;
        $this->truckRepo       = $truckRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'driver_truck';
    }

    public function readAll()
    {
        $all = $this->driverTruckRepo->allSkeleton()->get();

        $drivers = $this->driverRepo->allActive();

        $trucks = $this->truckRepo->allSkeleton()->get();

        return [
            'driver_trucks' => $all,
            'drivers'       => $drivers,
            'trucks'        => $trucks
        ];
    }

    public function readOne($id)
    {
        $one = $this->driverTruckRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
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
            $this->driverTruckRepo->deactiveByDriverId($i_one['driver_id']);
            $this->driverTruckRepo->deactiveByTruckId($i_one['truck_id']);

            $one = $this->driverTruckRepo->create($i_one);

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
        try {
            DB::beginTransaction();

            $one = $this->driverTruckRepo->find($data['id']);

            $i_one = [
                'driver_id'    => $data['driver_id'],
                'truck_id'     => $data['truck_id'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true
            ];

            // Deactive DriverTruck
            $this->driverTruckRepo->deactiveByDriverId($i_one['driver_id']);
            $this->driverTruckRepo->deactiveByTruckId($i_one['truck_id']);

            $one = $this->driverTruckRepo->update($one, $i_one);

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

    public function deactivateOne($id)
    {
        try {
            DB::beginTransaction();

            $one = $this->driverTruckRepo->deactivate($id);

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

    public function deleteOne($id)
    {
        try {
            DB::beginTransaction();

            $one = $this->driverTruckRepo->destroy($id);

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

    public function searchOne($filter)
    {
        $from_date = $filter['from_date'];
        $to_date   = $filter['to_date'];
        $range     = $filter['range'];

        $filtered = $this->driverTruckRepo->allSkeleton();

        $filtered = $this->driverTruckRepo->filterFromDateToDate($filtered, 'driver_trucks.created_date', $from_date, $to_date);

        $filtered = $this->driverTruckRepo->filterRangeDate($filtered, 'driver_trucks.created_date', $range);

        return [
            'driver_trucks' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}