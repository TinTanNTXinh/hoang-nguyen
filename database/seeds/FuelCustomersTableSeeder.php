<?php

use Illuminate\Database\Seeder;

class FuelCustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = \App\Customer::all();
        $fuels     = \App\Fuel::where('type', 'oil')->get();

        foreach ($customers as $customer) {
            \App\FuelCustomer::create([
                'type'         => 'OIL',
                'fuel_id'      => $fuels[0]->id,
                'customer_id'  => $customer->id,
                'created_by'   => 1,
                'updated_by'   => 0,
                'created_date' => date('Y-m-d'),
                'updated_date' => null,
                'active'       => true
            ]);
        }
    }
}
