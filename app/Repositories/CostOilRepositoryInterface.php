<?php

namespace App\Repositories;

interface CostOilRepositoryInterface
{
    public function findAllActiveByTruckIdAndInvoiceId($truck_id, $invoice_id);
}