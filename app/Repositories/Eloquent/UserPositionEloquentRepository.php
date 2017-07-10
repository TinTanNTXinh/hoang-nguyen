<?php

namespace App\Repositories\Eloquent;

use App\Repositories\UserPositionRepositoryInterface;
use App\UserPosition;

class UserPositionEloquentRepository extends BaseEloquentRepository implements UserPositionRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = UserPosition::class;
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