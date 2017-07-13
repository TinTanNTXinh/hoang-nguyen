<?php

namespace App\Repositories\Eloquent;

use App\Repositories\FuelCustomerRepositoryInterface;
use App\FuelCustomer;

class FuelCustomerEloquentRepository extends BaseEloquentRepository implements FuelCustomerRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = FuelCustomer::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function deleteByCustomerId($customer_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('customer_id', $customer_id)
            ->delete();
    }

    public function deactivateByCustomerId($customer_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('customer_id', $customer_id)
            ->update(['active' => false]);
    }

    public function findOneActiveByCustomerId($customer_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('type', 'OIL')
            ->where('customer_id', $customer_id)
            ->first();
    }
}