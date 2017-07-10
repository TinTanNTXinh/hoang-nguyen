<?php

namespace App\Repositories\Eloquent;

use App\Repositories\DriverTruckRepositoryInterface;
use App\DriverTruck;
use DB;
use App\Common\DBHelper;

class DriverTruckEloquentRepository extends EloquentBaseRepository implements DriverTruckRepositoryInterface
{
    /** ===== INIT MODEL ===== */
    public function setModel()
    {
        return DriverTruck::class;
    }

    /** ===== PUBLIC FUNCTION ===== */
    public function allSkeleton()
    {
        return $this->allActiveQuery('driver_trucks.active')
            ->leftJoin('drivers', 'drivers.id', '=', 'driver_trucks.driver_id')
            ->leftJoin('trucks', 'trucks.id', '=', 'driver_trucks.truck_id')
            ->select(
                'driver_trucks.id', 'driver_trucks.driver_id', 'driver_trucks.truck_id'
                , 'drivers.fullname as driver_fullname'
                , DB::raw(DBHelper::getWithAreaCodeNumberPlate('trucks.area_code', 'trucks.number_plate', 'truck_area_code_number_plate'))
            );
    }

    public function oneSkeleton($id)
    {
        return $this->allSkeleton()->where('driver_trucks.id', $id);
    }

    public function deactiveByDriverId($driver_id)
    {
        return $this->allActiveQuery()
            ->where('driver_id', $driver_id)
            ->update(['active' => false]);
    }

    public function deactiveByTruckId($truck_id)
    {
        return $this->allActiveQuery()
            ->where('truck_id', $truck_id)
            ->update(['active' => false]);
    }
}