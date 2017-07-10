<?php

namespace App\Services;


interface UserServiceInterface extends BaseServiceInterface
{
    public function changePassword($data);
}