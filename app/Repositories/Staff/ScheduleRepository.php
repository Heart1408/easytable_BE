<?php

namespace App\Repositories\Staff;

use App\Models\Customer;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class ScheduleRepository
{
    protected $customers;
    public function __construct(Customer $customers)
    {
        $this->customers = $customers;
    }

    public function get_list_schedule($chainstore_id, $date)
    {
        $data = Booking::where(DB::raw('DATE(time)'), $date)->whereHas(
            'staff',
            function ($query) use ($chainstore_id) {
                $query->where('chain_store_id', $chainstore_id);
            }
        )->get();

        return [
            'success' => true,
            'data' => $data,
        ];
    }

    public function create_schedule($data, $staff)
    {
        $customer = Customer::where('phone', $data['phone'])->first();
        if (is_null($customer)) {
            $newCustomer = new Customer;
            $newCustomer->fullname = $data['customername'];
            $newCustomer->phone = $data['phone'];
            $customer = $newCustomer->save();
        }

        Booking::create([
            'customer_id' => $customer->id,
            'time' => $data['time'],
            'note' => $data['note'],
            'table_detail_id' => $data['table_id'],
            'staff_id' => $staff->id,
        ]);

        return [
            'success' => true,
            'message' => "Thêm lịch đặt bàn thành công!",
        ];
    }

    public function delete($data, $chainstore_id)
    {
        $booking = Booking::find($data['booking_id']);
        if (is_null($booking)) {
            return [
                'success' => false,
                'message' => 'Lỗi!',
            ];
        }

        if (Booking::checkBookingInChainstore($data['booking_id'], $chainstore_id)) {
            $booking->delete();
            return [
                'success' => true,
                'message' => 'Xóa thành công!',
            ];
        }

        return [
            'success' => false,
            'message' => 'Lỗi!',
        ];
    }
}