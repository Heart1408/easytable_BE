<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Staff\TableService;
use TheSeer\Tokenizer\Exception;
use App\Models\Floor;
use App\Models\TableDetail;
use App\Models\Booking;
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
                $data = Booking::where('table_detail_id', $table_id)
                    ->where(DB::raw('DATE(time)'), $today)
                    ->where('status', 2)->first();

                return response()->json([
                    'success' => true,
                    'data' => $data,
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
}