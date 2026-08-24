<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public const KANBAN_PHASES = [
        'masuk' => [
            'label' => 'Booking Masuk',
            'statuses' => ['baru_masuk'],
            'card_class' => 'phase-masuk',
            'badge_class' => 'phase-badge phase-badge--muted',
        ],
        'proses' => [
            'label' => 'Booking Proses',
            'statuses' => ['konfirmasi', 'dijadwalkan'],
            'card_class' => 'phase-proses',
            'badge_class' => 'phase-badge phase-badge--warning',
        ],
        'cancel' => [
            'label' => 'Booking Cancel',
            'statuses' => ['cancelled', 'cancel', 'batal'],
            'card_class' => 'phase-cancel',
            'badge_class' => 'phase-badge phase-badge--danger',
        ],
        'selesai' => [
            'label' => 'Booking Selesai',
            'statuses' => ['selesai_pelayanan', 'selesai_administrasi_fee'],
            'card_class' => 'phase-selesai',
            'badge_class' => 'phase-badge phase-badge--success',
        ],
    ];

    protected $fillable = [
        'customer_name',
        'contact_number',
        'number_of_passengers',
        'country_of_origin',
        'pickup_location',
        'pickup_address_en',
        'booking_date',
        'end_date',
        'pickup_time',
        'vehicle_id',
        'service_id',
        'mitra_id',
        'itinerary_id',
        'group_id',
        'travel_plans',
        'info_source',
        'info_source_other',
        'payment_plan',
        'down_payment_amount',
        'price',
        'pendapatan',
        'status',
        'created_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'end_date' => 'date',
        'pickup_time' => 'datetime:H:i',
        'down_payment_amount' => 'decimal:2',
        'price' => 'decimal:2',
        'pendapatan' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_service');
    }

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function kanbanPhase(): string
    {
        $status = (string) ($this->status ?? 'baru_masuk');

        foreach (self::KANBAN_PHASES as $phase => $definition) {
            if (in_array($status, $definition['statuses'], true)) {
                return $phase;
            }
        }

        return 'masuk';
    }

    public function kanbanPhaseLabel(): string
    {
        return self::KANBAN_PHASES[$this->kanbanPhase()]['label'] ?? 'Booking Masuk';
    }

    public function kanbanCardClass(): string
    {
        return self::KANBAN_PHASES[$this->kanbanPhase()]['card_class'] ?? 'phase-masuk';
    }

    public function kanbanBadgeClass(): string
    {
        return self::KANBAN_PHASES[$this->kanbanPhase()]['badge_class'] ?? 'phase-badge phase-badge--muted';
    }

    public function statusLabel(): string
    {
        return match ((string) ($this->status ?? 'baru_masuk')) {
            'baru_masuk' => 'Baru Masuk',
            'konfirmasi' => 'Konfirmasi',
            'dijadwalkan' => 'Dijadwalkan',
            'cancelled' => 'Cancel',
            'cancel' => 'Cancel',
            'batal' => 'Cancel',
            'selesai_pelayanan' => 'Selesai Pelayanan',
            'selesai_administrasi_fee' => 'Selesai Administrasi Fee',
            'confirmed' => 'Konfirmasi',
            'completed' => 'Selesai Pelayanan',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public static function phaseStatuses(string $phase): array
    {
        return self::KANBAN_PHASES[$phase]['statuses'] ?? [];
    }
}
