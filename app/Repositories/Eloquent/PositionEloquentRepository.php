<?php

namespace App\Repositories\Eloquent;

use App\Repositories\PositionRepositoryInterface;
use App\Position;

class PositionEloquentRepository extends BaseEloquentRepository implements PositionRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Position::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}