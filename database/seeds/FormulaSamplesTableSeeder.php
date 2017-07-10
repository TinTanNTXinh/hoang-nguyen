<?php

use Illuminate\Database\Seeder;
use App\Repositories\FormulaSampleRepositoryInterface;

class FormulaSamplesTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(FormulaSampleRepositoryInterface $repository)
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
        $rules = [
            'OIL',
            'PAIR',
            'RANGE',
            'SINGLE',
            'SINGLE',
            'SINGLE'
        ];

        $names = [
            'Giá dầu',
            'Tuyến đường',
            'Khoảng cách',
            'Mã hàng',
            '1 chiều/2 chiều',
            'Loại xe'
        ];

        $indexs = [
            2,
            1,
            3,
            4,
            5,
            6
        ];

        foreach($names as $key => $name)
        {
            \App\FormulaSample::create([
                'code'         => $this->repository->generateCode('FORMULASAMPLE'),
                'rule'         => $rules[$key],
                'name'         => $name,
                'index'        => $indexs[$key],
                'active'       => true
            ]);
        }
    }
}
