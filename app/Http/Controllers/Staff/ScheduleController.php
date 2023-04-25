<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Staff\ScheduleService;
use TheSeer\Tokenizer\Exception;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function get_schedule(Request $request, $date)
    {
        $chainstore_id = $request->user()->chain_store_id;
        try {
            $result = $this->scheduleService->get_schedule($chainstore_id, $date);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }

    public function create(Request $request)
    {
        $data = $request->only([
            'customername',
            'phone',
            'table_id',
            'time',
            'note',
        ]);

        try {
            $staff = $request->user();
            $result = $this->scheduleService->create_schedule($data, $staff);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }
    public function delete(Request $request)
    {
        $booking = $request->only('booking_id');
        $chainstore_id = $request->user()->chain_store_id;

        try {
            $result = $this->scheduleService->delete($booking, $chainstore_id);
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