<?php

namespace App\Services;


interface BaseServiceInterface
{
    /**
     * @return array
     */
    public function readAll();

    /**
     * @param $id integer
     * @return array
     */
    public function readOne($id);

    /**
     * @param $data array
     * @return array
     */
    public function createOne($data);

    /**
     * @param $data array
     * @return array
     */
    public function updateOne($data);

    /**
     * @param $id integer
     * @return array
     */
    public function deactivateOne($id);

    /**
     * @param $id integer
     * @return array
     */
    public function deleteOne($id);

    /**
     * @param $filter array
     * @return array
     */
    public function searchOne($filter);

    /**
     * @param $data array
     * @return array
     */
    public function validateInput($data);

    /**
     * @param $data array
     * @return boolean
     */
    public function validateEmpty($data);

    /**
     * @param $data array
     * @return array
     */
    public function validateLogic($data);

    /**
     * @param $id integer
     * @return array
     */
    public function validateUpdateOne($id);

    /**
     * @param $id integer
     * @return array
     */
    public function validateDeactivateOne($id);

    /**
     * @param $id integer
     * @return array
     */
    public function validateDeleteOne($id);
}