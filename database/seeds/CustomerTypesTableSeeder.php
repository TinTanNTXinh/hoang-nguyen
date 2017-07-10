<?php

use Illuminate\Database\Seeder;
use App\Repositories\CustomerTypeRepositoryInterface;

class CustomerTypesTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(CustomerTypeRepositoryInterface $repository)
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
        \App\CustomerType::create([
            'code'        => $this->repository->generateCode('CUSTOMERTYPE'),
            'name'        => 'Công ty',
            'description' => '',
            'active'      => true
        ]);
        \App\CustomerType::create([
            'code'        => $this->repository->generateCode('CUSTOMERTYPE'),
            'name'        => 'Cá nhân',
            'description' => '',
            'active'      => true
        ]);
    }
}
