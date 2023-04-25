<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'time',
        'status',
        'note',
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

    public static function checkBookingInChainstore($booking_id, $chainstore_id)
    {
        $booking = Booking::find($booking_id);
        if (is_null($booking))
            return false;

        $chains_id = $booking->staff->chain_store_id;
        if ($chains_id == $chainstore_id)
            return true;

        return false;
    }
}