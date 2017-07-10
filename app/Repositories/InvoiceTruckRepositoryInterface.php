<?php

namespace App\Repositories;

interface InvoiceTruckRepositoryInterface
{
    public function allSkeleton();

    public function oneSkeleton($id);

    public function readByPaymentDate($payment_date);
}