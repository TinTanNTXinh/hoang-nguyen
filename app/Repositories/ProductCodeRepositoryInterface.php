<?php

namespace App\Repositories;

interface ProductCodeRepositoryInterface
{
    public function deleteByProductId($product_id);

    public function deactivateByProductId($product_id);
}