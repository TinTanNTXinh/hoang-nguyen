<?php

namespace App\Repositories;

interface InvoiceDetailRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function deactivateByInvoiceId($invoice_id);

    public function deleteByInvoiceId($invoice_id);
}