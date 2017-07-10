<?php

namespace App\Repositories\Eloquent;

use App\Repositories\TransportRepositoryInterface;
use App\Transport;
use App\Common\DBHelper;
use DB;

class TransportEloquentRepository extends EloquentBaseRepository implements TransportRepositoryInterface
{
    /** ===== INIT MODEL ===== */
    public function setModel()
    {
        return Transport::class;
    }

    /** ===== PUBLIC FUNCTION ===== */
    public function allSkeleton()
    {
        return $this->allActiveQuery('transports.active')
            ->leftJoin('products', 'products.id', '=', 'transports.product_id')
            ->leftJoin('customers', 'customers.id', '=', 'transports.customer_id')
            ->leftJoin('trucks', 'trucks.id', '=', 'transports.truck_id')
            ->leftJoin('truck_types', 'truck_types.id', '=', 'trucks.truck_type_id')
            ->leftJoin('postages', 'postages.id', '=', 'transports.postage_id')
            ->leftJoin('units', 'units.id', '=', 'postages.unit_id')
            ->leftJoin('users as creators', 'creators.id', '=', 'transports.created_by')
            ->leftJoin('users as updators', 'updators.id', '=', 'transports.updated_by')
            ->orderBy('transports.transport_date', 'desc')
            ->select('transports.*'
                , 'products.name as product_name'
                , 'customers.fullname as customer_fullname'
                , 'trucks.area_code as truck_area_code'
                , 'trucks.number_plate as truck_number_plate'
                , 'creators.fullname as creator_fullname'
                , 'updators.fullname as updator_fullname'
                , 'truck_types.name as truck_type_name'
                , 'postages.unit_price as postage_unit_price'
                , 'units.name as unit_name'
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.receive', 'fc_receive'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.delivery', 'fc_delivery'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.carrying', 'fc_carrying'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.parking', 'fc_parking'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.fine', 'fc_fine'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.phi_tang_bo', 'fc_phi_tang_bo'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.add_score', 'fc_add_score'))

                , DB::raw(DBHelper::getWithCurrencyFormat('transports.delivery_real', 'fc_delivery_real'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.carrying_real', 'fc_carrying_real'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.parking_real', 'fc_parking_real'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.fine_real', 'fc_fine_real'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.phi_tang_bo_real', 'fc_phi_tang_bo_real'))
                , DB::raw(DBHelper::getWithCurrencyFormat('transports.add_score_real', 'fc_add_score_real'))

                , DB::raw(DBHelper::getWithDateTimeFormat('transports.transport_date', 'fd_transport_date'))
                , DB::raw(DBHelper::getWithAreaCodeNumberPlate('trucks.area_code', 'trucks.number_plate', 'truck_area_code_number_plate'))
            )
            ->orderBy('transports.transport_date', 'desc');
    }

    public function oneSkeleton($id)
    {
        return $this->allSkeleton()->where('transports.id', $id);
    }

    public function readByIds($ids)
    {
        return $this->allActiveQuery()
            ->whereIn('id', $ids)
            ->get();
    }

    public function updateType2ByIds($ids, $type2)
    {
        return $this->allActiveQuery()
            ->whereIn('id', $ids)
            ->update(['type2' => $type2]);
    }

    public function updateType3ByIds($ids, $type3)
    {
        return $this->allActiveQuery()
            ->whereIn('id', $ids)
            ->update(['type3' => $type3]);
    }

    public function readByPostageId($postage_id)
    {
        return $this->allActiveQuery()
            ->where('transports.postage_id', $postage_id)
            ->get();
    }

    // Customer
    public function readByCustomerIdAndType2($customer_id, $type2)
    {
        return $this->allSkeleton()
            ->where('transports.customer_id', $customer_id)
            ->whereIn('transports.type2', $type2)
            ->get();
    }

    // Truck
    public function readByTruckIdAndType3($truck_id, $type3)
    {
        return $this->allSkeleton()
            ->where('transports.truck_id', $truck_id)
            ->whereIn('transports.type3', $type3)
            ->get();
    }
}