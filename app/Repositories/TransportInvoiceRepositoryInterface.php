<?php

namespace App\Repositories;

interface TransportInvoiceRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function deleteByInvoiceId($invoice_id);

    public function deactivateByInvoiceId($invoice_id);

    public function findAllInvoiceIdByInvoiceId($invoice_id);

    public function findAllInvoiceIdByTransportIds($transport_ids);

    public function readByInvoiceId($invoice_id);
}