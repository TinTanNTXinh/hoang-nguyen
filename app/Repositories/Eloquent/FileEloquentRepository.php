<?php

namespace App\Repositories\Eloquent;

use App\Repositories\FileRepositoryInterface;
use App\File;

class FileEloquentRepository extends BaseEloquentRepository implements FileRepositoryInterface
{
    /* ===== INIT MODEL ===== */
    protected function setModel()
    {
        $this->model = File::class;
    }

    /* ===== PUBLIC FUNCTION ===== */
    public function findAllActiveByTableNameAndTableId($table_name, $table_id)
    {
        return $this->getModel()
            ->whereActive(true)
            ->where('table_name', $table_name)
            ->where('table_id', $table_id)
            ->get();
    }

}