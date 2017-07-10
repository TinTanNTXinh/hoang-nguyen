<?php

namespace App\Repositories\Eloquent;

use App\Repositories\ProductCodeRepositoryInterface;
use App\ProductCode;

class ProductCodeEloquentRepository extends BaseEloquentRepository implements ProductCodeRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = ProductCode::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function deleteByProductId($product_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('product_id', $product_id)
            ->delete();
    }

    public function deactivateByProductId($product_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('product_id', $product_id)
            ->update(['active' => false]);
    }
}