<?php

use Illuminate\Database\Seeder;
use App\Repositories\PostageRepositoryInterface;

class PostagesTableSeeder extends Seeder
{
    protected $repository;

    public function __construct(PostageRepositoryInterface $repository)
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
        $FORMOSA_UNIT_PRICE = [144500, 234300, 814700];
        foreach ($FORMOSA_UNIT_PRICE as $key => $unit_price) {
            \App\Postage::create([
                'code'             => $this->repository->generateCode('POSTAGE'),
                'unit_price'       => $unit_price,
                'delivery_percent' => 10.00,
                'apply_date'       => '2016-10-10',
                'change_by_fuel'   => 0,
                'note'             => '',
                'created_by'       => 1,
                'updated_by'       => 0,
                'created_date'     => date('Y-m-d'),
                'updated_date'     => null,
                'active'           => true,
                'customer_id'      => 1,
                'unit_id'          => ++$key,
                'type'             => 'OIL',
                'fuel_id'          => 1
            ]);
        }

        # A CHAU #
        $ACHAU_UNIT_PRICE = [1513, 5036];
        foreach ($ACHAU_UNIT_PRICE as $key => $unit_price) {
            \App\Postage::create([
                'code'             => $this->repository->generateCode('POSTAGE'),
                'unit_price'       => $unit_price,
                'delivery_percent' => 10.00,
                'apply_date'       => '2016-10-10',
                'change_by_fuel'   => 0,
                'note'             => '',
                'created_by'       => 1,
                'updated_by'       => 0,
                'created_date'     => date('Y-m-d'),
                'updated_date'     => null,
                'active'           => true,
                'customer_id'      => 2,
                'unit_id'          => ++$key,
                'type'             => 'OIL',
                'fuel_id'          => 1
            ]);
        }

        # PROTRADE #
        $PROTRADE_UNIT_PRICE = [144500, 234300, 814700];
        foreach ($PROTRADE_UNIT_PRICE as $key => $unit_price) {
            \App\Postage::create([
                'code'             => $this->repository->generateCode('POSTAGE'),
                'unit_price'       => $unit_price,
                'delivery_percent' => 10.00,
                'apply_date'       => '2016-10-10',
                'change_by_fuel'   => 0,
                'note'             => '',
                'created_by'       => 1,
                'updated_by'       => 0,
                'created_date'     => date('Y-m-d'),
                'updated_date'     => null,
                'active'           => true,
                'customer_id'      => 3,
                'unit_id'          => ++$key,
                'type'             => 'OIL',
                'fuel_id'          => 1
            ]);
        }
    }
}
