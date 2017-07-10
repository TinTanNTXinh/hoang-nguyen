<?php

namespace App\Repositories\Eloquent;

use App\Repositories\InvoiceCustomerRepositoryInterface;
use App\Invoice;
use App\Common\Helpers\DBHelper;
use DB;

class InvoiceCustomerEloquentRepository extends BaseEloquentRepository implements InvoiceCustomerRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Invoice::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->where('invoices.active', true)
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
            ->orderBy('invoices.invoice_date', 'desc')
            ->get();
    }

    public function findByPaymentDate($payment_date)
    {
        if (!isset($payment_date))
            $payment_date = date('Y-m-d');

        return $this->getModel()
            ->whereActive(true)
            ->where('invoices.type2', 'like', 'CUSTOMER-%')
            ->where('total_paid', '<', 'after_vat')
            ->whereDate('payment_date', $payment_date)
            ->get();
    }
}