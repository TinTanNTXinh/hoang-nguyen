<?php

namespace App\Services\Eloquent;

use App\Services\TruckServiceInterface;
use App\Repositories\TruckRepositoryInterface;
use App\Repositories\TruckTypeRepositoryInterface;
use App\Repositories\GarageRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class TruckService implements TruckServiceInterface
{
    private $user;
    private $table_name;

    protected $truckRepo, $truckTypeRepo, $garageRepo;

    public function __construct(TruckRepositoryInterface $truckRepo
        , TruckTypeRepositoryInterface $truckTypeRepo
        , GarageRepositoryInterface $garageRepo)
    {
        $this->truckRepo     = $truckRepo;
        $this->truckTypeRepo = $truckTypeRepo;
        $this->garageRepo    = $garageRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'truck';
    }

    public function readAll()
    {
        $all = $this->truckRepo->allSkeleton()->get();

        $garages     = $this->garageRepo->allActive();
        $truck_types = $this->truckTypeRepo->allSkeleton()->get();

        return [
            'trucks'      => $all,
            'garages'     => $garages,
            'truck_types' => $truck_types
        ];
    }

    public function readOne($id)
    {
        $one = $this->truckRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'                => $this->truckRepo->generateCode('TRUCK'),
                'area_code'           => $data['area_code'],
                'number_plate'        => $data['number_plate'],
                'trademark'           => $data['trademark'],
                'year_of_manufacture' => $data['year_of_manufacture'],
                'owner'               => $data['owner'],
                'length'              => $data['length'],
                'width'               => $data['width'],
                'height'              => $data['height'],
                'status'              => $data['status'],
                'note'                => $data['note'],
                'created_by'          => $this->user->id,
                'updated_by'          => 0,
                'created_date'        => date('Y-m-d'),
                'updated_date'        => null,
                'active'              => true,
                'truck_type_id'       => $data['truck_type_id'],
                'garage_id'           => $data['garage_id']
            ];

            $one = $this->truckRepo->create($i_one);

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

            $one = $this->truckRepo->find($data['id']);

            $i_one = [
                'area_code'           => $data['area_code'],
                'number_plate'        => $data['number_plate'],
                'trademark'           => $data['trademark'],
                'year_of_manufacture' => $data['year_of_manufacture'],
                'owner'               => $data['owner'],
                'length'              => $data['length'],
                'width'               => $data['width'],
                'height'              => $data['height'],
                'status'              => $data['status'],
                'note'                => $data['note'],
                'updated_by'          => $this->user->id,
                'updated_date'        => date('Y-m-d'),
                'active'              => true,
                'truck_type_id'       => $data['truck_type_id'],
                'garage_id'           => $data['garage_id']
            ];

            $one = $this->truckRepo->update($one, $i_one);

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

            $one = $this->truckRepo->deactivate($id);

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

            $one = $this->truckRepo->destroy($id);

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

        $filtered = $this->truckRepo->allSkeleton();

        $filtered = $this->truckRepo->filterFromDateToDate($filtered, 'trucks.created_date', $from_date, $to_date);

        $filtered = $this->truckRepo->filterRangeDate($filtered, 'trucks.created_date', $range);

        return [
            'trucks' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}