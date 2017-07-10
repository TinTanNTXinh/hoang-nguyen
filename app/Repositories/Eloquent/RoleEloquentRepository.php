<?php

namespace App\Repositories\Eloquent;

use App\Repositories\RoleRepositoryInterface;
use App\Role;

class RoleEloquentRepository extends BaseEloquentRepository implements RoleRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = Role::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
}