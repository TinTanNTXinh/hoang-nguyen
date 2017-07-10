<?php

namespace App\Repositories;

interface DriverTruckRepositoryInterface
{
    public function deactivateByDriverId($driver_id);

    public function deactivateByTruckId($truck_id);
}