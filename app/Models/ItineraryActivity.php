<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'itinerary_day_id',
        'time_from',
        'time_to',
        'activity',
        'sort_order',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(ItineraryDay::class, 'itinerary_day_id');
    }
}
