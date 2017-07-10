<?php

use Illuminate\Database\Seeder;
use App\Repositories\UnitRepositoryInterface;

class UnitsTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(UnitRepositoryInterface $repository)
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
            'đ/Chuyến',
            'đ/Kg',
            'đ/Tấn',
            'đ/Pallet',
            'đ/Khối',
            'đ/Thùng',
            'đ/Cây'
        ];

        foreach ($array_name as $name) {
            \App\Unit::create([
                'code'        => $this->repository->generateCode('UNIT'),
                'name'        => $name,
                'description' => '',
                'active'      => true
            ]);
        }
    }
}
