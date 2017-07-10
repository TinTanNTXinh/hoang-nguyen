<?php

namespace App\Repositories\Eloquent;

use App\Repositories\GarageRepositoryInterface;
use App\Garage;

class GarageEloquentRepository extends BaseEloquentRepository implements GarageRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Garage::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->where('garages.active', true)
            ->leftJoin('garage_types', 'garage_types.id', '=', 'garages.garage_type_id')
            ->select('garages.*'
                , 'garage_types.name as garage_type_name'
            )
            ->get();
    }
}