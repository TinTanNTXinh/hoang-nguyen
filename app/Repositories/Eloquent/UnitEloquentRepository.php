<?php

namespace App\Repositories\Eloquent;

use App\Repositories\UnitRepositoryInterface;
use App\Unit;

class UnitEloquentRepository extends BaseEloquentRepository implements UnitRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Unit::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}