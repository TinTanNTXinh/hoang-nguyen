<?php

namespace App\Repositories\Eloquent;

use App\Repositories\UserRoleRepositoryInterface;
use App\UserRole;

class UserRoleEloquentRepository extends BaseEloquentRepository implements UserRoleRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = UserRole::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function deleteByUserId($user_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('user_id', $user_id)
            ->delete();
    }

    public function deactivateByUserId($user_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('user_id', $user_id)
            ->update(['active' => false]);
    }
}