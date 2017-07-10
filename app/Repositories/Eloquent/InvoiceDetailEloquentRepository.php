<?php

namespace App\Repositories\Eloquent;

use App\Repositories\InvoiceDetailRepositoryInterface;
use App\InvoiceDetail;

class InvoiceDetailEloquentRepository extends EloquentBaseRepository implements InvoiceDetailRepositoryInterface
{
    /** ===== INIT MODEL ===== */
    public function setModel()
    {
        return InvoiceDetail::class;
    }

    /** ===== PUBLIC FUNCTION ===== */
    public function allSkeleton()
    {
        return $this->model->whereActive(true);
    }

    public function oneSkeleton($id)
    {
        return $this->allSkeleton()->where('invoice_details.id', $id);
    }

    public function deactivateByInvoiceId($invoice_id)
    {
        return $this->allActiveQuery()
            ->where('invoice_id', $invoice_id)
            ->update(['active' => false]);
    }

    public function deleteByInvoiceId($invoice_id)
    {
        return $this->allActiveQuery()
            ->where('invoice_id', $invoice_id)
            ->delete();
    }
}