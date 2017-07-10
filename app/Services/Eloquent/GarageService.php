<?php

namespace App\Services\Eloquent;

use App\Services\GarageServiceInterface;
use App\Repositories\GarageRepositoryInterface;
use App\Repositories\GarageTypeRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class GarageService implements GarageServiceInterface
{
    private $user;
    private $table_name;

    protected $garageRepo, $garageTypeRepo;

    public function __construct(GarageRepositoryInterface $garageRepo
        , GarageTypeRepositoryInterface $garageTypeRepo)
    {
        $this->garageRepo     = $garageRepo;
        $this->garageTypeRepo     = $garageTypeRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'garage';
    }

    public function readAll()
    {
        $all = $this->garageRepo->allSkeleton()->get();

        $garage_types = $this->garageTypeRepo->allActive();

        return [
            'garages'      => $all,
            'garage_types' => $garage_types
        ];
    }

    public function readOne($id)
    {
        $one = $this->garageRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'           => $this->garageRepo->generateCode('GARAGE'),
                'name'           => $data['name'],
                'description'    => $data['description'],
                'address'        => $data['address'],
                'contactor'      => $data['contactor'],
                'phone'          => $data['phone'],
                'active'         => true,
                'garage_type_id' => $data['garage_type_id']
            ];

            $one = $this->garageRepo->create($i_one);

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

            $one = $this->garageRepo->find($data['id']);

            $i_one = [
                'name'           => $data['name'],
                'description'    => $data['description'],
                'address'        => $data['address'],
                'contactor'      => $data['contactor'],
                'phone'          => $data['phone'],
                'active'         => true,
                'garage_type_id' => $data['garage_type_id']
            ];

            $one = $this->garageRepo->update($one, $i_one);

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

            $one = $this->garageRepo->deactivate($id);

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

            $one = $this->garageRepo->destroy($id);

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

        $filtered = $this->garageRepo->allSkeleton();

        $filtered = $this->garageRepo->filterFromDateToDate($filtered, 'garages.created_at', $from_date, $to_date);

        $filtered = $this->garageRepo->filterRangeDate($filtered, 'garages.created_at', $range);

        return [
            'garages' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}