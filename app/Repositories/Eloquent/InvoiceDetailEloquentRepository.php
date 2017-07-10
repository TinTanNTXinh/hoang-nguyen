<?php

namespace App\Repositories\Eloquent;

use App\Repositories\InvoiceDetailRepositoryInterface;
use App\InvoiceDetail;

class InvoiceDetailEloquentRepository extends BaseEloquentRepository implements InvoiceDetailRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = InvoiceDetail::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function deactivateByInvoiceId($invoice_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('invoice_id', $invoice_id)
            ->update(['active' => false]);
    }

    public function deleteByInvoiceId($invoice_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('invoice_id', $invoice_id)
            ->delete();
    }
}