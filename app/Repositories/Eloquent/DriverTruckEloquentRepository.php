<?php

namespace App\Repositories\Eloquent;

use App\Repositories\DriverTruckRepositoryInterface;
use App\DriverTruck;
use DB;
use App\Common\Helpers\DBHelper;

class DriverTruckEloquentRepository extends BaseEloquentRepository implements DriverTruckRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = DriverTruck::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->where('driver_trucks.active', true)
            ->leftJoin('drivers', 'drivers.id', '=', 'driver_trucks.driver_id')
            ->leftJoin('trucks', 'trucks.id', '=', 'driver_trucks.truck_id')
            ->select(
                'driver_trucks.id', 'driver_trucks.driver_id', 'driver_trucks.truck_id'
                , 'drivers.fullname as driver_fullname'
                , DB::raw(DBHelper::getWithAreaCodeNumberPlate('trucks.area_code', 'trucks.number_plate', 'truck_area_code_number_plate'))
            )
            ->get();
    }

    public function deactivateByDriverId($driver_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('driver_id', $driver_id)
            ->update(['active' => false]);
    }

    public function deactivateByTruckId($truck_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('truck_id', $truck_id)
            ->update(['active' => false]);
    }
}