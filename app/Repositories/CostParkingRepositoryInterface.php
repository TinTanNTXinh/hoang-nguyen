<?php

namespace App\Repositories;

interface CostParkingRepositoryInterface
{
    public function findAllActiveByTruckIdAndInvoiceId($truck_id, $invoice_id);
}