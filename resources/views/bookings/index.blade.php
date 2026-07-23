@extends('layouts.app', ['title' => 'Manage Bookings'])

@php
  $bookingBoardData = $bookings->mapWithKeys(function ($booking) {
      $raw = (string) ($booking->contact_number ?? '');
      $digits = preg_replace('/\D+/', '', $raw);
      $country = strtolower(trim((string) ($booking->country_of_origin ?? '')));
      $countryCodeMap = [
          'indonesia' => '62',
          'malaysia' => '60',
          'singapore' => '65',
          'thailand' => '66',
          'vietnam' => '84',
          'philippines' => '63',
          'united states' => '1',
          'usa' => '1',
          'united kingdom' => '44',
          'uk' => '44',
          'australia' => '61',
          'japan' => '81',
          'south korea' => '82',
          'korea' => '82',
          'united arab emirates' => '971',
          'uae' => '971',
          'saudi arabia' => '966',
          'india' => '91',
      ];

      if (str_starts_with(trim($raw), '+')) {
          $whatsappNumber = $digits;
      } elseif (str_starts_with($digits, '00')) {
          $whatsappNumber = substr($digits, 2);
      } elseif (str_starts_with($digits, '0')) {
          $whatsappNumber = isset($countryCodeMap[$country])
              ? $countryCodeMap[$country] . substr($digits, 1)
              : $digits;
      } else {
          $whatsappNumber = $digits;
      }

      $vehicleName = trim((string) (($booking->vehicle?->make ?? '') . ' ' . ($booking->vehicle?->model ?? '')));
      $serviceName = (string) ($booking->service?->name ?? '-');
      $invoiceNumber = $booking->invoice?->invoice_number;
      $invoiceShowUrl = $booking->invoice ? route('invoice.show', $booking->invoice) : null;
      $invoiceDownloadUrl = $booking->invoice ? route('invoice.show', $booking->invoice) . '?download=1' : null;
      $manualInvoiceUrl = $booking->invoice?->manual_invoice_path ? asset('storage/' . $booking->invoice->manual_invoice_path) : null;
      $hasManualInvoice = !empty($manualInvoiceUrl);
      $spjShowUrl = route('spj.show', $booking);
      $spjDownloadUrl = route('spj.show', $booking) . '?download=1';
      $itineraryPdfUrl = $booking->itinerary_id ? route('itineraries.pdf', $booking->itinerary_id) : null;

      return [$booking->id => [
          'id' => $booking->id,
          'customer_name' => $booking->customer_name,
          'contact_number' => $booking->contact_number,
          'number_of_passengers' => $booking->number_of_passengers,
          'country_of_origin' => $booking->country_of_origin,
          'pickup_location' => $booking->pickup_location,
          'pickup_address_en' => $booking->pickup_address_en,
          'booking_date' => optional($booking->booking_date)->format('d M Y'),
          'booking_date_iso' => optional($booking->booking_date)->format('Y-m-d'),
          'end_date' => optional($booking->end_date)->format('d M Y'),
          'end_date_iso' => optional($booking->end_date)->format('Y-m-d'),
          'pickup_time' => \Illuminate\Support\Carbon::parse($booking->pickup_time)->format('H:i'),
          'vehicle_name' => $vehicleName !== '' ? $vehicleName : '-',
          'group_name' => $booking->group?->name ?? null,
          'service_name' => $serviceName,
          'travel_plans' => $booking->travel_plans,
          'info_source' => $booking->info_source,
          'info_source_other' => $booking->info_source_other,
          'payment_plan' => $booking->payment_plan,
          'payment_plan_label' => match ($booking->payment_plan) {
              'down_payment' => 'Down Payment',
              'payment_full_transfer' => 'Payment Full Transfer',
              'payment_full_on_driver' => 'Payment Full On Driver',
              default => (string) ($booking->payment_plan ?? '-'),
          },
          'down_payment_amount' => $booking->down_payment_amount !== null ? number_format((float) $booking->down_payment_amount, 0, ',', '.') : '-',
          'price' => number_format((float) ($booking->price ?? 0), 0, ',', '.'),
          'status' => $booking->status,
          'status_label' => $booking->statusLabel(),
          'phase' => $booking->kanbanPhase(),
          'phase_label' => $booking->kanbanPhaseLabel(),
          'card_class' => $booking->kanbanCardClass(),
          'badge_class' => $booking->kanbanBadgeClass(),
          'invoice_number' => $invoiceNumber,
          'invoice_show_url' => $invoiceShowUrl,
          'invoice_download_url' => $invoiceDownloadUrl,
          'manual_invoice_url' => $manualInvoiceUrl,
          'has_manual_invoice' => $hasManualInvoice,
          'spj_show_url' => $spjShowUrl,
          'spj_download_url' => $spjDownloadUrl,
          'itinerary_pdf_url' => $itineraryPdfUrl,
          'edit_url' => route('bookings.edit', $booking),
          'delete_url' => route('bookings.destroy', $booking),
          'phase_update_url' => route('bookings.phase.update', $booking),
          'whatsapp_url' => $whatsappNumber !== '' ? ('https://wa.me/' . $whatsappNumber . '?text=' . urlencode($booking->invoice
              ? ('Halo ' . $booking->customer_name . '. Invoice: ' . $invoiceNumber . ' Total (IDR): ' . number_format((float) ($booking->invoice->amount ?? 0), 0, ',', '.') . '. Mohon konfirmasi.')
              : ('Halo ' . $booking->customer_name . '. Mohon konfirmasi booking Anda.'))) : null,
      ]];
  });
