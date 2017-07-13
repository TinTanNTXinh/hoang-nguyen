<?php

namespace App\Repositories\Eloquent;

use App\Repositories\OilRepositoryInterface;
use App\Fuel;
use DB;
use App\Common\Helpers\DBHelper;
use App\Common\Helpers\DateTimeHelper;

class OilEloquentRepository extends BaseEloquentRepository implements OilRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Fuel::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->where('fuels.active', true)
            ->where('type', 'OIL')
            ->select('fuels.*'
                , DB::raw(DBHelper::getWithCurrencyFormat('fuels.price', 'fc_price'))
                , DB::raw(DBHelper::getWithDateTimeFormat('fuels.apply_date', 'fd_apply_date'))
            )
            ->orderBy('apply_date', 'desc')
            ->get();
    }

    public function findOneActiveByApplyDate($i_apply_date = null)
    {
        if (!isset($i_apply_date))
            $i_apply_date = DateTimeHelper::addTimeForDate(date('Y-m-d'), 'max');

        $oil = $this->getModel()
            ->whereActive(true)
            ->where('type', 'OIL')
            ->where('apply_date', '<=', $i_apply_date)
            ->latest('apply_date')
            ->first();

        return $oil;
    }

    public function findAllActiveByApplyDate($operator, $i_apply_date = null)
    {
        if (!isset($i_apply_date))
            $i_apply_date = DateTimeHelper::addTimeForDate(date('Y-m-d'), 'max');

        $oils = $this->getModel()
            ->whereActive(true)
            ->where('type', 'OIL')
            ->where('apply_date', $operator, $i_apply_date)
            ->orderBy('apply_date', 'asc')
            ->get();

        return $oils;
    }
}