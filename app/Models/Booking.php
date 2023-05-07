<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    const STATUS = ['not arrived' => 1, 'arrived' => 2, 'cancelled' => 3, 'paid' => 4];
    const FEEDBACK_STATUS = ['unseen' => 1, 'seen' => 2];
    const BookingCutoffDate = 7;

    protected $fillable = [
        'customer_id',
        'time',
        'status',
        'note',
        'rate',
        'comment',
        'feedback_status',
        'table_detail_id',
        'staff_id',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table_detail()
    {
        return $this->belongsTo(TableDetail::class);
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'bills')->withPivot('quantity', 'status');
    }

    // public static function checkBookingInChainstore($booking_id, $chainstore_id)
    // {
    //     $booking = Booking::find($booking_id);
    //     if (is_null($booking))
    //         return false;

    //     $chains_id = $booking->staff->chain_store_id;
    //     if ($chains_id == $chainstore_id)
    //         return true;

    //     return false;
    // }

    public static function checkBookingInChainstore($booking_id, $chainstore_id)
    {
        $booking = Booking::find($booking_id);
        if (!$booking) {
            return false;
        }

        return $booking->staff->chain_store_id == $chainstore_id;
    }

    public static function checkAddSchedule($time, $table_id)
    {
        $time = new DateTime($time);

        $now = Carbon::now();
        $sevenDaysAgo = $now->addDays(self::BookingCutoffDate);
        if ($time > $sevenDaysAgo)
            return false;

        $date = $time->format('Y-m-d');
        $hour = $time->format('H');

        $booking = Booking::where('table_detail_id', $table_id)
            ->where(DB::raw('DATE(time)'), $date)
            ->where(function ($query) use ($hour) {
                $query->where(DB::raw('HOUR(time)'), $hour)
                    ->orWhere(DB::raw('HOUR(time)'), $hour - 1)
                    ->orWhere(DB::raw('HOUR(time)'), $hour + 1);
            })->exists();

        if ($booking)
            return false;
        else
            return true;
    }

    public static function existedCustomerInSchedule($time, $customer_id)
    {
        $time = new DateTime($time);
        $date = $time->format('Y-m-d');

        $existedCus = Booking::where(DB::raw('DATE(time)'), $date)
            ->where('customer_id', $customer_id)->where('status', '<>', self::STATUS['paid'])->first();

        if ($existedCus)
            return true;
        return false;
    }

    public static function checkCustomerArrived($customer_id)
    {
        $time = new DateTime();
        $date = $time->format('Y-m-d');
        $booking_id = Booking::where('customer_id', $customer_id)
            ->where(function ($query) {
                $query->where('status', Booking::STATUS['arrived'])
                    ->orWhere('status', Booking::STATUS['paid']);
            })
            ->where(DB::raw('DATE(time)'), $date)
            ->pluck('id')
            ->first();

        return $booking_id ?: false;
    }

    public function scopefeedbackSearch($query, $data)
    {
        $chainstore_id = $data['chainstore_id'];
        $query = Booking::whereHas('staff', function ($q) use ($chainstore_id) {
            $q->where('chain_store_id', '=', $chainstore_id);
        })->whereNotNull('rate');

        if (isset($data['year'])) {
            $query->whereYear("updated_at", $data['year']);

            if (isset($data['month'])) {
                $query->whereMonth("updated_at", $data['month']);

                if (isset($data['day'])) {
                    $daysInMonth = Carbon::createFromDate($data['year'], $data['month'], 1)->daysInMonth;
                    $day = min($data['day'] ?? $daysInMonth, $daysInMonth);
                    $query->whereDay("updated_at", $day);
                }
            }
        }

        if (isset($data['rate'])) {
            $query->where('rate', $data['rate']);
        }

        if (isset($data['status'])) {
            $query->where('feedback_status', $data['status']);
        }

        return $query->with('customer:id,fullname')->orderBy('feedback_status')->orderByDesc('updated_at')->get();
    }
}