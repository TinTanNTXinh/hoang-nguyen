<?php

namespace App\Repositories;

interface TransportRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function readByIds($ids);

    public function updateType2ByIds($ids, $type2);

    public function updateType3ByIds($ids, $type3);

    public function readByPostageId($postage_id);

    // Customer
    public function readByCustomerIdAndType2($customer_id, $type2);

    // Truck
    public function readByTruckIdAndType3($truck_id, $type3);

}