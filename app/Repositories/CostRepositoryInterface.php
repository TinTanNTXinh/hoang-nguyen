<?php

namespace App\Repositories;

interface CostRepositoryInterface
{
    public function updateInvoiceIdByTruckId($truck_id, $invoice_id);

    public function findAllActiveByTruckIdAndInvoiceId($truck_id, $invoice_id);
}