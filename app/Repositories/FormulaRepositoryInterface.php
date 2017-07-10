<?php

namespace App\Repositories;

interface FormulaRepositoryInterface
{
    public function deleteByPostageId($postage_id);

    public function findPostageIdByFormulas($i_formulas, $i_customer_id, $i_transport_date = null);
}