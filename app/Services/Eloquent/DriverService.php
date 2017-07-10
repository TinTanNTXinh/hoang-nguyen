<?php

namespace App\Services\Eloquent;

use App\Services\DriverServiceInterface;
use App\Repositories\DriverRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class DriverService implements DriverServiceInterface
{
    private $user;
    private $table_name;

    protected $driverRepo;

    public function __construct(DriverRepositoryInterface $driverRepo)
    {
        $this->driverRepo     = $driverRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'driver';
    }

    public function readAll()
    {
        $all = $this->driverRepo->allSkeleton()->get();

        return [
            'drivers' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->driverRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'                  => $this->driverRepo->generateCode('DRIVER'),
                'fullname'              => $data['fullname'],
                'phone'                 => $data['phone'],
                'birthday'              => DateTimeHelper::toStringDateTimeClientForDB($data['birthday'], 'd/m/Y'),
                'sex'                   => $data['sex'],
                'email'                 => null,
                'dia_chi_thuong_tru'    => $data['dia_chi_thuong_tru'],
                'dia_chi_tam_tru'       => $data['dia_chi_tam_tru'],
                'so_chung_minh'         => $data['so_chung_minh'],
                'ngay_cap_chung_minh'   => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_cap_chung_minh'], 'd/m/Y'),
                'loai_bang_lai'         => $data['loai_bang_lai'],
                'so_bang_lai'           => $data['so_bang_lai'],
                'ngay_cap_bang_lai'     => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_cap_bang_lai'], 'd/m/Y'),
                'ngay_het_han_bang_lai' => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_het_han_bang_lai'], 'd/m/Y'),
                'start_date'            => DateTimeHelper::toStringDateTimeClientForDB($data['start_date'], 'd/m/Y'),
                'finish_date'           => DateTimeHelper::toStringDateTimeClientForDB($data['finish_date'], 'd/m/Y'),
                'note'                  => $data['note'],
                'created_by'            => $this->user->id,
                'updated_by'            => 0,
                'created_date'          => date('Y-m-d'),
                'updated_date'          => null,
                'active'                => true
            ];

            $one = $this->driverRepo->create($i_one);

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

            $one = $this->driverRepo->find($data['id']);

            $i_one = [
                'fullname'              => $data['fullname'],
                'phone'                 => $data['phone'],
                'birthday'              => DateTimeHelper::toStringDateTimeClientForDB($data['birthday'], 'd/m/Y'),
                'sex'                   => $data['sex'],
                'dia_chi_thuong_tru'    => $data['dia_chi_thuong_tru'],
                'dia_chi_tam_tru'       => $data['dia_chi_tam_tru'],
                'so_chung_minh'         => $data['so_chung_minh'],
                'ngay_cap_chung_minh'   => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_cap_chung_minh'], 'd/m/Y'),
                'loai_bang_lai'         => $data['loai_bang_lai'],
                'so_bang_lai'           => $data['so_bang_lai'],
                'ngay_cap_bang_lai'     => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_cap_bang_lai'], 'd/m/Y'),
                'ngay_het_han_bang_lai' => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_het_han_bang_lai'], 'd/m/Y'),
                'start_date'            => DateTimeHelper::toStringDateTimeClientForDB($data['start_date'], 'd/m/Y'),
                'finish_date'           => DateTimeHelper::toStringDateTimeClientForDB($data['finish_date'], 'd/m/Y'),
                'note'                  => $data['note'],
                'updated_by'            => $this->user->id,
                'updated_date'          => date('Y-m-d'),
                'active'                => true
            ];

            $one = $this->driverRepo->update($one, $i_one);

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

            $one = $this->driverRepo->deactivate($id);

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

            $one = $this->driverRepo->destroy($id);

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

        $filtered = $this->driverRepo->allSkeleton();

        $filtered = $this->driverRepo->filterFromDateToDate($filtered, 'drivers.created_date', $from_date, $to_date);

        $filtered = $this->driverRepo->filterRangeDate($filtered, 'drivers.created_date', $range);

        return [
            'drivers' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}