<?php

namespace App\Services;


interface InvoiceCustomerServiceInterface extends BaseServiceInterface
{
    public function readByCustomerIdAndType2($customer_id, $type2);
    public function computeByTransportIds($transport_ids);
    public function computeByInvoiceId($invoice_id, $validate);
    public function readByPaymentDate($payment_date);
}