<?php

use Illuminate\Database\Seeder;
use App\Repositories\ProductTypeRepositoryInterface;

class ProductTypesTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(ProductTypeRepositoryInterface $repository)
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
            'Thuốc',
            'Mỹ phẩm',
            'Hóa chất'
        ];
        foreach ($array_name as $item) {
            \App\ProductType::create([
                'code'        => $this->repository->generateCode('PRODUCTTYPE'),
                'name'        => $item,
                'description' => '',
                'active'      => true
            ]);
        }
    }
}
