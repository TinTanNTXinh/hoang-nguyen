<?php

namespace App\Repositories;

interface TransportInvoiceRepositoryInterface
{
    public function deleteByInvoiceId($invoice_id);

    public function deactivateByInvoiceId($invoice_id);

    public function findAllInvoiceIdByInvoiceId($invoice_id);

    public function findAllInvoiceIdByTransportIds($transport_ids);
}