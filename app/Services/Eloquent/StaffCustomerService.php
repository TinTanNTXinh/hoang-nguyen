<?php

namespace App\Services\Eloquent;

use App\Services\StaffCustomerServiceInterface;
use App\Repositories\StaffCustomerRepositoryInterface;
use App\Repositories\CustomerRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class StaffCustomerService implements StaffCustomerServiceInterface
{
    private $user;
    private $table_name;

    protected $staffCustomerRepo, $customerRepo;

    public function __construct(StaffCustomerRepositoryInterface $staffCustomerRepo
        , CustomerRepositoryInterface $customerRepo)
    {
        $this->staffCustomerRepo     = $staffCustomerRepo;
        $this->customerRepo     = $customerRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'staff_customer';
    }

    public function readAll()
    {
        $all = $this->staffCustomerRepo->allSkeleton()->get();

        $customers = $this->customerRepo->allActive();

        return [
            'staff_customers' => $all,
            'customers'       => $customers
        ];
    }

    public function readOne($id)
    {
        $one = $this->staffCustomerRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
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

            $one = $this->staffCustomerRepo->create($i_one);

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

            $one = $this->staffCustomerRepo->find($data['id']);

            $i_one = [
                'fullname'    => $data['fullname'],
                'address'     => $data['address'],
                'phone'       => $data['phone'],
                'email'       => $data['email'],
                'position'    => $data['position'],
                'active'      => true,
                'customer_id' => $data['customer_id']
            ];

            $one = $this->staffCustomerRepo->update($one, $i_one);

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

            $one = $this->staffCustomerRepo->deactivate($id);

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

            $one = $this->staffCustomerRepo->destroy($id);

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

        $filtered = $this->staffCustomerRepo->allSkeleton();

        $filtered = $this->staffCustomerRepo->filterFromDateToDate($filtered, 'staff_customers.created_at', $from_date, $to_date);

        $filtered = $this->staffCustomerRepo->filterRangeDate($filtered, 'staff_customers.created_at', $range);

        return [
            'staff_customers' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}