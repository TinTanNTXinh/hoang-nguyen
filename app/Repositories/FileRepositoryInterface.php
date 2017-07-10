<?php

namespace App\Repositories;

interface FileRepositoryInterface
{
    public function findAllActiveByTableNameAndTableId($table_name, $table_id);
}