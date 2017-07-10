<?php

namespace App\Repositories;


interface BaseRepositoryInterface
{
    /**
     * @param $id integer
     * @return \Illuminate\Support\Collection
     */
    public function findOne($id);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function findAll();

    /**
     * @param $ids array
     * @return \Illuminate\Support\Collection
     */
    public function findAllByIds($ids);

    /**
     * @param $field_name string
     * @param $value mixed
     * @param string $operator
     * @return \Illuminate\Support\Collection
     */
    public function findOneByFieldName($field_name, $value, $operator = '=');

    /**
     * @param $field_name string
     * @param $value mixed
     * @param string $operator
     * @return \Illuminate\Support\Collection
     */
    public function findAllByFieldName($field_name, $value, $operator = '=');

    // Active

    /**
     * @param $id integer
     * @return \Illuminate\Support\Collection
     */
    public function findOneActive($id);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function findAllActive();

    /**
     * @param $ids array
     * @return \Illuminate\Support\Collection
     */
    public function findAllActiveByIds($ids);

    /**
     * @param $field_name string
     * @param $value mixed
     * @param string $operator
     * @return \Illuminate\Support\Collection
     */
    public function findOneActiveByFieldName($field_name, $value, $operator = '=');

    /**
     * @param $field_name string
     * @param $value mixed
     * @param string $operator
     * @return \Illuminate\Support\Collection
     */
    public function findAllActiveByFieldName($field_name, $value, $operator = '=');

    /**
     * @param $data array
     * @return \Illuminate\Support\Collection
     */
    public function createOne($data);

    /**
     * @param $id integer
     * @param $data array
     * @return \Illuminate\Support\Collection
     */
    public function updateOne($id, $data);

    /**
     * @param $data array
     * @return \Illuminate\Support\Collection
     */
    public function saveOne($data);

    /**
     * @param $id integer
     * @return boolean
     */
    public function deactivateOne($id);

    /**
     * @return integer
     */
    public function deactivateAll();

    /**
     * @param $ids array
     * @return integer
     */
    public function deactivateAllByIds($ids);

    /**
     * @param $id integer
     * @return integer
     */
    public function destroyOne($id);

    /**
     * @return integer
     */
    public function destroyAll();

    /**
     * @param array $ids
     * @return integer
     */
    public function destroyAllByIds($ids);

    /**
     * @return integer
     */
    public function countAll();

    /**
     * @return integer
     */
    public function countAllActive();

    /**
     * @param $prefix string
     * @return string
     */
    public function generateCode($prefix);

    /**
     * @param $field_name string
     * @param $value mixed
     * @param array $skip_id
     * @return boolean
     */
    public function existsValue($field_name, $value, $skip_id = []);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function findAllSkeleton();

    /**
     * @param $id integer
     * @return \Illuminate\Support\Collection
     */
    public function findOneSkeleton($id);

    /**
     * @return \Illuminate\Support\Collection
     */
    public function findAllActiveSkeleton();

    /**
     * @param $id integer
     * @return \Illuminate\Support\Collection
     */
    public function findOneActiveSkeleton($id);
}