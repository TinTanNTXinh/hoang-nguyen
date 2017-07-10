<?php

namespace App\Services\Eloquent;

use App\Services\CustomerServiceInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\CustomerTypeRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class CustomerService implements CustomerServiceInterface
{
    private $user;
    private $table_name;

    protected $customerRepo, $customerTypeRepo;

    public function __construct(CustomerRepositoryInterface $customerRepo
        , CustomerTypeRepositoryInterface $customerTypeRepo)
    {
        $this->customerRepo     = $customerRepo;
        $this->customerTypeRepo = $customerTypeRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'customer';
    }

    public function readAll()
    {
        $all = $this->customerRepo->allSkeleton()->get();

        $customer_types = $this->customerTypeRepo->allActive();

        return [
            'customers'      => $all,
            'customer_types' => $customer_types
        ];
    }

    public function readOne($id)
    {
        $one = $this->customerRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
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

            $one = $this->customerRepo->create($i_one);

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

            $one = $this->customerRepo->find($data['id']);

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

            $one = $this->customerRepo->update($one, $i_one);

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

            $one = $this->customerRepo->deactivate($id);

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

            $one = $this->customerRepo->destroy($id);

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

        $filtered = $this->customerRepo->allSkeleton();

        $filtered = $this->customerRepo->filterFromDateToDate($filtered, 'customers.created_at', $from_date, $to_date);

        $filtered = $this->customerRepo->filterRangeDate($filtered, 'customers.created_at', $range);

        return [
            'customers' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}