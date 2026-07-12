<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
        'website',
        'contact',
        'address',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
