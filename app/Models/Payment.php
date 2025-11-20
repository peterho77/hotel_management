<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payment";

    protected $fillable = [
        'booking_id',
        'amount',
        'instrument',
        'type',
        'status',
        'transaction_id',
        'transaction_type',
        'vnp_transaction_no',
        'vnp_txn_ref',
        'vnp_bank_code',
        'vnp_card_type',
        'vnp_response_code',
        'vnp_pay_date'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
