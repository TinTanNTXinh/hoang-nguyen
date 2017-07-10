<?php

namespace App\Repositories;

interface TruckTypeRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function findByTruckId($truck_id);
}