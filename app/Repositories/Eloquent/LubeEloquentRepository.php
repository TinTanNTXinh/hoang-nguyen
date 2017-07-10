<?php

namespace App\Repositories\Eloquent;

use App\Repositories\LubeRepositoryInterface;
use App\Fuel;
use DB;
use App\Common\Helpers\DBHelper;
use App\Common\Helpers\DateTimeHelper;

class LubeEloquentRepository extends BaseEloquentRepository implements LubeRepositoryInterface
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
            ->where('type', 'LUBE')
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

        $lube = $this->getModel()
            ->whereActive(true)
            ->where('type', 'LUBE')
            ->where('apply_date', '<=', $i_apply_date)
            ->latest('apply_date')
            ->first();

        return $lube;
    }
}