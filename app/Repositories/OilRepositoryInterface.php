<?php

namespace App\Repositories;

interface OilRepositoryInterface
{
    /**
     * @param null|string $i_apply_date
     * @return \App\Fuel
     */
    public function findOneActiveByApplyDate($i_apply_date = null);

    /**
     * @param $operator string
     * @param null $i_apply_date
     * @return \App\Fuel[]
     */
    public function findAllActiveByApplyDate($operator, $i_apply_date = null);
}