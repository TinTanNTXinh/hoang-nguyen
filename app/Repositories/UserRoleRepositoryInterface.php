<?php

namespace App\Repositories;

interface UserRoleRepositoryInterface
{
    public function deleteByUserId($user_id);

    public function deactivateByUserId($user_id);
}