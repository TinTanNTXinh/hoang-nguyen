<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\GarageServiceInterface;
use App\Interfaces\ICrud;
use App\Interfaces\IValidate;
use App\Common\HttpStatusCodeHelper;
use Route;

class GarageController extends Controller implements ICrud, IValidate
{
    private $table_name;

    protected $garageService;

    public function __construct(GarageServiceInterface $garageService)
    {
        $this->garageService = $garageService;

        $this->table_name = 'garage';
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

        if (!$this->updateOne($data))
            return response()->json(['msg' => ['Update failed!']], HttpStatusCodeHelper::$unprocessableEntity);
        $arr_datas = $this->readAll();
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    public function patchDeactivateOne(Request $request)
    {
        $id = $request->input('id');
        if (!$this->deactivateOne($id))
            return response()->json(['msg' => 'Deactivate failed!'], HttpStatusCodeHelper::$unprocessableEntity);
        $arr_datas = $this->readAll();
        return response()->json($arr_datas, HttpStatusCodeHelper::$ok);
    }

    public function deleteDeleteOne(Request $request)
    {
        $id = Route::current()->parameter('id');
        if (!$this->deleteOne($id))
            return response()->json(['msg' => 'Delete failed!'], HttpStatusCodeHelper::$unprocessableEntity);
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
        return $this->garageService->readAll();
    }

    public function readOne($id)
    {
        return $this->garageService->readOne($id);
    }

    public function createOne($data)
    {
        return $this->garageService->createOne($data);
    }

    public function updateOne($data)
    {
        return $this->garageService->updateOne($data);
    }

    public function deactivateOne($id)
    {
        return $this->garageService->deactivateOne($id);
    }

    public function deleteOne($id)
    {
        return $this->garageService->deleteOne($id);
    }

    public function searchOne($filter)
    {
        return $this->garageService->searchOne($filter);
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

    /** ===== MY FUNCTION ===== */
}
