<?php

namespace App\Repositories;

interface FuelCustomerRepositoryInterface
{
    public function deleteByCustomerId($customer_id);

    public function deactivateByCustomerId($customer_id);
}