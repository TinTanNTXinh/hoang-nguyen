<?php

namespace App\Repositories;

interface PostageRepositoryInterface
{
    public function findByCustomerIdAndTransportDate($customer_id, $transport_date = null);

}