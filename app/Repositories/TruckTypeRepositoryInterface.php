<?php

namespace App\Repositories;

interface TruckTypeRepositoryInterface
{
    public function findOneActiveByTruckId($truck_id);
}