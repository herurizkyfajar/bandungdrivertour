<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpjManual extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_name',
        'customer_name',
        'customer_contact',
        'country_of_origin',
        'passenger_count',
        'start_date',
        'pickup_time',
        'pickup_address',
        'service_type',
        'service_duration',
        'payment_plan',
        'trip_details',
    ];

    protected $casts = [
        'start_date' => 'date',
        'passenger_count' => 'integer',
    ];
}
