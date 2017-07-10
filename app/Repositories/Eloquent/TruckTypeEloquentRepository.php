<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TruckTypeRepositoryInterface;
use App\TruckType;
use DB;
use App\Common\Helpers\DBHelper;

class TruckTypeEloquentRepository extends BaseEloquentRepository implements TruckTypeRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = TruckType::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->where('truck_types.active', true)
            ->select('truck_types.*'
                , DB::raw(DBHelper::getWithTruckTypeNameWeight('truck_types.name', 'truck_types.weight', 'name_weight'))
                , DB::raw(DBHelper::getWithCurrencyFormat('truck_types.unit_price_park', 'fc_unit_price_park'))
            )
            ->get();
    }

    public function findOneActiveByTruckId($truck_id)
    {
        $truck_type = $this->getModel()
            ->where('truck_types.active', true)
            ->leftJoin('trucks', 'trucks.truck_type_id', '=', 'truck_types.id')
            ->where('trucks.id', $truck_id)
            ->select('truck_types.*')
            ->first();
        return $truck_type;
    }
}