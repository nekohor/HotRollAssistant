<?php
/**
 * File AuthController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */
namespace App\Backend\Http\Controllers;

use App\Backend\Http\JsonResponse;
use App\Backend\Http\Resources\UserResource;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // $credentials = $request->only('email', 'password');
        $credentials = [];
        $credentials['name'] = $request->input('username');
        $credentials['password'] = $request->input('password');
        
        if ($token = $this->guard()->attempt($credentials)) {
            return response()->json(new UserResource(Auth::user()), Response::HTTP_OK)->header('Authorization', $token);
        }

        return response()->json(new JsonResponse([], 'login_error'), Response::HTTP_UNAUTHORIZED);
    }

    public function logout()
    {
        $this->guard()->logout();
        return response()->json((new JsonResponse())->success([]), Response::HTTP_OK);
    }

    public function user()
    {
        return new UserResource(Auth::user());
    }

    /**
     * @return mixed
     */
    private function guard()
    {
        return Auth::guard();
    }
}
