<?php

namespace App\Services\Staff;

use App\Repositories\Staff\ScheduleRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ScheduleService
{
    /**
     * @var scheduleRepository
     */
    protected $scheduleRepository;

    /**
     * ScheduleRepository constructor.
     * 
     * @param ScheduleRepository $scheduleRepository
     */
    public function __construct(ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }
    public function get_schedule($chainstore_id, $date)
    {
        try {
            return $this->scheduleRepository->get_list_schedule($chainstore_id, $date);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    public function create_schedule($data, $staff)
    {
        $validator = Validator::make($data, [
            'customername' => 'required|string',
            'phone' => 'required|regex:/(03)[0-9]{8}/',
            'table_id' => 'required',
            'time' => 'required',
            'note' => 'max:1000',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        try {
            return $this->scheduleRepository->create_schedule($data, $staff);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    public function delete($booking, $chainstore_id)
    {
        try {
            return $this->scheduleRepository->delete($booking, $chainstore_id);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}