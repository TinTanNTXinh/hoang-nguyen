<?php

namespace App\Repositories\Eloquent;

use App\Repositories\ProductCodeRepositoryInterface;
use App\ProductCode;

class ProductCodeEloquentRepository extends EloquentBaseRepository implements ProductCodeRepositoryInterface
{
    /** ===== INIT MODEL ===== */
    public function setModel()
    {
        return ProductCode::class;
    }

    /** ===== PUBLIC FUNCTION ===== */
    public function allSkeleton()
    {
        return $this->model->whereActive(true);
    }

    public function oneSkeleton($id)
    {
        return $this->allSkeleton()->where('product_codes.id', $id);
    }

    public function readByProductId($product_id)
    {
        return $this->allActiveQuery()
            ->where('product_id', $product_id)
            ->get();
    }

    public function deleteByProductId($product_id)
    {
        return $this->allActiveQuery()
            ->where('product_id', $product_id)
            ->delete();
    }

    public function deactivateByProductId($product_id)
    {
        return $this->allActiveQuery()
            ->where('product_id', $product_id)
            ->update(['active' => false]);
    }
}