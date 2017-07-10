<?php

namespace App\Repositories;

interface OilRepositoryInterface
{
    public function findOneActiveByApplyDate($i_apply_date = null);
}