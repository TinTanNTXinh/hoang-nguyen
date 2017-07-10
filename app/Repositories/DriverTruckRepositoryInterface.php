<?php

namespace App\Repositories;

interface DriverTruckRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function deactiveByDriverId($driver_id);

    public function deactiveByTruckId($truck_id);
}