<?php

namespace App\Services;


interface OilServiceInterface extends BaseServiceInterface
{
    public function readByApplyDate($apply_date);
}