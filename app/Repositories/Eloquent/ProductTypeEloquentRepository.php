<?php

namespace App\Repositories\Eloquent;

use App\Repositories\ProductTypeRepositoryInterface;
use App\ProductType;

class ProductTypeEloquentRepository extends BaseEloquentRepository implements ProductTypeRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = ProductType::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}