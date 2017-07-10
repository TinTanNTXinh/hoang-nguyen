<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TruckRepositoryInterface;
use App\Truck;
use DB;
use App\Common\Helpers\DBHelper;

class TruckEloquentRepository extends BaseEloquentRepository implements TruckRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Truck::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->where('trucks.active', true)
            ->leftJoin('garages', 'garages.id', '=', 'trucks.garage_id')
            ->leftJoin('truck_types', 'truck_types.id', '=', 'trucks.truck_type_id')
            ->select('trucks.*'
                , 'truck_types.name as truck_type_name'
                , 'truck_types.weight as truck_type_weight'
                , 'garages.name as garage_name'
                , DB::raw(DBHelper::getWithAreaCodeNumberPlate('trucks.area_code', 'trucks.number_plate', 'area_code_number_plate'))
            )
            ->get();
    }

    public function existsAreaCodeNumberPlate($area_code, $number_plate, $skip_id = [])
    {
        // Check luôn cả dữ liệu đã deactivate [whereActive(true)]
        $exists = $this->getModel()
            ->where('area_code', $area_code)
            ->where('number_plate', $number_plate)
            ->whereNotIn('id', $skip_id)
            ->count();
        return ($exists > 0); // boolean
    }
}