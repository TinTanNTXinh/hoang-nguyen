<?php

namespace App\Repositories\Eloquent;

use App\Repositories\CostOtherRepositoryInterface;
use App\Cost;
use DB;
use App\Common\Helpers\DBHelper;

class CostOtherEloquentRepository extends BaseEloquentRepository implements CostOtherRepositoryInterface
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
            ->where('costs.type', 'OTHER')
            ->leftJoin('trucks', 'trucks.id', '=', 'costs.truck_id')
            ->select('costs.*'
                , DB::raw(DBHelper::getWithCurrencyFormat('costs.after_vat', 'fc_after_vat'))
                , DB::raw(DBHelper::getWithDateTimeFormat('costs.created_date', 'fd_created_date'))
                , DB::raw(DBHelper::getWithAreaCodeNumberPlate('trucks.area_code', 'trucks.number_plate', 'truck_area_code_number_plate'))
            )
            ->get();
    }

    public function findAllActiveByTruckIdAndInvoiceId($truck_id, $invoice_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('type', 'OTHER')
            ->where('invoice_id', $invoice_id)
            ->where('truck_id', $truck_id)
            ->get();
    }
}