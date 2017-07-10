<?php

namespace App\Repositories\Eloquent;

use App\Repositories\CustomerTypeRepositoryInterface;
use App\CustomerType;

class CustomerTypeEloquentRepository extends BaseEloquentRepository implements CustomerTypeRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = CustomerType::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}