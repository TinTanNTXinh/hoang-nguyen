<?php

namespace App\Services\Eloquent;

use App\Services\UnitServiceInterface;
use App\Repositories\UnitRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class UnitService implements UnitServiceInterface
{
    private $user;
    private $table_name;

    protected $unitRepo;

    public function __construct(UnitRepositoryInterface $unitRepo)
    {
        $this->unitRepo     = $unitRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'unit';
    }

    public function readAll()
    {
        $all = $this->unitRepo->allSkeleton()->get();

        return [
            'units' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->unitRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'        => $this->unitRepo->generateCode('UNIT'),
                'name'        => $data['name'],
                'description' => $data['description'],
                'active'      => true
            ];

            $one = $this->unitRepo->create($i_one);

            if (!$one) {
                DB::rollback();
                return false;
            }

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            return false;
        }
    }

    public function updateOne($data)
    {
        try {
            DB::beginTransaction();

            $one = $this->unitRepo->find($data['id']);

            $i_one = [
                'name'        => $data['name'],
                'description' => $data['description'],
                'active'      => true
            ];

            $one = $this->unitRepo->update($one, $i_one);

            if (!$one) {
                DB::rollback();
                return false;
            }

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            return false;
        }
    }

    public function deactivateOne($id)
    {
        try {
            DB::beginTransaction();

            $one = $this->unitRepo->deactivate($id);

            if (!$one) {
                DB::rollback();
                return false;
            }

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            return false;
        }
    }

    public function deleteOne($id)
    {
        try {
            DB::beginTransaction();

            $one = $this->unitRepo->destroy($id);

            if (!$one) {
                DB::rollback();
                return false;
            }

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            return false;
        }
    }

    public function searchOne($filter)
    {
        $from_date = $filter['from_date'];
        $to_date   = $filter['to_date'];
        $range     = $filter['range'];

        $filtered = $this->unitRepo->allSkeleton();

        $filtered = $this->unitRepo->filterFromDateToDate($filtered, 'units.created_at', $from_date, $to_date);

        $filtered = $this->unitRepo->filterRangeDate($filtered, 'units.created_at', $range);

        return [
            'units' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}