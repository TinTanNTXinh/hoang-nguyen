<?php

namespace App\Repositories;

interface InvoiceCustomerRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function readByInvoiceIds($invoice_ids);

    public function readByPaymentDate($payment_date);
}