<?php

namespace App\Repositories\Eloquent;

use App\Repositories\CostParkingRepositoryInterface;
use App\Cost;
use DB;
use App\Common\Helpers\DBHelper;

class CostParkingEloquentRepository extends BaseEloquentRepository implements CostParkingRepositoryInterface
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
            ->where('costs.type', 'PARKING')
            ->leftJoin('trucks', 'trucks.id', '=', 'costs.truck_id')
            ->leftJoin('truck_types', 'truck_types.id', '=', 'trucks.truck_type_id')
            ->select('costs.*'
                , 'truck_types.id as truck_type_id', 'truck_types.name as truck_type_name', 'truck_types.weight as truck_type_weight'
                , 'truck_types.unit_price_park as truck_type_unit_price_park'
                , DB::raw(DBHelper::getWithCurrencyFormat('truck_types.unit_price_park', 'fc_truck_type_unit_price_park'))
                , DB::raw(DBHelper::getWithCurrencyFormat('costs.after_vat', 'fc_after_vat'))
                , DB::raw(DBHelper::getWithDateTimeFormat('costs.checkin_date', 'fd_checkin_date'))
                , DB::raw(DBHelper::getWithDateTimeFormat('costs.checkout_date', 'fd_checkout_date'))
                , DB::raw(DBHelper::getWithAreaCodeNumberPlate('trucks.area_code', 'trucks.number_plate', 'truck_area_code_number_plate'))
            )
            ->get();
    }

    public function findAllActiveByTruckIdAndInvoiceId($truck_id, $invoice_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('type', 'PARKING')
            ->where('invoice_id', $invoice_id)
            ->where('truck_id', $truck_id)
            ->get();
    }
}