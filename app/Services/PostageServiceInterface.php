<?php

namespace App\Services;


interface PostageServiceInterface extends BaseServiceInterface
{
    public function readByCustomerId($customer_id);
    public function sortRule($i_formulas);
}