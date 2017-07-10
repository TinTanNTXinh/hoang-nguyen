<?php

namespace App\Services;


interface InvoiceCustomerServiceInterface
{
    public function readAll();
    public function readOne($id);
    public function createOne($data);
    public function updateOne($data);
    public function deactivateOne($id);
    public function deleteOne($id);
    public function searchOne($filter);

    public function validateUpdateOne($id);
    public function validateDeactivateOne($id);
    public function validateDeleteOne($id);

    public function readByCustomerIdAndType2($customer_id, $type2);
    public function computeByTransportIds($transport_ids);
    public function computeByInvoiceId($invoice_id, $validate);
    public function readByPaymentDate($payment_date);
}