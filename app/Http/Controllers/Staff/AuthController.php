<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use Auth;
use Validator;

class AuthController extends Controller
{
    public static function staffs() {
        return new Staff();
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'    => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Nhập tên đăng nhập và mật khẩu.',
            ];
            return response()->json($response, 400);
        }

        $credentials = $request->only('username', 'password');  

        if (Auth::guard('staff')->attempt($credentials)) {
            $staff = Auth::guard('staff')->user();
            $token = $staff->createToken('staff')->plainTextToken;

            $response = [
                'success' => true,
                'user_info' => $staff,
                'token' => $token,
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu sai!',
            ];

            return response()->json($response, 401);
        }
    }

    public function getCurrentStaff(Request $request)
    {
        $staff = $request->user();
        $response = [
            'success' => true,
            'data' => [
                'id' => $staff->id,
                'username' => $staff->username,
                'role'=> $staff->role,
                'chain_store_id'=> $staff->chain_store_id,
            ],
        ];

        return response()->json($response);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        $response = [
            'success' => true,
            'message' => 'Đã đăng xuất.',
        ];

        return response()->json($response);
    }
}