@endphp

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; }
  .sidebar { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; height: fit-content; position: sticky; top: 76px; }
  .side-menu { list-style: none; padding: 0; margin: 0; display: grid; gap: .35rem; }
  .side-menu a { display: block; padding: .6rem .8rem; border-radius: 10px; text-decoration: none; color: var(--text); border: 1px solid #e5e7eb; }
  .side-menu a:hover, .side-menu a.active { background: #f1f5f9; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; }
  .kanban-top { display: flex; flex-wrap: wrap; gap: .75rem; justify-content: space-between; align-items: flex-start; }
  .kanban-subtitle { color: var(--muted); font-size: .95rem; margin-top: .25rem; }
  .kanban-filters { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1rem; }
  .kanban-filters .field { flex: 1 1 220px; }
  .kanban-filters input, .kanban-filters select { width: 100%; }
  .kanban-board { display: flex; gap: 1rem; margin-top: 1.25rem; overflow-x: auto; overflow-y: hidden; padding-bottom: .9rem; scroll-snap-type: x proximity; -webkit-overflow-scrolling: touch; }
  .kanban-column { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 18px; padding: .9rem; flex: 0 0 320px; width: 320px; min-width: 320px; display: flex; flex-direction: column; gap: .75rem; scroll-snap-align: start; }
  .kanban-column.is-over { outline: 2px dashed #94a3b8; outline-offset: 2px; }
  .kanban-column__header { display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
  .kanban-column__title { margin: 0; font-size: 1rem; font-weight: 700; color: #0f172a; }
  .kanban-column__count { display: inline-flex; align-items: center; justify-content: center; min-width: 2rem; padding: .2rem .55rem; border-radius: 999px; background: #e2e8f0; color: #0f172a; font-weight: 700; font-size: .85rem; }
  .kanban-column__cards { display: grid; gap: .75rem; min-height: 140px; }
  .kanban-empty { border: 1px dashed #cbd5e1; border-radius: 16px; padding: 1rem; text-align: center; color: #64748b; background: rgba(255,255,255,.65); }
  .booking-card { border-radius: 18px; border: 1px solid #dbe3ee; background: #ffffff; box-shadow: 0 8px 18px rgba(15, 23, 42, .06); padding: .9rem; cursor: grab; transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease; }
  .booking-card:hover { transform: translateY(-1px); box-shadow: 0 12px 24px rgba(15, 23, 42, .10); }
  .booking-card:active { cursor: grabbing; }
  .booking-card.is-dragging { opacity: .55; }
  .booking-card.phase-masuk { background: #ffffff; }
  .booking-card.phase-proses { background: #fff9db; border-color: #f3d66b; }
  .booking-card.phase-cancel { background: #fef2f2; border-color: #fca5a5; }
  .booking-card.phase-selesai { background: #ecfdf3; border-color: #86efac; }
  .booking-card__top { display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem; margin-bottom: .7rem; }
  .booking-card__name { margin: 0; font-size: 1.02rem; font-weight: 800; color: #0f172a; line-height: 1.3; }
  .booking-card__meta { display: grid; gap: .45rem; margin: .75rem 0 0; }
  .booking-card__meta-row { display: flex; justify-content: space-between; gap: .75rem; color: #334155; font-size: .9rem; }
  .booking-card__meta-row span { color: #64748b; }
  .booking-card__footer { display: flex; align-items: center; justify-content: space-between; gap: .5rem; margin-top: .75rem; padding-top: .75rem; border-top: 1px dashed #dbe3ee; }
  .booking-card__hint { color: #64748b; font-size: .82rem; }
  .booking-card__open { border: 1px solid #cbd5e1; background: #ffffff; color: #0f172a; border-radius: 999px; padding: .4rem .7rem; font-weight: 700; cursor: pointer; }
  .booking-card__open:hover { background: #f8fafc; }
  .phase-badge { display: inline-flex; align-items: center; justify-content: center; padding: .25rem .55rem; border-radius: 999px; font-size: .76rem; font-weight: 700; white-space: nowrap; }
  .phase-badge--muted { background: #e2e8f0; color: #0f172a; }
  .phase-badge--warning { background: #fef3c7; color: #92400e; }
  .phase-badge--danger { background: #fee2e2; color: #b91c1c; }
  .phase-badge--success { background: #dcfce7; color: #166534; }
  .phase-badge--manual { background: linear-gradient(135deg, #111827 0%, #374151 100%); color: #ffffff; box-shadow: 0 8px 16px rgba(15, 23, 42, .12); }
  .status-chip { display: inline-flex; align-items: center; padding: .2rem .5rem; border-radius: 999px; font-size: .72rem; background: rgba(15, 23, 42, .06); color: #334155; }
  .booking-detail-backdrop { position: fixed; inset: 0; background: rgba(15, 23, 42, .55); display: none; align-items: center; justify-content: center; padding: 1rem; z-index: 100; }
  .booking-detail-backdrop.open { display: flex; }
  .booking-detail-modal { width: min(980px, 100%); max-height: min(90vh, 920px); overflow: auto; background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 24px 70px rgba(15, 23, 42, .35); }
  .booking-detail-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1.15rem 1.2rem; border-bottom: 1px solid #e2e8f0; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); }
  .booking-detail-title { margin: 0; font-size: 1.25rem; font-weight: 800; color: #0f172a; }
  .booking-detail-body { padding: 1.2rem; display: grid; gap: 1rem; }
  .booking-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .85rem; }
  .detail-box { border: 1px solid #e2e8f0; border-radius: 16px; padding: .85rem .9rem; background: #f8fafc; }
  .detail-label { display: block; font-size: .78rem; text-transform: uppercase; letter-spacing: .08em; color: #64748b; margin-bottom: .3rem; }
  .detail-value { font-weight: 600; color: #0f172a; line-height: 1.5; }
  .booking-detail-section { border: 1px solid #e2e8f0; border-radius: 18px; padding: 1rem; }
  .booking-detail-section h4 { margin: 0 0 .75rem; font-size: 1rem; color: #0f172a; }
  .booking-detail-actions { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .6rem; }
  .action-link, .action-button { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; border-radius: 12px; padding: .7rem .9rem; border: 1px solid #cbd5e1; text-decoration: none; font-weight: 700; background: #ffffff; color: #0f172a; }
  .action-link:hover, .action-button:hover { background: #f8fafc; }
  .action-link--primary, .action-button--primary { background: #2563eb; border-color: #2563eb; color: #ffffff; }
  .action-link--danger, .action-button--danger { background: #dc2626; border-color: #dc2626; color: #ffffff; }
  .booking-detail-footer { display: flex; flex-wrap: wrap; justify-content: space-between; gap: .75rem; padding: 0 1.2rem 1.2rem; border-top: 1px solid #e2e8f0; background: #ffffff; }
  .phase-select { display: flex; flex-wrap: wrap; gap: .6rem; align-items: center; }
  .phase-select select { min-width: 220px; }
  .modal-close { border: 1px solid #cbd5e1; background: #ffffff; color: #0f172a; border-radius: 999px; width: 40px; height: 40px; font-size: 1.05rem; cursor: pointer; flex: 0 0 auto; }
  .modal-close:hover { background: #f8fafc; }
  .toast { position: fixed; right: 1rem; bottom: 1rem; z-index: 120; display: none; background: #0f172a; color: #ffffff; padding: .85rem 1rem; border-radius: 14px; box-shadow: 0 16px 32px rgba(15, 23, 42, .25); }
  .toast.show { display: block; }
  .notify-backdrop { position: fixed; right: 1rem; bottom: 1rem; width: min(340px, calc(100vw - 2rem)); display: none; align-items: stretch; justify-content: flex-end; padding: 0; z-index: 130; }
  .notify-backdrop.open { display: flex; }
  .notify-modal { width: 100%; background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0; box-shadow: 0 20px 50px rgba(15, 23, 42, .25); overflow: hidden; }
  .notify-head { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; padding: .9rem 1rem; border-bottom: 1px solid #e2e8f0; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); }
  .notify-title { margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a; }
  .notify-body { padding: .95rem 1rem 1rem; display:grid; gap: .65rem; }
  .notify-line { display:grid; gap: .2rem; }
  .notify-label { display:block; font-size:.72rem; text-transform:uppercase; letter-spacing:.08em; color:#64748b; }
  .notify-value { color:#0f172a; font-weight:700; line-height:1.45; }
  .notify-close { border: 1px solid #cbd5e1; background: #ffffff; color:#0f172a; border-radius: 999px; width: 34px; height: 34px; font-size: 1rem; cursor: pointer; flex: 0 0 auto; }
  .notify-close:hover { background:#f8fafc; }
  @media (max-width: 1200px) {
    .dashboard-wrap { grid-template-columns: 1fr; }
    .sidebar { position: static; }
    .kanban-column { flex-basis: 300px; width: 300px; min-width: 300px; }
  }
  @media (max-width: 980px) {
    .booking-detail-grid, .booking-detail-actions { grid-template-columns: 1fr; }
    .booking-detail-modal { max-height: 92vh; }
    .notify-backdrop { width: min(320px, calc(100vw - 1.5rem)); right: .75rem; bottom: .75rem; }
    .kanban-column { flex-basis: 86vw; width: 86vw; min-width: 280px; }
  }
  @media (max-width: 640px) {
    .kanban-board { gap: .75rem; padding-bottom: 1rem; }
    .kanban-column { flex-basis: 88vw; width: 88vw; min-width: 280px; }
  }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <main class="content-card">
    <div class="kanban-top">
      <div>
        <h2 style="margin:0;">Kelola Booking</h2>
        <div class="kanban-subtitle">Drag card antar kolom atau buka detail untuk pindah status secara manual.</div>
      </div>
      <div style="display:flex; gap:.5rem; align-items:center;">
        <a class="btn btn-primary" href="{{ route('bookings.create') }}">Tambah Booking</a>
        <a href="{{ route('bookings.trash') }}" title="Trash Bookings" style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; background:#f1f5f9; border:1px solid var(--border); color:#64748b; text-decoration:none; transition:all .15s;">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        </a>
      </div>
    </div>

    <form id="booking-filter-form" method="GET" action="{{ route('bookings.index') }}" class="kanban-filters">
      <div class="field">
        <label for="q">Pencarian</label>
        <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, nomor, kendaraan, atau invoice" autocomplete="off" />
      </div>
      <div class="field">
        <label for="has_mitra">Filter Mitra</label>
        <select id="has_mitra" name="has_mitra">
          <option value="" {{ empty($filters['has_mitra']) ? 'selected' : '' }}>Semua</option>
          <option value="yes" {{ ($filters['has_mitra'] ?? '') === 'yes' ? 'selected' : '' }}>Sudah ada Mitra</option>
          <option value="no" {{ ($filters['has_mitra'] ?? '') === 'no' ? 'selected' : '' }}>Belum ada Mitra</option>
        </select>
      </div>
    </form>

    <div class="kanban-board" id="kanbanBoard">
      @foreach(\App\Models\Booking::KANBAN_PHASES as $phaseKey => $definition)
        <section class="kanban-column" data-phase="{{ $phaseKey }}">
          <div class="kanban-column__header">
            <div>
              <h3 class="kanban-column__title">{{ $definition['label'] }}</h3>
      <div class="kanban-subtitle">
        {{ $phaseKey === 'masuk' ? 'Tidak diberi warna khusus.' : ($phaseKey === 'proses' ? 'Tanda kuning untuk booking yang sedang dikerjakan.' : ($phaseKey === 'cancel' ? 'Tanda merah untuk booking cancel.' : 'Tanda hijau untuk booking selesai.')) }}
      </div>
            </div>
            <div class="kanban-column__count" data-count-for="{{ $phaseKey }}">{{ $counts[$phaseKey] ?? 0 }}</div>
          </div>
          <div class="kanban-column__cards" data-drop-zone="{{ $phaseKey }}">
            @forelse(($bookingsByPhase[$phaseKey] ?? collect()) as $booking)
              <article
                class="booking-card {{ $booking->kanbanCardClass() }}"
                draggable="true"
                data-booking-id="{{ $booking->id }}"
                data-phase="{{ $booking->kanbanPhase() }}"
              >
                <div class="booking-card__top">
                  <div>
                    <h4 class="booking-card__name">{{ $booking->customer_name }}</h4>
                    <div class="kanban-subtitle" style="margin-top:.25rem;">{{ $booking->contact_number ?? '-' }}</div>
                  </div>
                  <div style="display:flex; flex-wrap:wrap; gap:.35rem; justify-content:flex-end;">
                    <span class="{{ $booking->kanbanBadgeClass() }}">{{ $booking->kanbanPhaseLabel() }}</span>
                    @if(!empty($bookingBoardData[$booking->id]['has_manual_invoice']))
                      <span class="phase-badge phase-badge--manual">Manual</span>
                    @endif
                  </div>
                </div>

                <div class="booking-card__meta">
                  <div class="booking-card__meta-row">
                    <span>Tanggal</span>
                    <strong>{{ optional($booking->booking_date)->format('d M Y') }} - {{ optional($booking->end_date)->format('d M Y') }}</strong>
                  </div>
                  <div class="booking-card__meta-row">
                    <span>Mobil</span>
                    <strong>{{ trim((string) (($booking->vehicle?->make ?? '') . ' ' . ($booking->vehicle?->model ?? ''))) !== '' ? trim((string) (($booking->vehicle?->make ?? '') . ' ' . ($booking->vehicle?->model ?? ''))) : '-' }}</strong>
                  </div>
                  <div class="booking-card__meta-row">
                    <span>Biaya</span>
                    <strong>IDR {{ number_format((float) ($booking->price ?? 0), 0, ',', '.') }}</strong>
                  </div>
                  @if($booking->group)
                  <div class="booking-card__meta-row">
                    <span>Group</span>
                    <strong>{{ $booking->group->name }}</strong>
                  </div>
                  @endif
                </div>

                <div class="booking-card__footer">
                  <div class="booking-card__hint">Klik untuk detail</div>
                  <div style="display:flex; gap:.35rem; align-items:center;">
                    @if($booking->itinerary_id)
                      <a href="{{ route('itineraries.pdf', $booking->itinerary_id) }}" class="booking-card__open" style="text-decoration:none; font-size:.78rem; padding:.3rem .6rem;" title="Download Itinerary PDF" onclick="event.stopPropagation();">Itinerary</a>
                    @endif
                    <button type="button" class="booking-card__open" data-open-booking="{{ $booking->id }}">Detail</button>
                  </div>
                </div>
              </article>
            @empty
              <div class="kanban-empty" data-empty-state="{{ $phaseKey }}">Tidak ada booking di kolom ini.</div>
            @endforelse
          </div>
        </section>
      @endforeach
    </div>
  </main>
</div>

<div class="booking-detail-backdrop" id="bookingDetailBackdrop" aria-hidden="true">
  <div class="booking-detail-modal" role="dialog" aria-modal="true" aria-labelledby="bookingDetailTitle">
    <div class="booking-detail-head">
      <div>
        <h3 class="booking-detail-title" id="bookingDetailTitle">Detail Booking</h3>
        <div class="kanban-subtitle" id="bookingDetailSubtitle">Informasi booking lengkap dan aksi cepat.</div>
      </div>
      <button type="button" class="modal-close" id="bookingDetailClose" aria-label="Tutup popup">x</button>
    </div>

    <div class="booking-detail-body">
      <div class="booking-detail-grid">
        <div class="detail-box">
          <span class="detail-label">Customer</span>
          <div class="detail-value" id="detailCustomerName">-</div>
        </div>
        <div class="detail-box">
          <span class="detail-label">Status Kanban</span>
          <div class="detail-value" id="detailPhaseLabel">-</div>
        </div>
        <div class="detail-box">
          <span class="detail-label">Tanggal Mulai</span>
          <div class="detail-value" id="detailStartDate">-</div>
        </div>
        <div class="detail-box">
          <span class="detail-label">Tanggal Berakhir</span>
          <div class="detail-value" id="detailEndDate">-</div>
        </div>
        <div class="detail-box">
          <span class="detail-label">Mobil</span>
          <div class="detail-value" id="detailVehicleName">-</div>
        </div>
        <div class="detail-box">
          <span class="detail-label">Biaya</span>
          <div class="detail-value" id="detailPrice">-</div>
        </div>
        <div class="detail-box">
          <span class="detail-label">Group</span>
          <div class="detail-value" id="detailGroupName">-</div>
        </div>
      </div>

      <div class="booking-detail-section">
        <h4>Informasi Lengkap</h4>
        <div class="booking-detail-grid">
          <div class="detail-box"><span class="detail-label">Telepon</span><div class="detail-value" id="detailContact">-</div></div>
          <div class="detail-box"><span class="detail-label">Penumpang</span><div class="detail-value" id="detailPassengers">-</div></div>
          <div class="detail-box"><span class="detail-label">Asal Negara</span><div class="detail-value" id="detailCountry">-</div></div>
          <div class="detail-box"><span class="detail-label">Pickup</span><div class="detail-value" id="detailPickupLocation">-</div></div>
          <div class="detail-box"><span class="detail-label">Pickup Address</span><div class="detail-value" id="detailPickupAddress">-</div></div>
          <div class="detail-box"><span class="detail-label">Layanan</span><div class="detail-value" id="detailServiceName">-</div></div>
          <div class="detail-box"><span class="detail-label">Pembayaran</span><div class="detail-value" id="detailPaymentPlan">-</div></div>
          <div class="detail-box"><span class="detail-label">Down Payment</span><div class="detail-value" id="detailDownPayment">-</div></div>
          <div class="detail-box"><span class="detail-label">Status Internal</span><div class="detail-value" id="detailStatusLabel">-</div></div>
          <div class="detail-box"><span class="detail-label">Invoice</span><div class="detail-value" id="detailInvoiceNumber">-</div></div>
        </div>
      </div>

      <div class="booking-detail-section">
        <h4>Detail Rencana Perjalanan</h4>
        <div class="detail-value" id="detailTravelPlans" style="white-space: normal;">-</div>
      </div>

      <div class="booking-detail-section">
        <h4>Aksi Cepat</h4>
        <div class="booking-detail-actions" id="bookingActionButtons"></div>
      </div>
    </div>

    <div class="booking-detail-footer">
      <div class="phase-select">
        <div>
          <label for="bookingPhaseSelect" style="margin-bottom:.25rem;">Pindah Status</label>
          <select id="bookingPhaseSelect">
            <option value="masuk">Booking Masuk</option>
            <option value="proses">Booking Proses</option>
            <option value="cancel">Booking Cancel</option>
            <option value="selesai">Booking Selesai</option>
          </select>
        </div>
        <button type="button" class="action-button action-button--primary" id="bookingPhaseSave">Simpan Status</button>
      </div>
      <div style="display:flex; gap:.6rem; flex-wrap:wrap;">
        <a href="#" class="action-link" id="bookingEditLink">Edit</a>
        <button type="button" class="action-button action-button--danger" id="bookingDeleteTrigger">Hapus</button>
      </div>
    </div>
  </div>
</div>

<div class="toast" id="bookingToast"></div>

<div class="notify-backdrop" id="bookingNotifyBackdrop" aria-hidden="true">
  <div class="notify-modal" role="dialog" aria-modal="true" aria-labelledby="bookingNotifyTitle">
    <div class="notify-head">
      <div>
        <h3 class="notify-title" id="bookingNotifyTitle">Booking Baru Masuk</h3>
      </div>
      <button type="button" class="notify-close" id="bookingNotifyClose" aria-label="Tutup popup">x</button>
    </div>
    <div class="notify-body">
      <div class="notify-line">
        <span class="notify-label">Customer</span>
        <div class="notify-value" id="notifyCustomerName">-</div>
      </div>
      <div class="notify-line">
        <span class="notify-label">Waktu Booking</span>
        <div class="notify-value" id="notifyCreatedAt">-</div>
      </div>
      <div class="notify-line">
        <span class="notify-label">Status</span>
        <div class="notify-value" id="notifyStatusText">Booking baru telah diterima.</div>
      </div>
    </div>
  </div>
</div>

<form method="POST" id="bookingDeleteForm" style="display:none;">
  @csrf
  @method('DELETE')
</form>

<script>
  window.bookingBoardData = @json($bookingBoardData);
  window.bookingPhaseEndpoints = @json($bookings->mapWithKeys(fn ($booking) => [$booking->id => route('bookings.phase.update', $booking)]));
  window.bookingCsrfToken = @json(csrf_token());
  window.bookingSnapshot = @json($bookings->pluck('id'));
  window.bookingNotificationSettings = @json($notificationSoundSettings ?? []);
</script>

<script>
  (function () {
    const form = document.getElementById('booking-filter-form');
    const hasMitraEl = document.getElementById('has_mitra');
    const qEl = document.getElementById('q');
    const toastEl = document.getElementById('bookingToast');
    const notifyBackdrop = document.getElementById('bookingNotifyBackdrop');
    const notifyClose = document.getElementById('bookingNotifyClose');
    const notifyCustomerName = document.getElementById('notifyCustomerName');
    const notifyCreatedAt = document.getElementById('notifyCreatedAt');
    const notifyStatusText = document.getElementById('notifyStatusText');
    const board = document.getElementById('kanbanBoard');
    const backdrop = document.getElementById('bookingDetailBackdrop');
    const modalClose = document.getElementById('bookingDetailClose');
    const phaseSelect = document.getElementById('bookingPhaseSelect');
    const phaseSaveBtn = document.getElementById('bookingPhaseSave');
    const actionButtons = document.getElementById('bookingActionButtons');
    const deleteForm = document.getElementById('bookingDeleteForm');
    const editLink = document.getElementById('bookingEditLink');
    const deleteTrigger = document.getElementById('bookingDeleteTrigger');

    const detailFields = {
      customerName: document.getElementById('detailCustomerName'),
      phaseLabel: document.getElementById('detailPhaseLabel'),
      startDate: document.getElementById('detailStartDate'),
      endDate: document.getElementById('detailEndDate'),
      vehicleName: document.getElementById('detailVehicleName'),
      price: document.getElementById('detailPrice'),
      groupName: document.getElementById('detailGroupName'),
      contact: document.getElementById('detailContact'),
      passengers: document.getElementById('detailPassengers'),
      country: document.getElementById('detailCountry'),
      pickupLocation: document.getElementById('detailPickupLocation'),
      pickupAddress: document.getElementById('detailPickupAddress'),
      serviceName: document.getElementById('detailServiceName'),
      paymentPlan: document.getElementById('detailPaymentPlan'),
      downPayment: document.getElementById('detailDownPayment'),
      statusLabel: document.getElementById('detailStatusLabel'),
      invoiceNumber: document.getElementById('detailInvoiceNumber'),
      travelPlans: document.getElementById('detailTravelPlans'),
    };

    if (!form || !hasMitraEl || !qEl || !board || !backdrop || !modalClose || !phaseSelect || !phaseSaveBtn) {
      return;
    }

    let activeBookingId = null;
    let notificationOpen = false;
    let notificationBookingId = null;

    function showToast(message) {
      if (!toastEl) return;
      toastEl.textContent = message;
      toastEl.classList.add('show');
      window.clearTimeout(showToast.timer);
      showToast.timer = window.setTimeout(() => {
        toastEl.classList.remove('show');
      }, 2200);
    }

    function playNotificationSound() {
      try {
        const settings = window.bookingNotificationSettings || {};
        if (!settings.enabled) return;
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return;
        const audioCtx = window.__bdtBookingNotifyAudioCtx || new Ctx();
        window.__bdtBookingNotifyAudioCtx = audioCtx;
        if (audioCtx.state === 'suspended') return;
        const volume = Math.max(0.08, Math.min(1, Number(settings.volume || 80) / 100));
        const repeat = Math.max(1, Math.min(5, Number(settings.repeat || 3)));
        const intervalMs = Math.max(50, Number(settings.interval_ms || 140));
        const type = String(settings.type || 'beep');
        const presets = {
          beep: { wave: 'sine', base: 980, peak: 1320 },
          chime: { wave: 'triangle', base: 740, peak: 1020 },
          bell: { wave: 'square', base: 620, peak: 860 },
          soft: { wave: 'sine', base: 520, peak: 760 },
        };
        const preset = presets[type] || presets.beep;
        const start = audioCtx.currentTime;
        const beepDuration = 0.16;
        const gap = intervalMs / 1000;
        for (let i = 0; i < repeat; i += 1) {
          const now = start + (i * (beepDuration + gap));
          const osc = audioCtx.createOscillator();
          const gain = audioCtx.createGain();
          osc.type = preset.wave;
          osc.frequency.setValueAtTime(preset.base, now);
          osc.frequency.exponentialRampToValueAtTime(preset.peak, now + 0.08);
          gain.gain.setValueAtTime(0.0001, now);
          gain.gain.exponentialRampToValueAtTime(volume, now + 0.02);
          gain.gain.exponentialRampToValueAtTime(0.0001, now + beepDuration);
          osc.connect(gain);
          gain.connect(audioCtx.destination);
          osc.start(now);
          osc.stop(now + beepDuration + 0.02);
        }
      } catch (_) {}
    }

    async function unlockNotificationAudio() {
      try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return;
        if (!window.__bdtBookingNotifyAudioCtx) {
          window.__bdtBookingNotifyAudioCtx = new Ctx();
        }
        const audioCtx = window.__bdtBookingNotifyAudioCtx;
        if (audioCtx.state === 'suspended') {
          await audioCtx.resume();
        }
      } catch (_) {}
    }

    function escapeHtml(value) {
      return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function phaseLabel(phase) {
      const map = {
        masuk: 'Booking Masuk',
        proses: 'Booking Proses',
        cancel: 'Booking Cancel',
        selesai: 'Booking Selesai',
      };
      return map[phase] || 'Booking Masuk';
    }

    function getBooking(id) {
      return window.bookingBoardData ? window.bookingBoardData[id] : null;
    }

    function columnForPhase(phase) {
      return document.querySelector('[data-drop-zone="' + phase + '"]');
    }

    function countElForPhase(phase) {
      return document.querySelector('[data-count-for="' + phase + '"]');
    }

    function ensureEmptyState(column) {
      if (!column) return;
      const cards = column.querySelectorAll('.booking-card');
      const empty = column.querySelector('[data-empty-state]');
      if (cards.length === 0 && !empty) {
        const div = document.createElement('div');
        div.className = 'kanban-empty';
        div.dataset.emptyState = column.dataset.dropZone;
        div.textContent = 'Tidak ada booking di kolom ini.';
        column.appendChild(div);
      } else if (cards.length > 0 && empty) {
        empty.remove();
      }
    }

    function updateColumnCount(phase) {
      const column = columnForPhase(phase);
      const count = column ? column.querySelectorAll('.booking-card').length : 0;
      const countEl = countElForPhase(phase);
      if (countEl) countEl.textContent = String(count);
    }

    function setCardPhase(card, phase) {
      card.dataset.phase = phase;
      card.classList.remove('phase-masuk', 'phase-proses', 'phase-cancel', 'phase-selesai');
      card.classList.add(phase === 'proses' ? 'phase-proses' : phase === 'cancel' ? 'phase-cancel' : phase === 'selesai' ? 'phase-selesai' : 'phase-masuk');

      const badge = card.querySelector('.phase-badge');
      if (badge) {
        badge.textContent = phaseLabel(phase);
        if (phase === 'masuk') badge.className = 'phase-badge phase-badge--muted';
        if (phase === 'proses') badge.className = 'phase-badge phase-badge--warning';
        if (phase === 'cancel') badge.className = 'phase-badge phase-badge--danger';
        if (phase === 'selesai') badge.className = 'phase-badge phase-badge--success';
      }
    }

    function moveCardToPhase(card, phase) {
      const targetCards = columnForPhase(phase);
      if (!targetCards) return;
      const targetColumn = targetCards.closest('.kanban-column');
      const currentPhase = card.dataset.phase;
      const currentColumn = columnForPhase(currentPhase);

      targetCards.appendChild(card);
      setCardPhase(card, phase);
      if (currentColumn) {
        ensureEmptyState(currentColumn);
        updateColumnCount(currentPhase);
      }
      ensureEmptyState(targetColumn);
      updateColumnCount(phase);
    }

    async function updateBookingPhase(bookingId, phase) {
      const booking = getBooking(bookingId);
      const endpoint = window.bookingPhaseEndpoints ? window.bookingPhaseEndpoints[bookingId] : null;

      if (!booking || !endpoint) return false;

      const response = await fetch(endpoint, {
        method: 'PATCH',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': window.bookingCsrfToken,
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ phase: phase }),
      });

      if (!response.ok) {
        const payload = await response.json().catch(() => null);
        throw new Error((payload && payload.message) ? payload.message : 'Gagal memperbarui status booking.');
      }

      const payload = await response.json();
      booking.phase = payload.phase || phase;
      booking.phase_label = payload.phase_label || phaseLabel(phase);
      booking.status = payload.status || booking.status;
      booking.status_label = payload.status_label || booking.status_label;
      return true;
    }

    function closeModal() {
      backdrop.classList.remove('open');
      backdrop.setAttribute('aria-hidden', 'true');
      activeBookingId = null;
    }

    function closeNotificationPopup() {
      notificationOpen = false;
      notificationBookingId = null;
      if (notifyBackdrop) {
        notifyBackdrop.classList.remove('open');
        notifyBackdrop.setAttribute('aria-hidden', 'true');
      }
      window.location.reload();
    }

    function openNotificationPopup(booking) {
      if (!booking || notificationOpen) return;
      notificationOpen = true;
      notificationBookingId = booking.id;
      notifyCustomerName.textContent = booking.customer_name || '-';
      notifyCreatedAt.textContent = booking.created_at || '-';
      notifyStatusText.textContent = 'Booking baru sudah masuk.';
      notifyBackdrop.classList.add('open');
      notifyBackdrop.setAttribute('aria-hidden', 'false');
      playNotificationSound();
    }

    function openModal(bookingId) {
      const booking = getBooking(bookingId);
      if (!booking) return;

      activeBookingId = bookingId;
      detailFields.customerName.textContent = booking.customer_name || '-';
      detailFields.phaseLabel.textContent = booking.phase_label || phaseLabel(booking.phase);
      detailFields.startDate.textContent = booking.booking_date || '-';
      detailFields.endDate.textContent = booking.end_date || '-';
      detailFields.vehicleName.textContent = booking.vehicle_name || '-';
      detailFields.groupName.textContent = booking.group_name || '-';
      detailFields.price.textContent = 'IDR ' + (booking.price || '0');
      detailFields.contact.textContent = booking.contact_number || '-';
      detailFields.passengers.textContent = booking.number_of_passengers || '-';
      detailFields.country.textContent = booking.country_of_origin || '-';
      detailFields.pickupLocation.textContent = booking.pickup_location || '-';
      detailFields.pickupAddress.textContent = booking.pickup_address_en || '-';
      detailFields.serviceName.textContent = booking.service_name || '-';
      detailFields.paymentPlan.textContent = booking.payment_plan_label || '-';
      detailFields.downPayment.textContent = booking.down_payment_amount || '-';
      detailFields.statusLabel.textContent = booking.status_label || '-';
      detailFields.invoiceNumber.textContent = booking.invoice_number || '-';
      detailFields.travelPlans.innerHTML = booking.travel_plans || '<span style="color:#64748b;">Tidak ada rencana perjalanan.</span>';

      phaseSelect.value = booking.phase || 'masuk';
      editLink.href = booking.edit_url || '#';
      deleteForm.action = booking.delete_url || '#';
      deleteTrigger.dataset.bookingId = bookingId;

      const buttons = [];
      if (booking.invoice_show_url) {
        buttons.push('<a class="action-link action-link--primary" href="' + booking.invoice_show_url + '">Lihat Invoice</a>');
        buttons.push('<a class="action-link" href="' + booking.invoice_download_url + '">Download Invoice</a>');
      }
      if (booking.manual_invoice_url) {
        buttons.push('<a class="action-link" href="' + booking.manual_invoice_url + '" target="_blank" rel="noopener">Invoice Manual</a>');
      }
      if (booking.whatsapp_url) {
        buttons.push('<a class="action-link" href="' + booking.whatsapp_url + '" target="_blank" rel="noopener">Chat WA</a>');
      }
      if (booking.spj_show_url) {
        buttons.push('<a class="action-link" href="' + booking.spj_show_url + '">Lihat SPJ</a>');
      }
      if (booking.spj_download_url) {
        buttons.push('<a class="action-link" href="' + booking.spj_download_url + '">Download SPJ</a>');
      }
      if (booking.itinerary_pdf_url) {
        buttons.push('<a class="action-link action-link--primary" href="' + booking.itinerary_pdf_url + '">Download Itinerary</a>');
      }
      if (booking.edit_url) {
        buttons.push('<a class="action-link action-link--primary" href="' + booking.edit_url + '">Edit Booking</a>');
      }

      actionButtons.innerHTML = buttons.length ? buttons.join('') : '<div class="kanban-empty" style="margin:0;">Tidak ada aksi tambahan.</div>';
      backdrop.classList.add('open');
      backdrop.setAttribute('aria-hidden', 'false');
    }

    function syncCardStatusAfterMove(card, phase) {
      const booking = getBooking(card.dataset.bookingId);
      if (!booking) return;
      booking.phase = phase;
      booking.phase_label = phaseLabel(phase);
      setCardPhase(card, phase);
      const modalOpen = backdrop.classList.contains('open') && activeBookingId === String(card.dataset.bookingId);
      if (modalOpen) {
        phaseSelect.value = phase;
        detailFields.phaseLabel.textContent = phaseLabel(phase);
        detailFields.statusLabel.textContent = booking.status_label || detailFields.statusLabel.textContent;
      }
    }

    document.querySelectorAll('.booking-card').forEach((card) => {
      card.addEventListener('dragstart', (event) => {
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', card.dataset.bookingId);
        card.classList.add('is-dragging');
      });
      card.addEventListener('dragend', () => {
        card.classList.remove('is-dragging');
        document.querySelectorAll('.kanban-column.is-over').forEach((el) => el.classList.remove('is-over'));
      });
      card.addEventListener('click', (event) => {
        if (event.target.closest('a, button')) return;
        openModal(card.dataset.bookingId);
      });
    });

    document.querySelectorAll('[data-open-booking]').forEach((button) => {
      button.addEventListener('click', () => openModal(button.dataset.openBooking));
    });

    document.querySelectorAll('[data-drop-zone]').forEach((zone) => {
      zone.addEventListener('dragover', (event) => {
        event.preventDefault();
        zone.closest('.kanban-column').classList.add('is-over');
      });
      zone.addEventListener('dragleave', () => {
        zone.closest('.kanban-column').classList.remove('is-over');
      });
      zone.addEventListener('drop', async (event) => {
        event.preventDefault();
        const column = zone.closest('.kanban-column');
        column.classList.remove('is-over');
        const bookingId = event.dataTransfer.getData('text/plain');
        const card = document.querySelector('.booking-card[data-booking-id="' + bookingId + '"]');
        if (!card) return;
        const targetPhase = zone.dataset.dropZone;
        if (card.dataset.phase === targetPhase) return;

        try {
          await updateBookingPhase(bookingId, targetPhase);
          moveCardToPhase(card, targetPhase);
          syncCardStatusAfterMove(card, targetPhase);
          showToast('Status booking diperbarui.');
        } catch (error) {
          showToast(error.message || 'Gagal memperbarui status booking.');
        }
      });
    });

    backdrop.addEventListener('click', (event) => {
      if (event.target === backdrop) closeModal();
    });
    modalClose.addEventListener('click', closeModal);
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') closeModal();
    });

    phaseSaveBtn.addEventListener('click', async () => {
      if (!activeBookingId) return;
      const card = document.querySelector('.booking-card[data-booking-id="' + activeBookingId + '"]');
      const nextPhase = phaseSelect.value;
      if (!card || card.dataset.phase === nextPhase) {
        closeModal();
        return;
      }

      try {
        await updateBookingPhase(activeBookingId, nextPhase);
        moveCardToPhase(card, nextPhase);
        syncCardStatusAfterMove(card, nextPhase);
        showToast('Status booking diperbarui.');
        closeModal();
      } catch (error) {
        showToast(error.message || 'Gagal memperbarui status booking.');
      }
    });

    deleteTrigger.addEventListener('click', () => {
      if (!activeBookingId) return;
      if (!confirm('Hapus booking ini?')) return;
      deleteForm.submit();
    });

    function buildUrl(baseUrl) {
      const url = new URL(baseUrl, window.location.origin);
      const params = new URLSearchParams(url.search);
      const hasMitra = hasMitraEl.value;
      const q = qEl.value;
      if (hasMitra) params.set('has_mitra', hasMitra); else params.delete('has_mitra');
      if (q) params.set('q', q); else params.delete('q');
      url.search = params.toString();
      return url.toString();
    }

    function fetchBoard(targetUrl) {
      const url = buildUrl(targetUrl || form.action);
      window.location.href = url;
    }

    async function checkForNewBookings() {
      try {
        const response = await fetch('/api/bookings', {
          headers: { 'Accept': 'application/json' },
          cache: 'no-store',
        });
        if (!response.ok) return;

        const bookings = await response.json();
        const latestId = Number(bookings?.[0]?.id || 0);
        const currentLatestId = Number((window.bookingSnapshot && window.bookingSnapshot[0]) || 0);

        if (latestId > currentLatestId && !notificationOpen) {
          const latestBookingResponse = await fetch('/api/bookings/' + latestId, {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store',
          });
          if (!latestBookingResponse.ok) return;
          const latestBooking = await latestBookingResponse.json();
          if (latestBooking && Number(latestBooking.id || 0) > currentLatestId) {
            openNotificationPopup({
              id: latestBooking.id,
              customer_name: latestBooking.customer_name,
              booking_date: latestBooking.booking_date,
              end_date: latestBooking.end_date,
              created_at: latestBooking.created_at ? new Date(latestBooking.created_at).toLocaleString('id-ID', {
                dateStyle: 'medium',
                timeStyle: 'short',
              }) : '-',
            });
            window.bookingSnapshot = [latestBooking.id].concat(window.bookingSnapshot || []);
          }
        }
      } catch (_) {}
    }

    hasMitraEl.addEventListener('change', () => fetchBoard());
    let qTimer = null;
    qEl.addEventListener('input', () => {
      window.clearTimeout(qTimer);
      qTimer = window.setTimeout(() => fetchBoard(), 300);
    });

    window.setInterval(checkForNewBookings, 15000);
    checkForNewBookings();

    notifyClose?.addEventListener('click', closeNotificationPopup);
    document.addEventListener('pointerdown', unlockNotificationAudio, { once: true });
    document.addEventListener('keydown', unlockNotificationAudio, { once: true });
    document.addEventListener('touchstart', unlockNotificationAudio, { once: true, passive: true });
  })();
</script>
@endsection
