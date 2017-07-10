<?php

namespace App\Services;


interface LubeServiceInterface extends BaseServiceInterface
{
    public function readByApplyDate($apply_date);
}