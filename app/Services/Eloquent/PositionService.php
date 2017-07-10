<?php

namespace App\Services\Eloquent;

use App\Services\PositionServiceInterface;
use App\Repositories\PositionRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class PositionService implements PositionServiceInterface
{
    private $user;
    private $table_name;

    protected $positionRepo;

    public function __construct(PositionRepositoryInterface $positionRepo)
    {
        $this->positionRepo     = $positionRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'position';
    }

    public function readAll()
    {
        $all = $this->positionRepo->allSkeleton()->get();

        return [
            'positions' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->positionRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'        => $this->positionRepo->generateCode('POSITION'),
                'name'        => $data['name'],
                'description' => $data['description'],
                'active'      => true
            ];

            $one = $this->positionRepo->create($i_one);

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

            $one = $this->positionRepo->find($data['id']);

            $i_one = [
                'name'        => $data['name'],
                'description' => $data['description'],
                'active'      => true
            ];

            $one = $this->positionRepo->update($one, $i_one);

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

            $one = $this->positionRepo->deactivate($id);

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

            $one = $this->positionRepo->destroy($id);

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

        $filtered = $this->positionRepo->allSkeleton();

        $filtered = $this->positionRepo->filterFromDateToDate($filtered, 'positions.created_at', $from_date, $to_date);

        $filtered = $this->positionRepo->filterRangeDate($filtered, 'positions.created_at', $range);

        return [
            'positions' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}