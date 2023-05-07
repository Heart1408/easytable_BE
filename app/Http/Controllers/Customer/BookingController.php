<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Dish;
use App\Models\Bill;
use Validator;

class BookingController extends Controller
{
    public static function customers()
    {
        return new Customer();
    }

    public function get_booking(Request $request)
    {
        $customer = $request->user();
        $booking_id = Booking::checkCustomerArrived($customer->id);
        $booking = Booking::with([
            'customer' => function ($query) {
                $query->select('id', 'fullname');
            }
        ])->where('id', $booking_id)
            ->first();

        $dishes = $booking->dishes()->withPivot('quantity')->get();
        $orderable = $dishes->contains(function ($dish) {
            return $dish->pivot->status === Bill::STATUS['not_ordered'];
        });

        $response = [
            'success' => true,
            'data' => [
                'booking_id' => $booking_id,
                'customer_name' => $booking->customer->fullname,
                'status' => $booking->status,
                'rate' => $booking->rate,
                'comment' => $booking->comment,
                'orderable' => $orderable,
                'bill' => $dishes,
            ],
        ];

        return response()->json($response, 200);
    }

    public function add(Request $request, $dish_id)
    {
        $dish = Dish::find($dish_id);
        if (!$dish || !$dish_id)
            return response()->json([
                'success' => false,
                'message' => 'Món ăn không hợp lệ!',
            ], 400);


        $customer = $request->user();
        $booking_id = Booking::checkCustomerArrived($customer->id);

        if (Booking::find($booking_id)->status === Booking::STATUS['paid'])
            return response()->json([
                'success' => false,
                'message' => 'Lỗi!',
            ], 400);

        $bill = Bill::where('booking_id', $booking_id)
            ->where('dish_id', $dish_id)
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->first();

        if (!$bill || $bill->status === Bill::STATUS['ordered']) {
            $bill = Bill::create([
                'booking_id' => $booking_id,
                'dish_id' => $dish_id,
                'quantity' => 1,
                'status' => Bill::STATUS['not_ordered'],
            ]);
        } else {
            $quantity = $bill->quantity + 1;
            $bill->update(['quantity' => $quantity]);
        }

        return response()->json([
            'success' => true,
            'message' => "Đã thêm {$dish->name} vào danh sách gọi món.",
        ], 200);
    }

    public function delete(Request $request, $dish_id)
    {
        $booking_id = Booking::checkCustomerArrived($request->user()->id);
        $dish = Dish::findOrFail($dish_id);

        try {
            $bill = Bill::where('booking_id', $booking_id)
                ->where('dish_id', $dish_id)
                ->where('status', Bill::STATUS['not_ordered'])
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->first();
            if (!$bill)
                return response()->json([
                    'success' => false,
                    'message' => "Không thể xóa {$dish->name} ra khỏi danh sách gọi món.",
                ], 200);

            $bill->delete();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$dish->name} ra khỏi danh sách gọi món.",
            ], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Món ăn không hợp lệ!',
            ], 400);
        }
    }

    public function confirm_order(Request $request)
    {
        $list_ordered = $request->input('listOrdered');
        $booking_id = $list_ordered[0]['pivot']['booking_id'];

        $dishes = [];
        foreach ($list_ordered as $dish) {
            if (!isset($dishes[$dish['id']])) {
                $dishes[$dish['id']] = 0;
            }
            $dishes[$dish['id']] += $dish['pivot']['quantity'];
        }

        foreach ($dishes as $dish_id => $total_quantity) {
            $bills = Bill::where('booking_id', $booking_id)
                ->where('dish_id', $dish_id)
                ->get();
            $keptBillId = null;
            foreach ($bills as $bill) {
                if ($keptBillId === null) {
                    $keptBillId = $bill->id;
                    $bill->update(['status' => Bill::STATUS['ordered'], 'quantity' => $total_quantity]);
                } else {
                    $bill->delete();
                }
            }

            // Xóa các bản ghi trùng lặp ngoại trừ bản ghi đầu tiên được giữ lại
            Bill::where('id', '!=', $keptBillId)
                ->where('dish_id', $dish_id)
                ->where('booking_id', $booking_id)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => "Đặt món thành công.",
        ], 200);
    }

    public function send_feedback(Request $request)
    {
        $requestData = $request->only('bookingId', 'rate', 'comment');

        $validator = Validator::make($requestData, [
            'bookingId' => 'required|integer',
            'rate' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ], [
                'bookingId.required' => 'Lỗi!',
                'rate.required' => 'Vui lòng chọn số sao đánh giá!',
                'comment.required' => 'Vui lòng nhập phản hồi của bạn!'
            ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();

            return response()->json([
                'success' => false,
                'message' => $errors,
            ], 400);
        }

        $booking_id = $requestData['bookingId'];
        $rate = $requestData['rate'];
        $comment = $requestData['comment'];

        $booking = Booking::findOrFail($booking_id);
        if ($booking->status !== Booking::STATUS['paid'])
            return response()->json([
                'success' => false,
                'message' => "Chưa thể gửi phản hồi lúc này.",
            ], 400);

        if ($booking->rate !== null)
            return response()->json([
                'success' => false,
                'message' => "Bạn đã gửi phản hồi.",
            ], 400);

        $booking->update(['rate' => $rate, 'comment' => $comment, 'feedback_status' => Booking::FEEDBACK_STATUS['unseen']]);

        return response()->json([
            'success' => true,
            'message' => "Gửi phản hồi thành công.",
        ], 200);
    }
}