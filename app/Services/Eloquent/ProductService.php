<?php

namespace App\Services\Eloquent;

use App\Services\ProductServiceInterface;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\ProductCodeRepositoryInterface;
use App\Common\DateTimeHelper;
use App\Common\AuthHelper;
use DB;
use League\Flysystem\Exception;

class ProductService implements ProductServiceInterface
{
    private $user;
    private $table_name;

    protected $productRepo, $productCodeRepo;

    public function __construct(ProductRepositoryInterface $productRepo
        , ProductCodeRepositoryInterface $productCodeRepo)
    {
        $this->productRepo     = $productRepo;
        $this->productCodeRepo     = $productCodeRepo;

        $jwt_data = AuthHelper::getCurrentUser();
        if ($jwt_data['status']) {
            $user_data = AuthHelper::getInfoCurrentUser($jwt_data['user']);
            if ($user_data['status'])
                $this->user = $user_data['user'];
        }

        $this->table_name = 'product';
    }

    public function readAll()
    {
        $all = $this->productRepo->allSkeleton()->get();

        return [
            'products' => $all
        ];
    }

    public function readOne($id)
    {
        $one = $this->productRepo->oneSkeleton($id)->first();

        $product_codes = $this->productCodeRepo->readByProductId($id)
            ->pluck('name')
            ->toArray();

        return [
            $this->table_name => $one,
            'product_codes'   => $product_codes
        ];
    }

    public function createOne($data)
    {
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

            $one = $this->productRepo->create($i_one);

            if (!$one) {
                DB::rollback();
                return false;
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
                $product_code = $this->productCodeRepo->create($i_product_code);

                if (!$product_code) {
                    DB::rollback();
                    return false;
                }
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
        $product       = $data['product'];
        $product_codes = $data['product_codes'];

        try {
            DB::beginTransaction();

            $one = $this->productRepo->find($product['id']);

            $i_one = [
                'name'        => $product['name'],
                'description' => $product['description'],
                'active'      => true
            ];

            $one = $this->productRepo->update($one, $i_one);

            if (!$one) {
                DB::rollback();
                return false;
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
                $product_code = $this->productCodeRepo->create($i_product_code);

                if (!$product_code) {
                    DB::rollback();
                    return false;
                }
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

            $one = $this->productRepo->deactivate($id);

            if (!$one) {
                DB::rollback();
                return false;
            }

            $this->productCodeRepo->deactivateByProductId($id);

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

            $one = $this->productRepo->destroy($id);

            if (!$one) {
                DB::rollback();
                return false;
            }

            $this->productCodeRepo->deleteByProductId($id);

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

        $filtered = $this->productRepo->allSkeleton();

        $filtered = $this->productRepo->filterFromDateToDate($filtered, 'products.created_at', $from_date, $to_date);

        $filtered = $this->productRepo->filterRangeDate($filtered, 'products.created_at', $range);

        return [
            'products' => $filtered->get()
        ];
    }

    /** ===== MY FUNCTION ===== */

}