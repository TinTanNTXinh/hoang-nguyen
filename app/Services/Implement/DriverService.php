<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\DriverServiceInterface;
use App\Repositories\DriverRepositoryInterface;
use App\Repositories\FileRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class DriverService implements DriverServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $driverRepo, $fileRepo;

    public function __construct(AuthServiceInterface $authService
        , DriverRepositoryInterface $driverRepo
        , FileRepositoryInterface $fileRepo)
    {
        $this->authService = $authService;
        $this->driverRepo  = $driverRepo;
        $this->fileRepo    = $fileRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'driver';
    }

    public function readAll()
    {
        $all   = $this->driverRepo->findAllActiveSkeleton();
        $files = $this->fileRepo->findAllActive();

        return [
            'drivers' => $all,
            'files'   => $files
        ];
    }

    public function readOne($id)
    {
        $one = $this->driverRepo->findOneActiveSkeleton($id);

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        $validates = $this->validateInput($data);
        if (!$validates['status'])
            return $validates;

        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            $i_one = [
                'code'                  => $this->driverRepo->generateCode('DRIVER'),
                'fullname'              => $data['fullname'],
                'phone'                 => $data['phone'],
                'birthday'              => DateTimeHelper::toStringDateTimeClientForDB($data['birthday'], DateTimeHelper::$clientFormatDate),
                'sex'                   => $data['sex'],
                'email'                 => null,
                'dia_chi_thuong_tru'    => $data['dia_chi_thuong_tru'],
                'dia_chi_tam_tru'       => $data['dia_chi_tam_tru'],
                'so_chung_minh'         => $data['so_chung_minh'],
                'ngay_cap_chung_minh'   => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_cap_chung_minh'], DateTimeHelper::$clientFormatDate),
                'loai_bang_lai'         => $data['loai_bang_lai'],
                'so_bang_lai'           => $data['so_bang_lai'],
                'ngay_cap_bang_lai'     => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_cap_bang_lai'], DateTimeHelper::$clientFormatDate),
                'ngay_het_han_bang_lai' => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_het_han_bang_lai'], DateTimeHelper::$clientFormatDate),
                'start_date'            => DateTimeHelper::toStringDateTimeClientForDB($data['start_date'], DateTimeHelper::$clientFormatDate),
                'finish_date'           => DateTimeHelper::toStringDateTimeClientForDB($data['finish_date'], DateTimeHelper::$clientFormatDate),
                'note'                  => $data['note'],
                'created_by'            => $this->user->id,
                'updated_by'            => 0,
                'created_date'          => date('Y-m-d'),
                'updated_date'          => null,
                'active'                => true
            ];

            $one = $this->driverRepo->createOne($i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            DB::commit();
            $result['id']     = $one->id;
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function updateOne($data)
    {
        $validates = $this->validateInput($data);
        if (!$validates['status'])
            return $validates;

        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            $one = $this->driverRepo->findOneActive($data['id']);

            $i_one = [
                'fullname'              => $data['fullname'],
                'phone'                 => $data['phone'],
                'birthday'              => DateTimeHelper::toStringDateTimeClientForDB($data['birthday'], DateTimeHelper::$clientFormatDate),
                'sex'                   => $data['sex'],
                'dia_chi_thuong_tru'    => $data['dia_chi_thuong_tru'],
                'dia_chi_tam_tru'       => $data['dia_chi_tam_tru'],
                'so_chung_minh'         => $data['so_chung_minh'],
                'ngay_cap_chung_minh'   => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_cap_chung_minh'], DateTimeHelper::$clientFormatDate),
                'loai_bang_lai'         => $data['loai_bang_lai'],
                'so_bang_lai'           => $data['so_bang_lai'],
                'ngay_cap_bang_lai'     => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_cap_bang_lai'], DateTimeHelper::$clientFormatDate),
                'ngay_het_han_bang_lai' => DateTimeHelper::toStringDateTimeClientForDB($data['ngay_het_han_bang_lai'], DateTimeHelper::$clientFormatDate),
                'start_date'            => DateTimeHelper::toStringDateTimeClientForDB($data['start_date'], DateTimeHelper::$clientFormatDate),
                'finish_date'           => DateTimeHelper::toStringDateTimeClientForDB($data['finish_date'], DateTimeHelper::$clientFormatDate),
                'note'                  => $data['note'],
                'updated_by'            => $this->user->id,
                'updated_date'          => date('Y-m-d'),
                'active'                => true
            ];

            $one = $this->driverRepo->updateOne($one->id, $i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function deactivateOne($id)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            $one = $this->driverRepo->deactivateOne($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Deactivate File
            $files = $this->fileRepo->findAllActiveByTableNameAndTableId('drivers', $id);
            $this->fileRepo->deactivateAllByIds($files->pluck('id')->toArray());

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function deleteOne($id)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            $one = $this->driverRepo->destroyOne($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Delete File
            $files = $this->fileRepo->findAllActiveByTableNameAndTableId('drivers', $id);
            $this->fileRepo->destroyAllByIds($files->pluck('id')->toArray());

            DB::commit();
            $result['status'] = true;
            return $result;
        } catch (Exception $ex) {
            DB::rollBack();
            return $result;
        }
    }

    public function searchOne($filter)
    {
        $from_date = $filter['from_date'];
        $to_date   = $filter['to_date'];
        $range     = $filter['range'];
        $driver_id = $filter['driver_id'];

        $filtered = $this->driverRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_at', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_at', $range);

        if ($driver_id != 0)
            $filtered = $filtered->where('id', $driver_id);

        return [
            'drivers' => $filtered->values()
        ];
    }

    /** ===== VALIDATE BASIC ===== */
    public function validateInput($data)
    {
        if (!$this->validateEmpty($data))
            return ['status' => false, 'errors' => 'Dữ liệu không hợp lệ.'];

        $msgs = $this->validateLogic($data);
        return $msgs;
    }

    public function validateEmpty($data)
    {
//        if (!$data['name']) return false;
        return true;
    }

    public function validateLogic($data)
    {
        $msg_error = [];

//        $skip_id = isset($data['id']) ? [$data['id']] : [];
//
//        if ($data['code'] && $this->userRepo->existsValue('code', $data['code'], $skip_id))
//            array_push($msg_error, 'Mã đã tồn tại.');

//        if ($this->userRepo->existsValue('name', $data['name'], $skip_id))
//            array_push($msg_error, 'Tên đã tồn tại.');

        return [
            'status' => count($msg_error) > 0 ? false : true,
            'errors' => $msg_error
        ];
    }

    /** ===== VALIDATE ADVANCED ===== */
    public function validateUpdateOne($id)
    {
        return $this->validateDeactivateOne($id);
    }

    public function validateDeactivateOne($id)
    {
        $msg_error = [];

        return [
            'status' => count($msg_error) > 0 ? false : true,
            'errors' => $msg_error
        ];
    }

    public function validateDeleteOne($id)
    {
        return $this->validateDeactivateOne($id);
    }

    /** ===== MY FUNCTION ===== */

}