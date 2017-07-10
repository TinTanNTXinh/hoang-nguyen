<?php

namespace App\Repositories\Eloquent;

use App\Repositories\ProductRepositoryInterface;
use App\Product;

class ProductEloquentRepository extends BaseEloquentRepository implements ProductRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Product::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}