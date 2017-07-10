<?php

namespace App\Services\Implement;

use App\Services\AuthServiceInterface;
use App\Services\ProductServiceInterface;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\ProductCodeRepositoryInterface;
use App\Common\Helpers\DateTimeHelper;
use App\Common\Helpers\FilterHelper;
use DB;
use League\Flysystem\Exception;

class ProductService implements ProductServiceInterface
{
    private $user;
    private $table_name;

    protected $authService, $productRepo, $productCodeRepo;

    public function __construct(AuthServiceInterface $authService
        , ProductRepositoryInterface $productRepo
        , ProductCodeRepositoryInterface $productCodeRepo)
    {
        $this->authService     = $authService;
        $this->productRepo     = $productRepo;
        $this->productCodeRepo = $productCodeRepo;

        $jwt_data = $this->authService->getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = $this->authService->getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'product';
    }

    public function readAll()
    {
        $all = $this->productRepo->findAllActiveSkeleton();

        return [
            'products' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->productRepo->findOneActiveSkeleton($id);

        $product_codes = $this->productCodeRepo->findAllActiveByFieldName('product_id', $id)
            ->pluck('name')
            ->toArray();

        return [
            $this->table_name => $one,
            'product_codes'   => $product_codes
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

        $product       = $data['product'];
        $product_codes = $data['product_codes'];

        try {
            DB::beginTransaction();

            $i_one = [
                'code'            => $this->productRepo->generateCode('PRODUCT'),
                'name'            => $product['name'],
                'description'     => $product['description'],
                'active'          => true,
                'product_type_id' => 0
            ];

            $one = $this->productRepo->createOne($i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Insert ProductCode
            foreach ($product_codes as $code) {
                $i_product_code = [
                    'code'        => $this->productCodeRepo->generateCode('PRODUCTCODE'),
                    'name'        => $code,
                    'description' => '',
                    'active'      => true,
                    'product_id'  => $one->id
                ];
                $product_code   = $this->productCodeRepo->createOne($i_product_code);

                if (!$product_code) {
                    DB::rollback();
                    return $result;
                }
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

        $product       = $data['product'];
        $product_codes = $data['product_codes'];

        try {
            DB::beginTransaction();

            $one = $this->productRepo->findOneActive($product['id']);

            $i_one = [
                'name'        => $product['name'],
                'description' => $product['description'],
                'active'      => true
            ];

            $one = $this->productRepo->updateOne($one->id, $i_one);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            // Delete ProductCode
            $this->productCodeRepo->deleteByProductId($one->id);

            // Insert ProductCode
            foreach ($product_codes as $code) {
                $i_product_code = [
                    'code'        => $this->productCodeRepo->generateCode('PRODUCTCODE'),
                    'name'        => $code,
                    'description' => '',
                    'active'      => true,
                    'product_id'  => $one->id
                ];
                $product_code   = $this->productCodeRepo->createOne($i_product_code);

                if (!$product_code) {
                    DB::rollback();
                    return $result;
                }
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

            $one = $this->productRepo->deactivateOne($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            $this->productCodeRepo->deactivateByProductId($id);

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

            $one = $this->productRepo->destroyOne($id);

            if (!$one) {
                DB::rollback();
                return $result;
            }

            $this->productCodeRepo->deleteByProductId($id);

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

        $filtered = $this->productRepo->findAllActiveSkeleton();

        $filtered = FilterHelper::filterByFromDateToDate($filtered, 'created_at', $from_date, $to_date);

        $filtered = FilterHelper::filterByRangeDate($filtered, 'created_at', $range);

        return [
            'products' => $filtered->values()
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