<?php

namespace App\Repositories\Eloquent;

use App\Repositories\InvoiceCustomerRepositoryInterface;
use App\Invoice;
use App\Common\DBHelper;
use DB;

class InvoiceCustomerEloquentRepository extends EloquentBaseRepository implements InvoiceCustomerRepositoryInterface
{
    /** ===== INIT MODEL ===== */
    public function setModel()
    {
        return Invoice::class;
    }

    /** ===== PUBLIC FUNCTION ===== */
    public function allSkeleton()
    {
        return $this->allActiveQuery('invoices.active')
            ->leftJoin('customers', 'customers.id', '=', 'invoices.customer_id')
            ->where('invoices.type2', 'like', 'CUSTOMER-%')
            ->select('invoices.*'
                , 'customers.fullname as customer_fullname'
                , DB::raw(DBHelper::getWithCurrencyFormat('invoices.total_revenue', 'fc_total_revenue'))
                , DB::raw(DBHelper::getWithCurrencyFormat('invoices.total_receive', 'fc_total_receive'))
                , DB::raw(DBHelper::getWithCurrencyFormat('invoices.after_vat', 'fc_after_vat'))
                , DB::raw(DBHelper::getWithCurrencyFormat('invoices.total_pay', 'fc_total_pay'))
                , DB::raw(DBHelper::getWithCurrencyFormat('invoices.total_paid', 'fc_total_paid'))
                , DB::raw(DBHelper::getWithDateTimeFormat('invoices.invoice_date', 'fd_invoice_date'))
                , DB::raw(DBHelper::getWithDateTimeFormat('invoices.payment_date', 'fd_payment_date'))
            )
            ->orderBy('invoices.invoice_date', 'desc');
    }

    public function oneSkeleton($id)
    {
        return $this->allSkeleton()->where('invoices.id', $id);
    }

    public function readByInvoiceIds($invoice_ids)
    {
        return $this->allActiveQuery()
            ->whereIn('id', $invoice_ids)
            ->get();
    }

    public function readByPaymentDate($payment_date)
    {
        if (!isset($payment_date))
            $payment_date = date('Y-m-d');

        return $this->allActiveQuery()
            ->where('invoices.type2', 'like', 'CUSTOMER-%')
            ->where('total_paid', '<', 'after_vat')
            ->whereDate('payment_date', $payment_date)
            ->get();
    }
}