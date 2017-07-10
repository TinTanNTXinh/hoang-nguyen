<?php

namespace App\Repositories;

interface InvoiceDetailRepositoryInterface
{
    public function deactivateByInvoiceId($invoice_id);

    public function deleteByInvoiceId($invoice_id);
}