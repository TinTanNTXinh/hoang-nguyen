<?php

namespace App\Repositories;

interface CostParkingRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function readByTruckId($truck_id);
}