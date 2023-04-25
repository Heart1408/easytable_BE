<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;
use App\Models\Booking;
use App\Models\TableDetail;

class CustomerController extends Controller
{
    public function get_info(Request $request, $booking_id)
    {
        try {
            $chainstore_id = $request->user()->chain_store_id;
            if (Booking::checkBookingInChainstore($booking_id, $chainstore_id)) {

                $booking = Booking::find(6);

                $customer = $booking->customer;
                $data = [
                    'fullname' => $customer->fullname,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'birthday' => $customer->birthday,
                ];

                $floor_id = $booking->table_detail->floor_id;
                $data['floor_id'] = $floor_id;

                $result = [
                    'success' => true,
                    'data' => $data,
                ];
            } else {
                $result = [
                    'success' => false,
                    'message' => "Lỗi!",
                ];
            }
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }
}