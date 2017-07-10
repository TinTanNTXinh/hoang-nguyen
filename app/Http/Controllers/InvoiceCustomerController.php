<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\InvoiceCustomerServiceInterface;
use App\Interfaces\ICrud;
use App\Interfaces\IValidate;
use App\Common\HttpStatusCodeHelper;
use Route;

class InvoiceCustomerController extends Controller implements ICrud, IValidate
{
    private $table_name;

    protected $invoiceCustomerService;

    public function __construct(InvoiceCustomerServiceInterface $invoiceCustomerService)
    {
        $this->invoiceCustomerService = $invoiceCustomerService;

        $this->table_name = 'invoice_customer';
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
        $data      = $request->input($this->table_name);
        $validates = $this->validateInput($data);
        if (!$validates['status'])
            return response()->json(['msg' => $validates['errors']], HttpStatusCodeHelper::$unprocessableEntity);

        if (!$this->createOne($data))
            return response()->json(['msg' => ['Create failed!']], HttpStatusCodeHelper::$unprocessableEntity);
        $arr_datas = $this->readAll();
        return response()->json($arr_datas, HttpStatusCodeHelper::$created);
    }

    public function putUpdateOne(Request $request)
    {
        $data      = $request->input($this->table_name);
        $validates = $this->validateInput($data);
        if (!$validates['status'])
            return response()->json(['msg' => $validates['errors']], HttpStatusCodeHelper::$unprocessableEntity);

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

        $validate_data = $this->deactivateOne($id);
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
        return $this->invoiceCustomerService->readAll();
    }

    public function readOne($id)
    {
        return $this->invoiceCustomerService->readOne($id);
    }

    public function createOne($data)
    {
        return $this->invoiceCustomerService->createOne($data);
    }

    public function updateOne($data)
    {
        return $this->invoiceCustomerService->updateOne($data);
    }

    public function deactivateOne($id)
    {
        return $this->invoiceCustomerService->deactivateOne($id);
    }

    public function deleteOne($id)
    {
        return $this->invoiceCustomerService->deleteOne($id);
    }

    public function searchOne($filter)
    {
        return $this->invoiceCustomerService->searchOne($filter);
    }

    /** ===== VALIDATION ===== */
    public function validateInput($data)
    {
        if (!$this->validateEmpty($data))
            return ['status' => false, 'errors' => 'Dữ liệu không hợp lệ.'];

        $msgs = $this->validateLogic($data);
        return $msgs;
    }

    public function validateEmpty($data)
    {
        return true;
    }

    public function validateLogic($data)
    {
        $msg_error = [];

        $skip_id = isset($data['id']) ? [$data['id']] : [];

        return [
            'status' => count($msg_error) > 0 ? false : true,
            'errors' => $msg_error
        ];
    }

    /** ===== MY API FUNCTION ===== */
    public function getReadByCustomerIdAndType2()
    {
        $customer_id = Route::current()->parameter('customer_id');
        $one         = $this->readByCustomerIdAndType2($customer_id, ['']);
        return response()->json($one, HttpStatusCodeHelper::$ok);
    }

    public function getComputeByTransportIds()
    {
        $data          = (array)json_decode($_GET['query']);
        $transport_ids = $data['transport_ids'];
        $arr_datas     = $this->computeByTransportIds($transport_ids);
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    public function getComputeByInvoiceId()
    {
        $invoice_id = Route::current()->parameter('invoice_id');

        $validates = $this->computeByInvoiceId($invoice_id, true);
        if (!$validates['status'])
            return response()->json(['msg' => $validates['errors']], HttpStatusCodeHelper::$unprocessableEntity);
        return response()->json($validates['data'], HttpStatusCodeHelper::$ok);
    }

    public function getReadByPaymentDate()
    {
        $arr_datas     = $this->readByPaymentDate(null);
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    /** ===== MY FUNCTION ===== */
    private function readByCustomerIdAndType2($customer_id, $type2)
    {
        return $this->invoiceCustomerService->readByCustomerIdAndType2($customer_id, $type2);
    }

    private function computeByTransportIds($transport_ids)
    {
        return $this->invoiceCustomerService->computeByTransportIds($transport_ids);
    }

    private function computeByInvoiceId($invoice_id, $validate)
    {
        return $this->invoiceCustomerService->computeByInvoiceId($invoice_id, $validate);
    }

    private function readByPaymentDate($payment_date)
    {
        return $this->invoiceCustomerService->readByPaymentDate($payment_date);
    }

}
