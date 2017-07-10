<?php

namespace App\Repositories;

interface CostOilRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function readByTruckId($truck_id);
}