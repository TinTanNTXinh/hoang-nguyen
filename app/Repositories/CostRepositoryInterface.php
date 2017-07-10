<?php

namespace App\Repositories;

interface CostRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function readByTruckId($truck_id);

    public function updateInvoiceIdByTruckId($truck_id, $invoice_id);
}