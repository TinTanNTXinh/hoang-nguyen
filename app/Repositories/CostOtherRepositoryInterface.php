<?php

namespace App\Repositories;

interface CostOtherRepositoryInterface
{
    public function findAllActiveByTruckIdAndInvoiceId($truck_id, $invoice_id);
}