<?php

use Illuminate\Database\Seeder;
use App\Repositories\GarageTypeRepositoryInterface;

class GarageTypesTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(GarageTypeRepositoryInterface $repository)
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
            'Xe công ty',
            'Xe ngoài'
        ];

        foreach ($array_name as $name) {
            \App\GarageType::create([
                'code'        => $this->repository->generateCode('GARAGETYPE'),
                'name'        => $name,
                'description' => '',
                'active'      => true
            ]);
        }
    }
}
