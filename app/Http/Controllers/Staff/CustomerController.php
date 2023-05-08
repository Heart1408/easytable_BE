<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;
use App\Models\Booking;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function get_list(Request $request)
    {
        try {
            $data = $request->only('search_key');
            $data['chainstore_id'] = $request->user()->chain_store_id;
            $customers = Customer::search($data);

            $result = [
                'success' => true,
                'data' => $customers,
            ];

        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }
    public function get_info(Request $request, $booking_id)
    {
        try {
            $chainstore_id = $request->user()->chain_store_id;
            if (Booking::checkBookingInChainstore($booking_id, $chainstore_id)) {
                $booking = Booking::find($booking_id);
                $customer = $booking->customer;

                $data = [
                    'fullname' => $customer->fullname,
                    'phone' => $customer->phone,
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

    public function get_booking_info(Request $request)
    {
        $chainstore_id = $request->user()->chain_store_id;
        $data = $request->all();
        $data['chainstore_id'] = $chainstore_id;
        if (!isset($data['customerId'])) {
            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'Lỗi!',
            ]);
        }

        try {
            $booking = Booking::getCustomerBookings($data)->get();

            return response()->json([
                'status' => 200,
                'success' => true,
                'data' => $booking,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}