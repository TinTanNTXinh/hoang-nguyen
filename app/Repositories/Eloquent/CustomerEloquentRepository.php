<?php

namespace App\Repositories\Eloquent;

use App\Repositories\CustomerRepositoryInterface;
use App\Customer;
use DB;
use App\Common\Helpers\DBHelper;

class CustomerEloquentRepository extends BaseEloquentRepository implements CustomerRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Customer::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()->where('customers.active', true)
            ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.customer_type_id')
//            ->leftJoin('postages', 'postages.customer_id', '=', 'customers.id')
//            ->leftJoin('formulas', 'formulas.postage_id', '=', 'postages.id')
            ->select('customers.*'
                , 'customer_types.name as customer_type_name'
                , DB::raw(DBHelper::getWithDateTimeFormat('customers.finish_date', 'fd_finish_date'))
//                , DB::raw('COUNT(postages.id) as quantum_postage')
            )
            ->get();
    }
}