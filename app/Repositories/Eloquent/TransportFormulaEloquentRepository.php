<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TransportFormulaRepositoryInterface;
use App\TransportFormula;

class TransportFormulaEloquentRepository extends BaseEloquentRepository implements TransportFormulaRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = TransportFormula::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function deleteByTransportId($transport_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('transport_id', $transport_id)
            ->delete();
    }

    public function deactivateByTransportId($transport_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('transport_id', $transport_id)
            ->update(['active' => false]);
    }
}