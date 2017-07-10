<?php

use Illuminate\Database\Seeder;
use App\Repositories\ProductRepositoryInterface;

class ProductsTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(ProductRepositoryInterface $repository)
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
        $array_name = [
            'Sợi',
            'Bông',
            'Bao vitamin',
            'Phuy hóa chất',
            'Kiện',
            'Bao',
            'Thùng'
        ];

        foreach ($array_name as $key => $name) {
            \App\Product::create([
                'code'            => $this->repository->generateCode('PRODUCT'),
                'name'            => $name,
                'description'     => '',
                'active'          => true,
                'product_type_id' => 1
            ]);
        }
    }
}
