<?php

use Illuminate\Database\Seeder;
use App\Repositories\RoleRepositoryInterface;

class RolesTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(RoleRepositoryInterface $repository)
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
            'Dashboard', // 1
            'Position', // 2
            'User', // 3
            'Driver', // 10
            'Truck', // 9
            'Customer', // 4
            'Transport', // 7
            'Garage', // 8
            'InvoiceCustomer', // 17
            'InvoiceTruck', // 18
            'CostOil', // 13
            'CostLube', // 14
            'CostParking', // 15
            'CostOther', // 16
            'Postage', // 6
            'Oil', // 11
            'Lube', // 12
            'ReportRevenue', // 19
            'HistoryTransport', // 20
            'StaffCustomer', // 5
            'DriverTruck',
            'Unit',
            'Product',
            'Voucher',
            'FormulaSample',
            'TruckType',
        ];

        $array_group_id = [
            1,
            2,
            2,
            4,
            4,
            3,
            3,
            4,
            5,
            5,
            6,
            6,
            6,
            6,
            3,
            7,
            7,
            8,
            8,
            3,
            4,
            9,
            9,
            9,
            9,
            9,
        ];

        $array_index = [
            1, 2, 3, 10, 9, 4, 7, 8, 17, 18, 13, 14, 15, 16, 6, 11, 12, 19, 20, 5, 11, 22, 23, 25, 26, 24,
        ];

        $array_description = [
            'Trang chủ',
            'Chức vụ',
            'Người dùng',
            'Tài xế',
            'Xe',
            'Khách hàng',
            'Đơn hàng',
            'Nhà xe',
            'Khách hàng',
            'Xe',
            'Dầu',
            'Nhớt',
            'Đậu bãi',
            'Khác',
            'Cước phí',
            'Dầu',
            'Nhớt',
            'Doanh thu',
            'Lịch sử giao hàng',
            'Nhân viên khách hàng',
            'Phân tài',
            'Đơn vị tính',
            'Hàng',
            'Chứng từ',
            'Công thức mẫu',
            'Loại xe'
        ];

        $array_icon_name = [
            'fa fa-dashboard icon text-primary-lter',
            'fa fa-sitemap icon text-danger-lter',
            'fa fa-male icon text-danger-lter',
            'fa fa-id-card icon text-danger-lter',
            'fa fa-truck icon text-info-lter',
            'fa fa-users icon text-info-lter',
            'fa fa-shopping-cart icon text-success-lter',
            'fa fa-home icon text-success-lter',
            'fa fa-credit-card icon text-success-lter',
            'fa fa-credit-card icon text-info-lter',
            'fa fa-tint icon text-warning-lter',
            'fa fa-tint icon text-warning-lter',
            'fa fa-road icon text-info-lter',
            'fa fa-ellipsis-h icon text-info-lter',
            'fa fa-calendar-o icon text-success-lter',
            'fa fa-tint icon text-warning-lter',
            'fa fa-tint icon text-warning-lter',
            'fa fa-calculator icon text-primary-lter',
            'fa fa-calculator icon text-primary-lter',
            'fa fa-user icon text-info-lter',
            'fa fa-arrows icon text-warning-lter',
            'fa fa-thermometer-empty icon text-primary-lter',
            'fa fa-cube icon text-primary-lter',
            'fa fa-file-text icon text-warning-lter',
            'fa fa-file-text-o icon text-warning-lter',
            'fa fa-car icon text-warning-lter',
        ];

        foreach ($array_name as $key => $name) {
            $router_link = $array_name[$key] == 'IOCenter' ? 'IoCenter' : $array_name[$key];
            \App\Role::create([
                'code'          => $this->repository->generateCode('ROLE'),
                'name'          => $array_name[$key],
                'description'   => $array_description[$key],
                'router_link'   => '/' . strtolower(preg_replace('/\B([A-Z])/', '-$1', $router_link)) . 's',
                'icon_name'     => $array_icon_name[$key],
                'index'         => $array_index[$key],
                'group_role_id' => $array_group_id[$key],
                'active'        => true
            ]);
        }
    }
}
