<?php

namespace App\Repositories\Eloquent;

use App\Repositories\FieldRepositoryInterface;
use App\Field;

class FieldEloquentRepository extends BaseEloquentRepository implements FieldRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Field::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}