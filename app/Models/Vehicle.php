<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'make',
        'model',
        'color',
        'photo_path',
        'mitra_id',
        'price_per_day',
        'sort_order',
    ];
    protected $casts = [
        'price_per_day' => 'decimal:2',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function mitras()
    {
        return $this->belongsToMany(Mitra::class, 'mitra_vehicle')->withTimestamps();
    }
}
