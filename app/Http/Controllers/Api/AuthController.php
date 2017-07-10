<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\AuthServiceInterface;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /** API METHOD */
    public function postAuthentication(Request $request)
    {
        $arr_datas = $this->authService->authentication($request->input('user'));
        return response()->json($arr_datas, $arr_datas['status_code']);
    }

    public function getAuthorization()
    {
        $arr_datas = $this->authService->authorization();
        return response()->json($arr_datas, $arr_datas['status_code']);
    }

    /** LOGIC METHOD */
}
