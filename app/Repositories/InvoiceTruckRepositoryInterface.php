<?php

namespace App\Repositories;

interface InvoiceTruckRepositoryInterface
{
    public function findByPaymentDate($payment_date);
}