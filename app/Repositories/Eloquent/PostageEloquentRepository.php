<?php

namespace App\Repositories\Eloquent;

use App\Repositories\PostageRepositoryInterface;
use App\Postage;
use DB;
use App\Common\Helpers\DBHelper;
use App\Common\Helpers\DateTimeHelper;

class PostageEloquentRepository extends BaseEloquentRepository implements PostageRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Postage::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->where('postages.active', true)
            ->where('postages.type', 'OIL')
            ->leftJoin('units', 'units.id', '=', 'postages.unit_id')
            ->leftJoin('fuels', 'fuels.id', '=', 'postages.fuel_id')
            ->select('postages.*'
                , 'units.name as unit_name'
                , 'fuels.price as fuel_price'
                , DB::raw(DBHelper::getWithCurrencyFormat('postages.unit_price', 'fc_unit_price'))
                , DB::raw(DBHelper::getWithDateTimeFormat('postages.apply_date', 'fd_apply_date'))
                , DB::raw(DBHelper::getWithCurrencyFormat('fuels.price', 'fc_fuel_price'))
            )
            ->orderBy('postages.apply_date', 'desc')
            ->get();
    }

    public function findByCustomerIdAndTransportDate($customer_id, $transport_date = null)
    {
        if (!isset($transport_date))
            $transport_date = DateTimeHelper::addTimeForDate(date('Y-m-d'), 'max');

        $postage = $this->getModel()
            ->whereActive(true)
            ->where('type', 'OIL')
            ->where('customer_id', $customer_id)
            ->where('apply_date', '<=', $transport_date)
            ->orderBy('apply_date', 'desc')
            ->first();

        return $postage;
    }
}