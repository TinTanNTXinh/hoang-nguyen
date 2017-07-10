<?php

namespace App\Services;


interface InvoiceTruckServiceInterface
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

    public function readByTruckIdAndType3($truck_id, $type3);
    public function computeByTransportIds($transport_ids);
    public function updateCostInTransport($data);
    public function readTransportsByTruckId($truck_id);
    public function readByPaymentDate($payment_date);
}