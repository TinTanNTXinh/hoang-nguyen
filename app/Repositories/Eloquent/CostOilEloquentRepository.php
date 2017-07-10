<?php

namespace App\Repositories\Eloquent;

use App\Repositories\CostOilRepositoryInterface;
use App\Cost;
use DB;
use App\Common\DBHelper;

class CostOilEloquentRepository extends EloquentBaseRepository implements CostOilRepositoryInterface
{
    /** ===== INIT MODEL ===== */
    public function setModel()
    {
        return Cost::class;
    }

    /** ===== PUBLIC FUNCTION ===== */
    public function allSkeleton()
    {
        return $this->allActiveQuery('costs.active')
            ->where('costs.type', 'OIL')
            ->leftJoin('fuels', 'fuels.id', '=', 'costs.fuel_id')
            ->leftJoin('trucks', 'trucks.id', '=', 'costs.truck_id')
            ->select('costs.*'
                , 'fuels.price as fuel_price'
                , DB::raw(DBHelper::getWithCurrencyFormat('fuels.price', 'fc_fuel_price'))
                , DB::raw(DBHelper::getWithCurrencyFormat('costs.after_vat', 'fc_after_vat'))
                , DB::raw(DBHelper::getWithDateTimeFormat('costs.refuel_date', 'fd_refuel_date'))
                , DB::raw(DBHelper::getWithAreaCodeNumberPlate('trucks.area_code', 'trucks.number_plate', 'truck_area_code_number_plate'))
            );
    }

    public function oneSkeleton($id)
    {
        return $this->allSkeleton()->where('costs.id', $id);
    }

    public function readByTruckId($truck_id)
    {
        return $this->allSkeleton()
            ->where('invoice_id', 0)
            ->where('truck_id', $truck_id)
            ->get();
    }
}