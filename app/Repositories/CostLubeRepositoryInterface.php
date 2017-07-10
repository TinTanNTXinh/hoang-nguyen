<?php

namespace App\Repositories;

interface CostLubeRepositoryInterface
{
    public function findAllActiveByTruckIdAndInvoiceId($truck_id, $invoice_id);
}