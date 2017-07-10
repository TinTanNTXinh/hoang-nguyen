<?php

namespace App\Services;


interface VoucherServiceInterface
{
    public function readAll();
    public function readOne($id);
    public function createOne($data);
    public function updateOne($data);
    public function deactivateOne($id);
    public function deleteOne($id);
    public function searchOne($filter);
}