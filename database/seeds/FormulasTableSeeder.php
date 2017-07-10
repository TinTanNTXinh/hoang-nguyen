<?php

use Illuminate\Database\Seeder;
use App\Repositories\FormulaRepositoryInterface;

class FormulasTableSeeder extends Seeder
{
    protected $repository;
    public function __construct(FormulaRepositoryInterface $repository)
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
        # FORMOSA #
        $FORMOSA_VALUE = ['Đồng Nai', 'Tp HCM', 'Nha Trang'];
        foreach ($FORMOSA_VALUE as $key => $value) {
            \App\Formula::create([
                'code'         => $this->repository->generateCode('FORMULA'),
                'rule'         => 'SINGLE',
                'name'         => 'Tỉnh',
                'value1'       => $value,
                'value2'       => null,
                'index'        => ++$key,
                'created_by'   => 1,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true,
                'postage_id'   => $key,
            ]);
        }

        # A CHAU #
        $ACHAU_VALUE1 = ['An Giang', 'TX Châu Đốc', '310', 'M'];
        $ACHAU_NAME   = ['Tỉnh', 'Địa chỉ giao hàng', 'Cự ly', 'Mã SP'];

        foreach ($ACHAU_VALUE1 as $key => $value) {
            \App\Formula::create([
                'code'         => $this->repository->generateCode('FORMULA'),
                'rule'         => 'SINGLE',
                'name'         => $ACHAU_NAME[$key],
                'value1'       => $value,
                'value2'       => null,
                'index'        => ++$key,
                'created_by'   => 1,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true,
                'postage_id'   => 4,
            ]);
        }

        # PROTRADE #
        $PROTRADE_VALUE1 = [0, 10000, 20000];
        $PROTRADE_VALUE2 = [9500, 19500, 29500];
        $postage_id = 6;
        foreach ($PROTRADE_VALUE1 as $key => $value1) {
            \App\Formula::create([
                'code'         => $this->repository->generateCode('FORMULA'),
                'rule'         => 'OIL',
                'name'         => 'Giá dầu',
                'value1'       => $value1,
                'value2'       => $PROTRADE_VALUE2[$key],
                'index'        => ++$key,
                'created_by'   => 1,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true,
                'postage_id'   => $postage_id++,
            ]);
        }
    }
}
