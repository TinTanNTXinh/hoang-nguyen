<?php

namespace App\Services\Eloquent;

use App\Services\FormulaSampleServiceInterface;
use App\Repositories\FormulaSampleRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class FormulaSampleService implements FormulaSampleServiceInterface
{
    private $user;
    private $table_name;

    protected $formulaSampleRepo;

    public function __construct(FormulaSampleRepositoryInterface $formulaSampleRepo)
    {
        $this->formulaSampleRepo     = $formulaSampleRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'formula_sample';
    }

    public function readAll()
    {
        $all = $this->formulaSampleRepo->allSkeleton()->get();

        return [
            'formula_samples' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->formulaSampleRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'   => $this->formulaSampleRepo->generateCode('FORMULASAMPLE'),
                'rule'   => $data['rule'],
                'name'   => $data['name'],
                'index'  => 0,
                'active' => true
            ];

            $one = $this->formulaSampleRepo->create($i_one);

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

            $one = $this->formulaSampleRepo->find($data['id']);

            $i_one = [
                'rule'   => $data['rule'],
                'name'   => $data['name'],
                'index'  => 0,
                'active' => true
            ];

            $one = $this->formulaSampleRepo->update($one, $i_one);

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

            $one = $this->formulaSampleRepo->deactivate($id);

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

            $one = $this->formulaSampleRepo->destroy($id);

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

        $filtered = $this->formulaSampleRepo->allSkeleton();

        $filtered = $this->formulaSampleRepo->filterFromDateToDate($filtered, 'formula_samples.created_at', $from_date, $to_date);

        $filtered = $this->formulaSampleRepo->filterRangeDate($filtered, 'formula_samples.created_at', $range);

        return [
            'formula_samples' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}