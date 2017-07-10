<?php

namespace App\Repositories;

interface UserPositionRepositoryInterface
{
    public function deleteByUserId($user_id);

    public function deactivateByUserId($user_id);
}