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
}