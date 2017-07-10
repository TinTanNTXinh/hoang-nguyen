<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TransportVoucherRepositoryInterface;
use App\TransportVoucher;

class TransportVoucherEloquentRepository extends BaseEloquentRepository implements TransportVoucherRepositoryInterface
{

    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = TransportVoucher::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function deleteByTransportId($transport_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('transport_id', $transport_id)
            ->delete();
//        $ids = $this->readByTransportId($transport_id)->pluck('id')->toArray();
//        return $this->getModel()->destroy($ids);
    }

    public function deactivateByTransportId($transport_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('transport_id', $transport_id)
            ->update(['active' => false]);
    }
}