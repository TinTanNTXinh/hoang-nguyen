<?php

use Illuminate\Database\Seeder;
use App\Repositories\ProductCodeRepositoryInterface;

class ProductCodesTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(ProductCodeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array_code = [
            'M', 'N', 'O', 'P'
        ];

        $product_ids = \App\Product::all()->pluck('id')->toArray();
        foreach ($product_ids as $key => $product_id) {
            foreach ($array_code as $code) {
                \App\ProductCode::create([
                    'code'        => $this->repository->generateCode('PRODUCTCODE'),
                    'name'        => $code,
                    'description' => '',
                    'active'      => true,
                    'product_id'  => $product_id
                ]);
            }
        }
    }
}
