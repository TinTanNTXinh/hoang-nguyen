<?php

namespace App\Repositories;

interface OilRepositoryInterface
{
    /**
     * @param null|string $i_apply_date
     * @return \App\Fuel
     */
    public function findOneActiveByApplyDate($i_apply_date = null);
}