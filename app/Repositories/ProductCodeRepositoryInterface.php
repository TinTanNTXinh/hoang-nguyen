<?php

namespace App\Repositories;

interface ProductCodeRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function readByProductId($product_id);

    public function deleteByProductId($product_id);

    public function deactivateByProductId($product_id);
}