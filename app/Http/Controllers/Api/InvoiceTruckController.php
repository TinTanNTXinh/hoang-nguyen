<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\InvoiceTruckServiceInterface;
use App\Common\Interfaces\ICrud;
use App\Common\Helpers\HttpStatusCodeHelper;
use Route;

class InvoiceTruckController extends Controller implements ICrud
{
    private $table_name;

    protected $invoiceTruckService;

    public function __construct(InvoiceTruckServiceInterface $invoiceTruckService)
    {
        $this->invoiceTruckService = $invoiceTruckService;

        $this->table_name = 'invoice_truck';
    }

    /** ===== API METHOD ===== */
    public function getReadAll()
    {
        $arr_datas = $this->readAll();
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    public function getReadOne()
    {
        $id  = Route::current()->parameter('id');
        $one = $this->readOne($id);
        return response()->json($one, HttpStatusCodeHelper::$ok);
    }

    public function postCreateOne(Request $request)
    {
        $data = $request->input($this->table_name);

        $validate_data = $this->createOne($data);
        if (!$validate_data['status'])
            return response()->json(['errors' => $validate_data['errors']], HttpStatusCodeHelper::$unprocessableEntity);

        $arr_datas       = $this->readAll();
        $arr_datas['id'] = $validate_data['id'];
        return response()->json($arr_datas, HttpStatusCodeHelper::$created);
    }

    public function putUpdateOne(Request $request)
    {
        $data = $request->input($this->table_name);

        $validate_data = $this->updateOne($data);
        if (!$validate_data['status'])
            return response()->json(['errors' => $validate_data['errors']], HttpStatusCodeHelper::$unprocessableEntity);

        $arr_datas = $this->readAll();
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    public function patchDeactivateOne(Request $request)
    {
        $id = $request->input('id');

        $validate_data = $this->deactivateOne($id);
        if (!$validate_data['status'])
            return response()->json(['errors' => $validate_data['errors']], HttpStatusCodeHelper::$unprocessableEntity);

        $arr_datas = $this->readAll();
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    public function deleteDeleteOne(Request $request)
    {
        $id = Route::current()->parameter('id');

        $validate_data = $this->deleteOne($id);
        if (!$validate_data['status'])
            return response()->json(['errors' => $validate_data['errors']], HttpStatusCodeHelper::$unprocessableEntity);

        $arr_datas = $this->readAll();
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    public function getSearchOne()
    {
        $filter    = (array)json_decode($_GET['query']);
        $arr_datas = $this->searchOne($filter);
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    /** ===== LOGIC METHOD ===== */
    public function readAll()
    {
        return $this->invoiceTruckService->readAll();
    }

    public function readOne($id)
    {
        return $this->invoiceTruckService->readOne($id);
    }

    public function createOne($data)
    {
        return $this->invoiceTruckService->createOne($data);
    }

    public function updateOne($data)
    {
        return $this->invoiceTruckService->updateOne($data);
    }

    public function deactivateOne($id)
    {
        return $this->invoiceTruckService->deactivateOne($id);
    }

    public function deleteOne($id)
    {
        return $this->invoiceTruckService->deleteOne($id);
    }

    public function searchOne($filter)
    {
        return $this->invoiceTruckService->searchOne($filter);
    }

    /** ===== MY API FUNCTION ===== */
    public function getReadByTruckIdAndType3()
    {
        $truck_id = Route::current()->parameter('truck_id');
        $one      = $this->readByTruckIdAndType3($truck_id, ['']);
        return response()->json($one, HttpStatusCodeHelper::$ok);
    }

    public function getComputeByTransportIds()
    {
        $data          = (array)json_decode($_GET['query']);
        $transport_ids = $data['transport_ids'];
        $arr_datas     = $this->computeByTransportIds($transport_ids);
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    public function putUpdateCostInTransport(Request $request)
    {
        $data = $request->input('transport');

        if (!$this->updateCostInTransport($data))
            return response()->json(['msg' => ['Update failed!']], HttpStatusCodeHelper::$unprocessableEntity);

        $arr_datas = [
            'transports' => $this->invoiceTruckService->readTransportsByTruckId($data['truck_id'])
        ];
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    public function getReadByPaymentDate()
    {
        $arr_datas = $this->readByPaymentDate(null);
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    /** ===== MY FUNCTION ===== */
    private function readByTruckIdAndType3($truck_id, $type3)
    {
        return $this->invoiceTruckService->readByTruckIdAndType3($truck_id, $type3);
    }

    private function computeByTransportIds($transport_ids)
    {
        return $this->invoiceTruckService->computeByTransportIds($transport_ids);
    }

    private function updateCostInTransport($data)
    {
        return $this->invoiceTruckService->updateCostInTransport($data);
    }

    private function readByPaymentDate($payment_date)
    {
        return $this->invoiceTruckService->readByPaymentDate($payment_date);
    }

}
