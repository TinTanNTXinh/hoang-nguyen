<?php

use Illuminate\Database\Seeder;
use App\Repositories\UserRepositoryInterface;

class UsersTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(UserRepositoryInterface $repository)
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
        $prefix = 'USER';

        $array_name = [
            'Trần Thị Mỹ Nhi',
            'Nguyễn Đình Trường',
            'Trần Thị Thùy Trang',
            'Lê Bảo Khánh',
            'Đồng Thụy Mỹ Phương',
            'Nguyễn Thị Tường Ánh',
            'Hà Cẩm Quyên',
            'Võ Tấn Trường',
            'Lê Thị Xuân Nở',
            'Nguyễn Thế Anh',
            'Nguyễn Trần Hoàng Ngân',
            'Trần Nguyễn Thiện Lâm',
            'Nguyễn Hoàng Nam',
            'Huỳnh Tấn Đoàn',
            'Nguyễn Trung Nam',
            'Nguyễn Ngọc Sơn Trà'
        ];

        $array_username = [
            'tranthimynhi',
            'nguyendinhtruong',
            'tranthithuytrang',
            'lebaokhanh',
            'dongthuymyphuong',
            'nguyenthituonganh',
            'hacamquyen',
            'votantruong',
            'lethixuanno',
            'nguyentheanh',
            'nguyentranhoangngan',
            'trannguyenthienlam',
            'nguyenhoangnam',
            'huynhtandoan',
            'nguyentrungnam',
            'sontra'
        ];

        $array_phone = [
            '0907914033',
            '',
            '0908992024',
            '0908404095',
            '0902584558',
            '0965115547',
            '0909351767',
            '0918307097',
            '0979406949',
            '0933975234',
            '0903632733',
            '0906859565',
            '0938555890',
            '0906900511',
            '01644741267',
            '0985512512'
        ];

        $array_email = [
            'nhitran1983@hoangnguyenjsc.com',
            '',
            'trangtran3110@hoangnguyenjsc.com',
            'khanhle@hoangnguyenjsc.com',
            'phuongdong@hoangnguyenjsc.com',
            'anhnguyen@hoangnguyenjsc.com',
            'quyencam@hoangnguyenjsc.com',
            'truongvo@hoangnguyenjsc.com',
            'xuannolt@hoangnguyenjsc.com',
            'theanh.nguyen@hoangnguyenjsc.com',
            'ngannguyen@hoangnguyenjsc.com',
            'lamtran@hoangnguyenjsc.com',
            'namnguyen@hoangnguyenjsc.com',
            'doanhuynh@hoangnguyenjsc.com',
            'trungnam@hoangnguyenjsc.com',
            'tra.nguyen@hoangnguyenjsc.com'
        ];

        $array_sex = [
            'Nữ',
            'Nam',
            'Nữ',
            'Nam',
            'Nữ',
            'Nữ',
            'Nữ',
            'Nam',
            'Nữ',
            'Nam',
            'Nam',
            'Nam',
            'Nam',
            'Nam',
            'Nam',
            'Nam'
        ];

        foreach($array_name as $key => $name) {
            \App\User::create([
                'code'          => $this->repository->generateCode($prefix),
                'fullname'      => $name,
                'username'      => $array_username[$key],
                'password'      => Hash::make('123456'),
                'address'       => '',
                'phone'         => $array_phone[$key],
                'birthday'      => date('Y-m-d'),
                'sex'           => $array_sex[$key],
                'email'         => $array_email[$key],
                'note'          => '',
                'created_by'    => 1,
                'updated_by'    => 0,
                'created_date'  => date('Y-m-d H:i:s'),
                'updated_date'  => null,
                'active'        => true
            ]);
        }
    }
}
