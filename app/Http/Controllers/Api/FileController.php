<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\FileServiceInterface;
use App\Common\Interfaces\ICrud;
use App\Common\Helpers\HttpStatusCodeHelper;
use Route;

class FileController extends Controller implements ICrud
{
    private $table_name;

    protected $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;

        $this->table_name = 'file';
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

        $arr_datas = $this->readAll();
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
        return $this->fileService->readAll();
    }

    public function readOne($id)
    {
        return $this->fileService->readOne($id);
    }

    public function createOne($data)
    {
        return $this->fileService->createOne($data);
    }

    public function updateOne($data)
    {
        return $this->fileService->updateOne($data);
    }

    public function deactivateOne($id)
    {
        return $this->fileService->deactivateOne($id);
    }

    public function deleteOne($id)
    {
        return $this->fileService->deleteOne($id);
    }

    public function searchOne($filter)
    {
        return $this->fileService->searchOne($filter);
    }

    /** ===== MY FUNCTION ===== */
    public function postCreateMultiple(Request $request)
    {
        $data               = [];
        $data['table_name'] = $request->input('table_name');
        $data['table_id']   = $request->input('table_id');

        $files = $request->allFiles()['files'];

        $validate_data = $this->createMultiple($data, $files);
        if (!$validate_data['status'])
            return response()->json(['errors' => $validate_data['errors']], HttpStatusCodeHelper::$unprocessableEntity);

        $arr_datas = $this->readAll();
        return response()->json($arr_datas, HttpStatusCodeHelper::$created);
    }

    public function createMultiple($data, $files)
    {
        return $this->fileService->createMultiple($data, $files);
    }

    public function getDownloadOne(Request $request)
    {
        $id  = Route::current()->parameter('id');

        $data = $this->downloadOne($id);

        return response()->download($data['path_to_file'], $data['name'], $data['headers']);
    }

    public function downloadOne($id)
    {
        return $this->fileService->downloadOne($id);
    }
}
