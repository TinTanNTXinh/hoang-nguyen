<?php

namespace App\Repositories;

interface TruckRepositoryInterface
{
    /**
     * @param $area_code string
     * @param $number_plate string
     * @param $skip_id integer[]
     * @return boolean
     */
    public function existsAreaCodeNumberPlate($area_code, $number_plate, $skip_id = []);
}