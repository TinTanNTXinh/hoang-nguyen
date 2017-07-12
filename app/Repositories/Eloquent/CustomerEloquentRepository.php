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
        return $this->getModel()
            ->where('customers.active', true)
            ->where('fuels.type', 'OIL')
            ->leftJoin('customer_types', 'customer_types.id', '=', 'customers.customer_type_id')
            ->leftJoin('fuel_customers', 'fuel_customers.customer_id', '=', 'customers.id')
            ->leftJoin('fuels', 'fuels.id', '=', 'fuel_customers.fuel_id')
//            ->leftJoin('postages', 'postages.customer_id', '=', 'customers.id')
//            ->leftJoin('formulas', 'formulas.postage_id', '=', 'postages.id')
            ->select('customers.*'
                , 'customer_types.name as customer_type_name'
                , 'fuels.price as fuel_price', 'fuels.apply_date as fuel_apply_date'
                , DB::raw(DBHelper::getWithCurrencyFormat('fuels.price', 'fc_fuel_price'))
                , DB::raw(DBHelper::getWithDateTimeFormat('fuels.apply_date', 'fd_fuel_apply_date'))
                , DB::raw(DBHelper::getWithDateTimeFormat('customers.finish_date', 'fd_finish_date'))
//                , DB::raw('COUNT(postages.id) as quantum_postage')
            )
            ->get();
    }
}