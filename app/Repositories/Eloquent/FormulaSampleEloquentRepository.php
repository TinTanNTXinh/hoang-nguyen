<?php

namespace App\Repositories\Eloquent;

use App\Repositories\FormulaSampleRepositoryInterface;
use App\FormulaSample;

class FormulaSampleEloquentRepository extends BaseEloquentRepository implements FormulaSampleRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = FormulaSample::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->whereActive(true)
            ->select('*'
                , \DB::raw("(CASE WHEN rule = 'OIL' THEN 'Dầu'
                 WHEN rule = 'SINGLE' THEN 'Giá trị'
                 WHEN rule = 'RANGE' THEN 'Khoảng'
                 WHEN rule = 'PAIR' THEN 'Cặp'
                    ELSE '' END) as rule_name")
            )
            ->get();
    }

}