<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\StaffCustomerServiceInterface;
use App\Repositories\StaffCustomerRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class StaffCustomerService implements StaffCustomerServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $staffCustomerRepo, $customerRepo;

    public function __construct(AuthServiceInterface $authService
        , StaffCustomerRepositoryInterface $staffCustomerRepo
        , CustomerRepositoryInterface $customerRepo)
    {
        $this->authService      = $authService;
        $this->staffCustomerRepo     = $staffCustomerRepo;
        $this->customerRepo     = $customerRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'staff_customer';
    }

    public function readAll()
    {
        $all = $this->staffCustomerRepo->findAllActiveSkeleton();

        $customers = $this->customerRepo->findAllActive();

        return [
            'staff_customers' => $all,
            'customers'       => $customers
        ];
    }

    public function readOne($id)
    {
        $one = $this->staffCustomerRepo->findOneActiveSkeleton($id);

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
                'code'        => $this->staffCustomerRepo->generateCode('STAFFCUSTOMER'),
                'fullname'    => $data['fullname'],
                'address'     => $data['address'],
                'phone'       => $data['phone'],
                'birthday'    => null,
                'sex'         => 'Nam',
                'email'       => $data['email'],
                'position'    => $data['position'],
                'active'      => true,
                'customer_id' => $data['customer_id']
            ];

            $one = $this->staffCustomerRepo->createOne($i_one);

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

            $one = $this->staffCustomerRepo->findOneActive($data['id']);

            $i_one = [
                'fullname'    => $data['fullname'],
                'address'     => $data['address'],
                'phone'       => $data['phone'],
                'email'       => $data['email'],
                'position'    => $data['position'],
                'active'      => true,
                'customer_id' => $data['customer_id']
            ];

            $one = $this->staffCustomerRepo->updateOne($one->id, $i_one);

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

            $one = $this->staffCustomerRepo->deactivateOne($id);

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

    public function deleteOne($id)
    {
        $result = [
            'status' => false,
            'errors' => []
        ];

        try {
            DB::beginTransaction();

            $one = $this->staffCustomerRepo->destroyOne($id);

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

    public function searchOne($filter)
    {
        $from_date = $filter['from_date'];
        $to_date   = $filter['to_date'];
        $range     = $filter['range'];
        $customer_id = $filter['customer_id'];

        $filtered = $this->staffCustomerRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_at', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_at', $range);

        if ($customer_id != 0)
            $filtered = $filtered->where('customer_id', $customer_id);

        return [
            'staff_customers' => $filtered->values()
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