<?php

namespace App\Repositories;

interface InvoiceCustomerRepositoryInterface
{
    public function findByPaymentDate($payment_date);
}