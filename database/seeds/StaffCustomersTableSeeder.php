<?php

use Illuminate\Database\Seeder;
use App\Repositories\StaffCustomerRepositoryInterface;

class StaffCustomersTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(StaffCustomerRepositoryInterface $repository)
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
            'Hồ Văn Khởi',
            'Lưu Hoàng Kha',
            'Phạm Hữu Dư',
            'Nguyễn Hoàng Phúc'
        ];

        foreach ($array_name as $key => $value) {
            \App\StaffCustomer::create([
                'code'         => $this->repository->generateCode('STAFFCUSTOMER'),
                'fullname'     => $value,
                'address'      => '',
                'phone'        => '0987655321',
                'birthday'     => '1990-01-03',
                'sex'          => 'Nam',
                'email'        => 'myemail@email.com',
                'position'     => 'Kế toán',
                'active'       => true,
                'customer_id'  => ++$key
            ]);
        }
    }
}
