<?php

namespace App\Services\Eloquent;

use App\Services\VoucherServiceInterface;
use App\Repositories\VoucherRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class VoucherService implements VoucherServiceInterface
{
    private $user;
    private $table_name;

    protected $voucherRepo;

    public function __construct(VoucherRepositoryInterface $voucherRepo)
    {
        $this->voucherRepo     = $voucherRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'voucher';
    }

    public function readAll()
    {
        $all = $this->voucherRepo->allSkeleton()->get();

        return [
            'vouchers' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->voucherRepo->oneSkeleton($id)->first();

        return [
            $this->table_name => $one
        ];
    }

    public function createOne($data)
    {
        try {
            DB::beginTransaction();

            $i_one = [
                'code'        => $this->voucherRepo->generateCode('VOUCHER'),
                'name'        => $data['name'],
                'description' => $data['description'],
                'active'      => true
            ];

            $one = $this->voucherRepo->create($i_one);

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

            $one = $this->voucherRepo->find($data['id']);

            $i_one = [
                'name'        => $data['name'],
                'description' => $data['description'],
                'active'      => true
            ];

            $one = $this->voucherRepo->update($one, $i_one);

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

            $one = $this->voucherRepo->deactivate($id);

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

            $one = $this->voucherRepo->destroy($id);

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

        $filtered = $this->voucherRepo->allSkeleton();

        $filtered = $this->voucherRepo->filterFromDateToDate($filtered, 'vouchers.created_at', $from_date, $to_date);

        $filtered = $this->voucherRepo->filterRangeDate($filtered, 'vouchers.created_at', $range);

        return [
            'vouchers' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}