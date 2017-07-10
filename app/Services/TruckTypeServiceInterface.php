<?php

namespace App\Services;


interface TruckTypeServiceInterface extends BaseServiceInterface
{
    public function readByTruckId($truck_id);
}