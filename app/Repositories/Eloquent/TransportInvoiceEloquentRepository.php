<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TransportInvoiceRepositoryInterface;
use App\TransportInvoice;

class TransportInvoiceEloquentRepository extends BaseEloquentRepository implements TransportInvoiceRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = TransportInvoice::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function deleteByInvoiceId($invoice_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('invoice_id', $invoice_id)
            ->delete();
    }

    public function deactivateByInvoiceId($invoice_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('invoice_id', $invoice_id)
            ->update(['active' => false]);
    }

    public function findAllInvoiceIdByInvoiceId($invoice_id)
    {
        $transport_ids = $this->getModel()->whereActive(true)
            ->where('invoice_id', $invoice_id)
            ->pluck('transport_id')
            ->toArray();

        $invoice_ids = $this->getModel()
            ->whereActive(true)
            ->whereIn('transport_id', $transport_ids)
            ->pluck('invoice_id')
            ->unique()
            ->toArray();

        return $invoice_ids;
    }

    public function findAllInvoiceIdByTransportIds($transport_ids)
    {
        $invoice_ids = $this->getModel()
            ->whereActive(true)
            ->whereIn('transport_id', $transport_ids)
            ->pluck('invoice_id')
            ->unique()
            ->toArray();

        return $invoice_ids;
    }
}