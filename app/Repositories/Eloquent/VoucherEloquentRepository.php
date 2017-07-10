<?php

namespace App\Repositories\Eloquent;

use App\Repositories\VoucherRepositoryInterface;
use App\Voucher;

class VoucherEloquentRepository extends BaseEloquentRepository implements VoucherRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Voucher::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}