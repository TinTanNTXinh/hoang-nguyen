<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepositoryInterface;

abstract class BaseEloquentRepository implements BaseRepositoryInterface
{
    protected $model;

    /**
     * @return void
     */
    abstract protected function setModel();

    /**
     * @return mixed
     */
    protected function getModel()
    {
        $this->setModel();
        return app()->make($this->model);
    }

    public function findOne($id)
    {
        return $this->getModel()->find($id);
    }

    public function findAll()
    {
        return $this->getModel()->all();
    }

    public function findAllByIds($ids)
    {
        return $this->getModel()->whereIn('id', $ids)
            ->get();
    }

    public function findOneByFieldName($field_name, $value, $operator = '=')
    {
        return $this->getModel()
            ->where($field_name, $operator, $value)
            ->first();
    }

    public function findAllByFieldName($field_name, $value, $operator = '=')
    {
        return $this->getModel()
            ->where($field_name, $operator, $value)
            ->get();
    }

    public function findOneActive($id)
    {
        return $this->getModel()->whereActive(true)
            ->where('id', $id)
            ->first();
    }

    public function findAllActive()
    {
        return $this->getModel()->whereActive(true)
            ->get();
    }

    public function findAllActiveByIds($ids)
    {
        return $this->getModel()->whereActive(true)
            ->whereIn('id', $ids)
            ->get();
    }

    public function findOneActiveByFieldName($field_name, $value, $operator = '=')
    {
        return $this->getModel()->whereActive(true)
            ->where($field_name, $operator, $value)
            ->first();
    }

    public function findAllActiveByFieldName($field_name, $value, $operator = '=')
    {
        return $this->getModel()->whereActive(true)
            ->where($field_name, $operator, $value)
            ->get();
    }

    public function createOne($data)
    {
        return $this->getModel()->create($data); // object
    }

    public function updateOne($id, $data)
    {
        $isSuccess = $this->getModel()->where('id', $id)->update($data); // number
        if (!$isSuccess)
            return null;
        return $this->getModel()->find($id); // object
    }

    public function saveOne($data)
    {
        return $this->getModel()->save($data); // object
    }

    public function deactivateOne($id)
    {
        return $this->getModel()->find($id)->update(['active' => false]); // boolean
    }

    public function deactivateAll()
    {
        return $this->getModel()->whereActive(true)->update(['active' => false]); // number
    }

    public function deactivateAllByIds($ids)
    {
        return $this->getModel()->whereIn('id', $ids)->update(['active' => false]); // number
    }

    public function destroyOne($id)
    {
        return $this->getModel()->destroy($id); // number
    }

    public function destroyAll()
    {
        return $this->getModel()->whereActive(true)->delete(); // number
    }

    public function destroyAllByIds($ids)
    {
        return $this->getModel()->destroy($ids); // number
    }

    public function countAll()
    {
        return $this->getModel()->count(); // number
    }

    public function countAllActive()
    {
        return $this->getModel()->whereActive(true)->count(); // number
    }

    public function generateCode($prefix)
    {
        $code = $prefix . date('ymd');
        $stt  = $this->getModel()->where('code', 'like', $code . '%')->get()->count() + 1;
        return $code . substr("00" . $stt, -3);
    }

    public function existsValue($field_name, $value, $skip_id = [])
    {
        // Check luôn cả dữ liệu đã deactivate [whereActive(true)]
        $exists = $this->getModel()->where($field_name, $value)->whereNotIn('id', $skip_id)->count();
        return ($exists > 0); // boolean
    }

    public function findAllSkeleton()
    {
        return $this->findAll();
    }

    public function findOneSkeleton($id)
    {
        return $this->findAllSkeleton()->where('id', $id)->first();
    }

    public function findAllActiveSkeleton()
    {
        return $this->findAllActive();
    }

    public function findOneActiveSkeleton($id)
    {
        return $this->findAllActiveSkeleton()->where('id', $id)->first();
    }

}