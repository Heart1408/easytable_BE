<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Floor;
use App\Models\TableDetail;
use App\Models\Booking;
use App\Services\Staff\TableService;
use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;
use DB;

class TableController extends Controller
{
    protected $tableService;

    public function __construct(TableService $tableService)
    {
        $this->tableService = $tableService;
    }

    public function get_list_floor(Request $request)
    {
        $chainstore_id = $request->user()->chain_store_id;
        try {
            $data = Floor::select('id', 'name')->where('chain_store_id', $chainstore_id)->get();
            $result = [
                'success' => true,
                'data' => $data,
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
    public function get_list_table(Request $request, $floor_id)
    {
        try {
            $chainstore_id = $request->user()->chain_store_id;
            $floor = Floor::find($floor_id);

            if (is_null($floor) || $floor['chain_store_id'] != $chainstore_id) {
                $result = [
                    'success' => false,
                    'message' => "Lỗi!",
                ];
            } else {
                $list_table = TableDetail::where('floor_id', $floor_id)->get();
                $result = [
                    'success' => true,
                    'data' => $list_table,
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

    public function get_status_current(Request $request, $table_id)
    {
        try {
            $chainstore_id = $request->user()->chain_store_id;
            $table = TableDetail::find($table_id);

            if (is_null($table) || $table->floor->chain_store_id != $chainstore_id) {
                return response()->json([
                    'success' => false,
                    'message' => "Lỗi!",
                ]);
            } else {
                $today = now()->format('Y-m-d');
                $booking = Booking::with([
                    'customer' => function ($query) {
                        $query->select('id', 'fullname', 'phone');
                    }
                ])->where('table_detail_id', $table_id)
                    ->where(DB::raw('DATE(time)'), $today)
                    ->where(function ($query) {
                        $query->where('status', Booking::STATUS['arrived'])
                            ->orWhere('status', Booking::STATUS['paid']);
                    })->first();

                $bill = $booking->dishes()->wherePivot('status', '!=', Bill::STATUS['not_ordered'])->get();

                $count = $booking->dishes()->wherePivot('status', '!=', Bill::STATUS['servered'])->count();
                $payment_confirmable = ($count == 0);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'booking_id' => $booking->id,
                        'customer_name' => $booking->customer->fullname,
                        'customer_phone' => $booking->customer->phone,
                        'status' => $booking->status,
                        'rate' => $booking->rate,
                        'comment' => $booking->comment,
                        'payment_confirmable' => $payment_confirmable,
                        'bill' => $bill,
                    ],
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update_status_bill(Request $request)
    {
        try {
            $list_servered_id = $request->input('serveredListId');
            $booking_id = $request->input('bookingId');

            foreach ($list_servered_id as $dish_id) {
                Booking::find($booking_id)->dishes()->updateExistingPivot($dish_id, ['status' => Bill::STATUS['servered']]);
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Cập nhật thành công.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function payment_confirm(Request $request)
    {
        try {
            $booking_id = $request->input('bookingId');
            $booking = Booking::findOrFail($booking_id);

            $count = $booking->dishes()->wherePivot('status', '!=', Bill::STATUS['servered'])->count();
            if ($count > 0) {
                return response()->json([
                    'status' => 400,
                    'success' => false,
                    'message' => 'Chưa thể xác nhận thanh toán.',
                ]);
            }

            $booking->update(['status' => Booking::STATUS['paid']]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Xác nhận thanh toán thành công.',
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