<?php

namespace App\Repositories\Eloquent;

use App\Repositories\StaffCustomerRepositoryInterface;
use App\StaffCustomer;

class StaffCustomerEloquentRepository extends BaseEloquentRepository implements StaffCustomerRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = StaffCustomer::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->where('staff_customers.active', true)
            ->leftJoin('customers', 'customers.id', '=', 'staff_customers.customer_id')
            ->select('staff_customers.*'
                , 'customers.fullname as customer_fullname'
            )
            ->get();
    }
}