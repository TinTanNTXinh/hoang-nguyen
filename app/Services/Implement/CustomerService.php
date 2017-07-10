<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\CustomerServiceInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\CustomerTypeRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class CustomerService implements CustomerServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $customerRepo, $customerTypeRepo;

    public function __construct(AuthServiceInterface $authService
        , CustomerRepositoryInterface $customerRepo
        , CustomerTypeRepositoryInterface $customerTypeRepo)
    {
        $this->authService      = $authService;
        $this->customerRepo     = $customerRepo;
        $this->customerTypeRepo = $customerTypeRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'customer';
    }

    public function readAll()
    {
        $all = $this->customerRepo->findAllActiveSkeleton();

        $customer_types = $this->customerTypeRepo->findAllActive();

        return [
            'customers'      => $all,
            'customer_types' => $customer_types
        ];
    }

    public function readOne($id)
    {
        $one = $this->customerRepo->findOneActiveSkeleton($id);

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
                'code'             => $this->customerRepo->generateCode('CUSTOMER'),
                'tax_code'         => $data['tax_code'],
                'fullname'         => $data['fullname'],
                'address'          => $data['address'],
                'phone'            => $data['phone'],
                'email'            => $data['email'],
                'limit_oil'        => $data['limit_oil'],
                'oil_per_postage'  => $data['oil_per_postage'],
                'finish_date'      => DateTimeHelper::toStringDateTimeClientForDB($data['finish_date']),
                'note'             => $data['note'],
                'created_by'       => $this->user->id,
                'updated_by'       => 0,
                'created_date'     => date('Y-m-d H:i:s'),
                'updated_date'     => null,
                'active'           => true,
                'customer_type_id' => $data['customer_type_id']
            ];

            $one = $this->customerRepo->createOne($i_one);

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

            $one = $this->customerRepo->findOneActive($data['id']);

            $i_one = [
                'tax_code'         => $data['tax_code'],
                'fullname'         => $data['fullname'],
                'address'          => $data['address'],
                'phone'            => $data['phone'],
                'email'            => $data['email'],
                'limit_oil'        => $data['limit_oil'],
                'oil_per_postage'  => $data['oil_per_postage'],
                'finish_date'      => DateTimeHelper::toStringDateTimeClientForDB($data['finish_date']),
                'note'             => $data['note'],
                'updated_by'       => $this->user->id,
                'updated_date'     => date('Y-m-d H:i:s'),
                'active'           => true,
                'customer_type_id' => $data['customer_type_id']
            ];

            $one = $this->customerRepo->updateOne($one->id, $i_one);

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

            $one = $this->customerRepo->deactivateOne($id);

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

            $one = $this->customerRepo->destroyOne($id);

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
        $from_date   = $filter['from_date'];
        $to_date     = $filter['to_date'];
        $range       = $filter['range'];
        $customer_id = $filter['customer_id'];

        $filtered = $this->customerRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_at', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_at', $range);

        if ($customer_id != 0)
            $filtered = $filtered->where('id', $customer_id);

        return [
            'customers' => $filtered->values()
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