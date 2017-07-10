<?php

namespace App\Repositories\Eloquent;

use App\Repositories\CostOtherRepositoryInterface;
use App\Cost;
use DB;
use App\Common\DBHelper;

class CostOtherEloquentRepository extends EloquentBaseRepository implements CostOtherRepositoryInterface
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
            ->where('costs.type', 'OTHER')
            ->leftJoin('trucks', 'trucks.id', '=', 'costs.truck_id')
            ->select('costs.*'
                , DB::raw(DBHelper::getWithCurrencyFormat('costs.after_vat', 'fc_after_vat'))
                , DB::raw(DBHelper::getWithDateTimeFormat('costs.created_date', 'fd_created_date'))
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