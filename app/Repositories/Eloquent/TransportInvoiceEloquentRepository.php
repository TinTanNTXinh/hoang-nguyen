<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TransportInvoiceRepositoryInterface;
use App\TransportInvoice;

class TransportInvoiceEloquentRepository extends EloquentBaseRepository implements TransportInvoiceRepositoryInterface
{
    /** ===== INIT MODEL ===== */
    public function setModel()
    {
        return TransportInvoice::class;
    }

    /** ===== PUBLIC FUNCTION ===== */
    public function allSkeleton()
    {
        return $this->model->whereActive(true);
    }

    public function oneSkeleton($id)
    {
        return $this->allSkeleton()->where('transport_invoices.id', $id);
    }

    public function deleteByInvoiceId($invoice_id)
    {
        return $this->allActiveQuery()
            ->where('invoice_id', $invoice_id)
            ->delete();
    }

    public function deactivateByInvoiceId($invoice_id)
    {
        return $this->allActiveQuery()
            ->where('invoice_id', $invoice_id)
            ->update(['active' => false]);
    }

    public function findAllInvoiceIdByInvoiceId($invoice_id)
    {
        $transport_ids = $this->allActiveQuery()
            ->where('invoice_id', $invoice_id)
            ->pluck('transport_id')
            ->toArray();

        $invoice_ids = $this->allActiveQuery()
            ->whereIn('transport_id', $transport_ids)
            ->pluck('invoice_id')
            ->unique()
            ->toArray();

        return $invoice_ids;
    }

    public function findAllInvoiceIdByTransportIds($transport_ids)
    {
        $invoice_ids = $this->allActiveQuery()
            ->whereIn('transport_id', $transport_ids)
            ->pluck('invoice_id')
            ->unique()
            ->toArray();

        return $invoice_ids;
    }

    public function readByInvoiceId($invoice_id)
    {
        return $this->allActiveQuery()
            ->where('invoice_id', $invoice_id)
            ->get();
    }
}