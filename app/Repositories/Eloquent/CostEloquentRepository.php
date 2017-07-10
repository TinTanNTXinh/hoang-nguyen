<?php

namespace App\Repositories\Eloquent;

use App\Repositories\CostRepositoryInterface;
use App\Cost;
use DB;
use App\Common\Helpers\DBHelper;

class CostEloquentRepository extends BaseEloquentRepository implements CostRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Cost::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->where('costs.active', true)
            ->leftJoin('fuels', 'fuels.id', '=', 'costs.fuel_id')
            ->leftJoin('trucks', 'trucks.id', '=', 'costs.truck_id')
            ->select('costs.*'
                , 'fuels.price as fuel_price'
                , DB::raw(DBHelper::getWithCurrencyFormat('fuels.price', 'fc_fuel_price'))
                , DB::raw(DBHelper::getWithCurrencyFormat('costs.after_vat', 'fc_after_vat'))
                , DB::raw(DBHelper::getWithDateTimeFormat('costs.refuel_date', 'fd_refuel_date'))
                , DB::raw(DBHelper::getWithAreaCodeNumberPlate('trucks.area_code', 'trucks.number_plate', 'truck_area_code_number_plate'))
            )
            ->get();
    }

    public function updateInvoiceIdByTruckId($truck_id, $invoice_id)
    {
        return $this->getModel()
            ->where('costs.active', true)
            ->where('truck_id', $truck_id)
            ->update(['invoice_id' => $invoice_id]);
    }

    public function findAllActiveByTruckIdAndInvoiceId($truck_id, $invoice_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('invoice_id', $invoice_id)
            ->where('truck_id', $truck_id)
            ->get();
    }
}