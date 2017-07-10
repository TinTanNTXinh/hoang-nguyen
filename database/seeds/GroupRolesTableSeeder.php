<?php

use Illuminate\Database\Seeder;
use App\Repositories\GroupRoleRepositoryInterface;

class GroupRolesTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(GroupRoleRepositoryInterface $repository)
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
            'Mặc định', // 1
            'QL người dùng', // 2
            'QL khách hàng', // 3
            'QL Xe - Tài xế', // 4
            'QL công nợ', // 5
            'QL chi phí', // 6
            'QL nhiên liệu', // 7
            'Báo cáo', // 8
            'Dữ liệu ban đầu' // 9
        ];

        $array_index = [
            1, 3, 4, 5, 8, 7, 6, 9, 2
        ];

        $array_icon_name = [
            '',
            'fa fa-universal-access icon',
            'fa fa-address-book icon',
            'fa fa-truck icon',
            'fa fa-money icon',
            'fa fa-calendar-o icon',
            'fa fa-cubes icon',
            'fa fa-bar-chart icon',
            'fa fa-database icon'
        ];

        foreach($array_name as $key => $name){
            \App\GroupRole::create([
                'code'        => $this->repository->generateCode('GROUP_ROLE'),
                'name'        => $array_name[$key],
                'description' => '',
                'icon_name'   => $array_icon_name[$key],
                'index'       => $array_index[$key],
                'active'      => true
            ]);
        }
    }
}
