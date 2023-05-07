<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Booking;
use Auth;
use Validator;

class ConfirmController extends Controller
{
  public static function customers()
  {
    return new Customer();
  }

  public function login(Request $request)
  {
    $phone = $request->input('phone');
    $validator = Validator::make(['phone' => $phone], [
      'phone' => 'required|regex:/(03)[0-9]{8}/',
    ], [
        'phone.required' => 'Vui lòng nhập số điện thoại!',
        'phone.regex' => 'Số điện thoại không hợp lệ!',
      ]);

    if ($validator->fails()) {
      $errors = $validator->messages()->all();

      return response()->json([
        'success' => false,
        'message' => $errors,
      ], 400);
    }

    $customer = Auth::guard('customer')->getProvider()->retrieveByCredentials([
      'phone' => $phone,
    ]);

    if (!$customer) {
      return response()->json([
        'success' => false,
        'message' => 'Vui lòng xác nhận lại thông tin với nhân viên nhà hàng.',
      ], 400);
    }

    $booking = Booking::checkCustomerArrived($customer->id);

    if ($booking === false) {
      return response()->json([
        'success' => false,
        'message' => 'Vui lòng xác nhận lại thông tin với nhân viên nhà hàng.',
      ], 400);
    }
    $customer->role = 'customer';
    $token = $customer->createToken('customer')->plainTextToken;

    return response()->json([
      'success' => true,
      'token' => $token,
    ], 200);
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