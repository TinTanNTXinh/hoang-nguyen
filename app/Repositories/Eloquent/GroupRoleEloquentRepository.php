<?php

namespace App\Repositories\Eloquent;

use App\Repositories\GroupRoleRepositoryInterface;
use App\GroupRole;

class GroupRoleEloquentRepository extends BaseEloquentRepository implements GroupRoleRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = GroupRole::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}