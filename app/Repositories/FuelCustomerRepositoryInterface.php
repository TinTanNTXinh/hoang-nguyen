<?php

namespace App\Repositories;

interface FuelCustomerRepositoryInterface
{
    public function deleteByCustomerId($customer_id);

    public function deactivateByCustomerId($customer_id);

    /**
     * @param $customer_id integer
     * @return \App\FuelCustomer
     */
    public function findOneActiveByCustomerId($customer_id);
}