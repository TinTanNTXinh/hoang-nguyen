<?php

namespace App\Repositories\Eloquent;

use App\Repositories\InvoiceTruckRepositoryInterface;
use App\Invoice;
use App\Common\Helpers\DBHelper;
use DB;

class InvoiceTruckEloquentRepository extends BaseEloquentRepository implements InvoiceTruckRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Invoice::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()->
        where('invoices.active', true)
            ->leftJoin('trucks', 'trucks.id', '=', 'invoices.truck_id')
            ->where('invoices.type3', 'like', 'TRUCK-%')
            ->select('invoices.*'
                , DB::raw(DBHelper::getWithAreaCodeNumberPlate('trucks.area_code', 'trucks.number_plate', 'truck_area_number_plate'))
                , DB::raw(DBHelper::getWithCurrencyFormat('invoices.total_delivery', 'fc_total_delivery'))
                , DB::raw(DBHelper::getWithCurrencyFormat('invoices.total_cost', 'fc_total_cost'))
                , DB::raw(DBHelper::getWithCurrencyFormat('invoices.total_cost_in_transport', 'fc_total_cost_in_transport'))
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
            ->where('invoices.type3', 'like', 'TRUCK-%')
            ->where('total_paid', '<', 'after_vat')
            ->whereDate('payment_date', $payment_date)
            ->get();
    }
}