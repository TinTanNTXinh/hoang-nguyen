<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileServiceInterface;
use Route;

class TestController extends Controller
{
    protected $fileService;

    public function __construct(FileServiceInterface $fileService)
    {
        $this->fileService = $fileService;
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
