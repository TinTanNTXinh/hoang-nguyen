<?php

use Illuminate\Database\Seeder;
use App\Repositories\OilRepositoryInterface;

class FuelsTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(OilRepositoryInterface $repository)
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
        $default_price = 10000;

        $types = ['OIL', 'LUBE'];
        foreach ($types as $type) {
            \App\Fuel::create([
                'code'         => $this->repository->generateCode($type),
                'price'        => $default_price,
                'type'         => $type,
                'apply_date'   => '2016-01-01 00:00:00',
                'note'         => 'Giá mặc định ban đầu',
                'created_by'   => 1,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true
            ]);
        }
    }
}
