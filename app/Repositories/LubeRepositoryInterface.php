<?php

namespace App\Repositories;

interface LubeRepositoryInterface
{
    public function findOneActiveByApplyDate($i_apply_date = null);
}