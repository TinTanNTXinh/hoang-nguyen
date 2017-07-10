<?php

use Illuminate\Database\Seeder;
use App\Repositories\TruckRepositoryInterface;

class TrucksTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(TruckRepositoryInterface $repository)
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
        $number_plates = ['1111', '2222', '3333', '4444'];
        $truck_type_ids = [1, 2, 3, 3];
        foreach($number_plates as $key => $number_plate) {
            \App\Truck::create([
                'code'                => $this->repository->generateCode('TRUCK'),
                'area_code'           => '54N',
                'number_plate'        => $number_plate,
                'trademark'           => 'Hyundai',
                'year_of_manufacture' => '1992',
                'owner'               => 'Nguyễn Văn A',
                'length'              => '10',
                'width'               => '10',
                'height'              => '10',
                'status'              => 'Chưa phân tài',
                'note'                => '',
                'created_by'          => 1,
                'updated_by'          => 0,
                'created_date'        => date('Y-m-d'),
                'updated_date'        => null,
                'active'              => true,
                'truck_type_id'       => $truck_type_ids[$key],
                'garage_id'           => 1
            ]);
        }
    }
}
