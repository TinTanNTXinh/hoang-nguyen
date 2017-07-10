<?php

namespace App\Services\Eloquent;

use App\Services\LubeServiceInterface;
use App\Repositories\LubeRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class LubeService implements LubeServiceInterface
{
    private $user;
    private $table_name;

    protected $lubeRepo;

    public function __construct(LubeRepositoryInterface $lubeRepo)
    {
        $this->lubeRepo = $lubeRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'lube';
    }

    public function readAll()
    {
        $all = $this->lubeRepo->allSkeleton()->get();

        return [
            'lubes' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->lubeRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'         => $this->lubeRepo->generateCode('LUBE'),
                'price'        => $data['price'],
                'type'         => 'LUBE',
                'apply_date'   => DateTimeHelper::toStringDateTimeClientForDB($data['apply_date']),
                'note'         => $data['note'],
                'created_by'   => $this->user->id,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true
            ];

            $one = $this->lubeRepo->create($i_one);

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

            $one = $this->lubeRepo->find($data['id']);

            $i_one = [
                'price'        => $data['price'],
                'type'         => 'LUBE',
                'apply_date'   => DateTimeHelper::toStringDateTimeClientForDB($data['apply_date']),
                'note'         => $data['note'],
                'updated_by'   => $this->user->id,
                'updated_date' => date('Y-m-d'),
                'active'       => true
            ];

            $one = $this->lubeRepo->update($one, $i_one);

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

            $one = $this->lubeRepo->deactivate($id);

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

            $one = $this->lubeRepo->destroy($id);

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

        $filtered = $this->lubeRepo->allSkeleton();

        $filtered = $this->lubeRepo->filterFromDateToDate($filtered, 'fuels.created_date', $from_date, $to_date);

        $filtered = $this->lubeRepo->filterRangeDate($filtered, 'fuels.created_date', $range);

        return [
            'lubes' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */
    public function readByApplyDate($apply_date)
    {
        $apply_date = DateTimeHelper::toStringDateTimeClientForDB($apply_date);
        $lube       = $this->lubeRepo->findByApplyDate($apply_date);
        return [
            'lube' => $lube
        ];
    }

}