<?php

namespace App\Services;


interface CostOilServiceInterface
{
    public function readAll();
    public function readOne($id);
    public function createOne($data);
    public function updateOne($data);
    public function deactivateOne($id);
    public function deleteOne($id);
    public function searchOne($filter);

    public function validateUpdateOne($id);
    public function validateDeactivateOne($id);
    public function validateDeleteOne($id);
}