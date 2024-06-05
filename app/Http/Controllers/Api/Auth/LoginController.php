<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;



class LoginController extends Controller
{
    public function index(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $credentials = $request->only('email', 'password');
        if(!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email or Password is incorrect'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'user' => auth()->guard('api')->user()->only(['name', 'email']),
            'token' => $token,
        ], 200);


    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        //create user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);


        if($user) {
            //return success with Api Resource
            return response()->json([
                'status' => true,
                'message' => 'Successfully Registered!',
                'user' => $user
            ], 200);
        }

        //return failed with Api Resource
        if($user) {
            //return success with Api Resource
            return response()->json([
                'status' => false,
                'message' => 'Register Unsuccess!',
            ], 400);
        }
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Logout Success'
        ], 200);
    }
}
