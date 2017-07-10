<?php

namespace App\Services;


interface TransportServiceInterface extends BaseServiceInterface
{
    public function readFormulas($data);
    public function readPostage($data);
}