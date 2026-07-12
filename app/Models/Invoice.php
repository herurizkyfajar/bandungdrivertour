<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'invoice_number',
        'amount',
        'status',
        'issued_at',
        'due_date',
        'manual_invoice_path',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'due_date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
