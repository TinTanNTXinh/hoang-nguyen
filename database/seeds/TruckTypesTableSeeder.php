<?php

use Illuminate\Database\Seeder;
use App\Repositories\TruckTypeRepositoryInterface;

class TruckTypesTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(TruckTypeRepositoryInterface $repository)
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
        $array_name   = [
            'Xe container',
            'Xe tải',
            'Xe tải'
        ];
        $array_weight = [
            25, 8, 5
        ];

        $array_prices = [50000, 40000, 30000];

        foreach ($array_name as $key => $name) {
            \App\TruckType::create([
                'code'            => $this->repository->generateCode('TRUCKTYPE'),
                'name'            => $name,
                'weight'          => $array_weight[$key],
                'unit_price_park' => $array_prices[$key],
                'description'     => '',
                'active'          => true
            ]);
        }
    }
}
