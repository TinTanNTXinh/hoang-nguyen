<?php

namespace App\Repositories\Eloquent;

use App\Repositories\GarageTypeRepositoryInterface;
use App\GarageType;

class GarageTypeEloquentRepository extends BaseEloquentRepository implements GarageTypeRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = GarageType::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}