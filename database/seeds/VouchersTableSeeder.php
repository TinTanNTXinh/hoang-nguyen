<?php

use Illuminate\Database\Seeder;
use App\Repositories\VoucherRepositoryInterface;

class VouchersTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(VoucherRepositoryInterface $repository)
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
            'HĐ xanh',
            'HĐ vàng',
            'HĐ hồng',
            'Phiếu cân',
            'Phiếu nhập kho',
            'Phiếu xuất kho',
            'Phiếu giao hàng',
            'Lịch giao hàng',
            'Chứng từ khác'
        ];

        foreach($array_name as $key => $name){
            \App\Voucher::create([
                'code'		  => $this->repository->generateCode('VOUCHER'),
                'name'        => $array_name[$key],
                'description' => '',
                'active'      => true
            ]);
        }
    }
}
