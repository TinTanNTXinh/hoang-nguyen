<?php

namespace App\Repositories\Eloquent;

use App\Repositories\UserRepositoryInterface;
use App\User;

class UserEloquentRepository extends BaseEloquentRepository implements UserRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = User::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveSkeleton()
    {
        return $this->getModel()
            ->whereActive(true)
            ->whereNotIn('id', [1, 2])
            ->get();
    }


    public function findOneActiveSkeleton($id)
    {
        return $this->getModel()
            ->where('users.active', true)
            ->where('users.id', $id)
            ->leftJoin('files', 'files.table_id', '=', 'users.id')
            ->select('users.*'
                , 'files.path as file_path'
            )
            ->first();
    }

}