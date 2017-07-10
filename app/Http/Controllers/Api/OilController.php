<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\OilServiceInterface;
use App\Common\Interfaces\ICrud;
use App\Common\Helpers\HttpStatusCodeHelper;
use Route;

class OilController extends Controller implements ICrud
{
    private $table_name;

    protected $oilService;

    public function __construct(OilServiceInterface $oilService)
    {
        $this->oilService = $oilService;

        $this->table_name = 'oil';
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

        $validate_data = $this->createOne($data);
        if (!$validate_data['status'])
            return response()->json(['errors' => $validate_data['errors']], HttpStatusCodeHelper::$unprocessableEntity);

        $arr_datas = $this->readAll();
        return response()->json($arr_datas, HttpStatusCodeHelper::$created);
    }

    public function putUpdateOne(Request $request)
    {
        $data      = $request->input($this->table_name);

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
        return $this->oilService->readAll();
    }

    public function readOne($id)
    {
        return $this->oilService->readOne($id);
    }

    public function createOne($data)
    {
        return $this->oilService->createOne($data);
    }

    public function updateOne($data)
    {
        return $this->oilService->updateOne($data);
    }

    public function deactivateOne($id)
    {
        return $this->oilService->deactivateOne($id);
    }

    public function deleteOne($id)
    {
        return $this->oilService->deleteOne($id);
    }

    public function searchOne($filter)
    {
        return $this->oilService->searchOne($filter);
    }

    /** ===== MY FUNCTION ===== */
    public function getReadByApplyDate()
    {
        $data = (array)json_decode($_GET['query']);
        $one  = $this->readByApplyDate($data['apply_date']);
        return response()->json($one, HttpStatusCodeHelper::$ok);
    }

    private function readByApplyDate($apply_date)
    {
        return $this->oilService->readByApplyDate($apply_date);
    }
}
