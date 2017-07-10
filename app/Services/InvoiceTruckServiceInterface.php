<?php

namespace App\Services;


interface InvoiceTruckServiceInterface extends BaseServiceInterface
{
    public function readByTruckIdAndType3($truck_id, $type3);
    public function computeByTransportIds($transport_ids);
    public function updateCostInTransport($data);
    public function readTransportsByTruckId($truck_id);
    public function readByPaymentDate($payment_date);
}