<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TruckTypeRepositoryInterface;
use App\TruckType;
use DB;
use App\Common\DBHelper;

class TruckTypeEloquentRepository extends EloquentBaseRepository implements TruckTypeRepositoryInterface
{
    /** ===== INIT MODEL ===== */
    public function setModel()
    {
        return TruckType::class;
    }

    /** ===== PUBLIC FUNCTION ===== */
    public function allSkeleton()
    {
        return $this->allActiveQuery()
            ->select('truck_types.*'
                , DB::raw(DBHelper::getWithTruckTypeNameWeight('truck_types.name', 'truck_types.weight', 'name_weight'))
                , DB::raw(DBHelper::getWithCurrencyFormat('truck_types.unit_price_park', 'fc_unit_price_park'))
            );
    }

    public function oneSkeleton($id)
    {
        return $this->allSkeleton()->where('truck_types.id', $id);
    }

    public function findByTruckId($truck_id)
    {
        $truck_type = $this->allActiveQuery('truck_types.active')
            ->leftJoin('trucks', 'trucks.truck_type_id', '=', 'truck_types.id')
            ->where('trucks.id', $truck_id)
            ->select('truck_types.*')
            ->first();
        return $truck_type;
    }
}