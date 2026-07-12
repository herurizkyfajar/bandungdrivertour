<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'whatsapp_contact',
        'photo_path',
        'apps',
        'other_app',
    ];

    protected $casts = [
        'apps' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'mitra_vehicle')->withTimestamps();
    }
}
