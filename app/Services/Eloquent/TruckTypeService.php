<?php

namespace App\Services\Eloquent;

use App\Services\TruckTypeServiceInterface;
use App\Repositories\TruckTypeRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class TruckTypeService implements TruckTypeServiceInterface
{
    private $user;
    private $table_name;

    protected $truckTypeRepo;

    public function __construct(TruckTypeRepositoryInterface $truckTypeRepo)
    {
        $this->truckTypeRepo     = $truckTypeRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'truck_type';
    }

    public function readAll()
    {
        $all = $this->truckTypeRepo->allSkeleton()->get();

        return [
            'truck_types' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->truckTypeRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'            => $this->truckTypeRepo->generateCode('TRUCKTYPE'),
                'name'            => $data['name'],
                'weight'          => $data['weight'],
                'unit_price_park' => $data['unit_price_park'],
                'description'     => $data['description'],
                'active'          => true
            ];

            $one = $this->truckTypeRepo->create($i_one);

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

            $one = $this->truckTypeRepo->find($data['id']);

            $i_one = [
                'name'            => $data['name'],
                'weight'          => $data['weight'],
                'unit_price_park' => $data['unit_price_park'],
                'description'     => $data['description'],
                'active'          => true
            ];

            $one = $this->truckTypeRepo->update($one, $i_one);

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

            $one = $this->truckTypeRepo->deactivate($id);

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

            $one = $this->truckTypeRepo->destroy($id);

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

        $filtered = $this->truckTypeRepo->allSkeleton();

        $filtered = $this->truckTypeRepo->filterFromDateToDate($filtered, 'truck_types.created_at', $from_date, $to_date);

        $filtered = $this->truckTypeRepo->filterRangeDate($filtered, 'truck_types.created_at', $range);

        return [
            'truck_types' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */
    public function readByTruckId($truck_id)
    {
        $truck_type = $this->truckTypeRepo->findByTruckId($truck_id);
        return [
            'truck_type' => $truck_type
        ];
    }

}