<?php

namespace App\Repositories;

interface TransportRepositoryInterface
{
    public function updateType2ByIds($ids, $type2);

    public function updateType3ByIds($ids, $type3);

    // Customer
    public function findByCustomerIdAndType2($customer_id, $type2);

    // Truck
    public function findByTruckIdAndType3($truck_id, $type3);

}