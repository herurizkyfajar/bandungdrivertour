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
      $serviceName = $booking->services->pluck('name')->implode(', ') ?: ($booking->service?->name ?? '-');
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

  $bookingOrder = $bookings->pluck('id')->values()->flip();
  $initialBookingLimit = 15;
@endphp

@section('content')
<style>
  /* ===== Shell halaman: sama dengan dashboard ===== */
  body { background: #eef5ff; }

  body::before {
    content: "";
    position: fixed;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    background:
      radial-gradient(1100px 460px at 88% -8%, #d4e9ff 0%, rgba(212, 233, 255, 0) 72%),
      linear-gradient(180deg, #e3f0fd 0%, #eef5ff 34%, #f7fbff 68%, #e9f7ee 100%);
  }

  .container {
    max-width: 100% !important;
    width: 100%;
    margin: 0 auto;
    padding: 1rem 1.25rem .5rem;
  }

  .dashboard-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 1.25rem;
    align-items: start;
    min-height: calc(100vh - 6rem);
    padding-left: 252px;
  }

  .dashboard-main {
    display: grid;
    gap: 1.05rem;
    align-content: start;
    min-width: 0;
    min-height: 0;
    padding: 0 0 1rem;
    background: transparent;
    border: none;
    box-shadow: none;
  }

  .dashboard-sidebar {
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 252px;
    height: 100vh;
    max-height: none;
    margin: 0;
    border: none;
    border-radius: 0;
    padding: 1.1rem .9rem 1.25rem;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 60;
    color: #fff;
    background: linear-gradient(170deg, #1f63e8 0%, #0d4cd0 42%, #093fa9 100%);
    box-shadow: 6px 0 24px rgba(9, 63, 169, .16);
  }

  .dashboard-sidebar::after {
    content: "";
    display: block;
    flex: 0 0 auto;
    margin-top: 1.25rem;
    height: 168px;
    border-radius: 14px;
    background:
      linear-gradient(180deg, rgba(31, 99, 232, 0) 0%, rgba(9, 63, 169, .35) 100%),
      url('{{ asset('assets/img/gedung-sate-drive.png') }}') 14% 82% / 200% auto no-repeat;
  }

  .dashboard-brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .45rem;
    padding: 0 .3rem 1rem;
    margin-bottom: .75rem;
    border-bottom: 1px solid rgba(255, 255, 255, .18);
  }

  .dashboard-brand img {
    width: 142px;
    height: auto;
    padding: .3rem .45rem;
    border-radius: 10px;
    background: #ffffff;
    object-fit: contain;
  }

  .dashboard-brand h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #ffffff;
  }

  .dashboard-brand .subtitle {
    font-size: .82rem;
    color: rgba(255, 255, 255, .82);
  }

  .dashboard-section-label {
    margin: .85rem .3rem .4rem;
    padding: 0;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .6);
  }

  .dashboard-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: .12rem;
  }

  .dashboard-menu a,
  .dashboard-menu button {
    display: flex;
    align-items: center;
    gap: .6rem;
    width: 100%;
    padding: .48rem .55rem;
    border: none;
    border-radius: 11px;
    background: transparent;
    color: #e9f1ff;
    font-family: inherit;
    font-size: .87rem;
    font-weight: 600;
    text-align: left;
    text-decoration: none;
    cursor: pointer;
    transition: background .15s ease, color .15s ease;
  }

  .dashboard-menu a:hover,
  .dashboard-menu button:hover {
    background: rgba(255, 255, 255, .12);
    color: #ffffff;
  }

  .dashboard-menu a.active {
    background: #ffffff;
    color: #0b4fd6;
    box-shadow: 0 6px 14px rgba(4, 33, 90, .22);
  }

  .dashboard-menu .nav-icon {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(255, 255, 255, .14);
    color: #ffffff;
    font-size: .9rem;
  }

  .dashboard-menu a.active .nav-icon {
    background: #e7f0ff;
    color: #0b4fd6;
  }

  .dashboard-topbar {
    position: sticky;
    top: .5rem;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .85rem;
    padding: .65rem .85rem;
    border: 1px solid #e8f0fb;
    border-radius: 18px;
    background: rgba(255, 255, 255, .94);
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 22px rgba(13, 43, 82, .05);
  }

  .topbar-menu-btn {
    display: none;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: 1px solid #e6eef9;
    border-radius: 12px;
    background: #f6faff;
    color: #0b4fd6;
    cursor: pointer;
    flex: 0 0 auto;
  }

  .topbar-search {
    position: relative;
    flex: 1 1 420px;
    min-width: 0;
    max-width: 520px;
  }

  .topbar-search > i {
    position: absolute;
    left: .95rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .95rem;
  }

  .topbar-search input {
    width: 100%;
    height: auto;
    padding: .6rem 5.4rem .6rem 2.45rem;
    border: 1px solid #e6eef9;
    border-radius: 999px;
    background: #f8fbff;
    color: #0b2545;
    font-size: .88rem;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
  }

  .topbar-search input:focus {
    background: #ffffff;
    border-color: #7aa7e8;
    box-shadow: 0 0 0 4px rgba(21, 101, 192, .1);
  }

  .topbar-shortcut {
    position: absolute;
    right: .4rem;
    top: 50%;
    transform: translateY(-50%);
    margin: 0;
    padding: .18rem .45rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #ffffff;
    color: #475569;
    font-size: .72rem;
    font-weight: 700;
    white-space: nowrap;
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex: 0 1 auto;
  }

  .topbar-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: 1px solid #e8eef7;
    border-radius: 50%;
    background: #ffffff;
    color: #334155;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    transition: background .15s ease, border-color .15s ease, color .15s ease;
  }

  .topbar-icon-btn:hover {
    background: #f3f8ff;
    border-color: #cbd5e1;
    color: #0b4fd6;
  }

  .topbar-user {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .3rem .7rem .3rem .35rem;
    border: 1px solid #e8eef7;
    border-radius: 999px;
    background: #ffffff;
  }

  .topbar-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #2f80ed, #0b4fd6);
    color: #ffffff;
    font-size: .88rem;
    font-weight: 800;
    flex-shrink: 0;
  }

  .topbar-user-meta {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
    min-width: 0;
    overflow: hidden;
  }

  .topbar-user-meta strong {
    max-width: 130px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: .82rem;
    font-weight: 800;
    color: #0b2545;
  }

  .topbar-user-meta span {
    font-size: .7rem;
    color: #64748b;
    text-transform: capitalize;
  }

  .topbar-user > i { color: #94a3b8; font-size: .75rem; }

  /* ===== Header halaman booking ===== */
  .bookings-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.3rem 1.4rem;
    border: 1px solid #dbeafe;
    border-radius: 22px;
    background:
      linear-gradient(115deg, #ffffff 0%, #f4f9ff 48%, #e6f2ff 100%);
    box-shadow: 0 14px 30px rgba(13, 43, 82, .07);
  }

  .bookings-hero .hero-eyebrow {
    margin-bottom: .3rem;
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .14em;
    color: #1d6fe0;
  }

  .bookings-hero h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -.03em;
    color: #0b2545;
  }

  .bookings-hero .subtitle {
    margin: .35rem 0 0;
    max-width: 58ch;
    font-size: .9rem;
    line-height: 1.5;
    color: #3c5878;
  }

  .hero-actions {
    display: flex;
    align-items: center;
    gap: .6rem;
    flex: 0 0 auto;
    flex-wrap: wrap;
  }

  .bookings-hero .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .68rem 1.05rem;
    border-radius: 12px;
    font-size: .88rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    box-shadow: 0 8px 18px rgba(11, 61, 145, .16);
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }

  .bookings-hero .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(11, 61, 145, .22);
  }

  .bookings-hero .btn-primary { background: #0b4fd6; color: #ffffff; }
  .bookings-hero .btn-primary:hover { background: #093fa9; }

  .bookings-panel {
    position: relative;
    min-width: 0;
    padding: 1rem 1.1rem 1.1rem;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 12px 26px rgba(13, 43, 82, .06);
  }

  .bookings-toolbar {
    display: flex;
    flex-wrap: nowrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: .75rem;
  }

  .bookings-toolbar__fields {
    display: flex;
    flex: 1 1 auto;
    flex-wrap: nowrap;
    align-items: flex-end;
    gap: .6rem;
    min-width: 0;
  }

  .bookings-toolbar .field { flex: 1 1 0; min-width: 0; width: auto; max-width: none; }

  .bookings-toolbar label {
    display: block;
    font-size: .74rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: .3rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .bookings-toolbar select {
    width: 100%;
    min-width: 0;
    height: 42px;
    padding: 0 .6rem;
    text-overflow: ellipsis;
    border: 1px solid #dbe7f6;
    border-radius: 11px;
    background: #f8fbff;
    color: #0b2545;
    font-family: inherit;
    font-size: .88rem;
  }

  .bookings-total {
    display: inline-flex;
    flex: 0 0 auto;
    align-items: center;
    gap: .35rem;
    white-space: nowrap;
    padding: .45rem .8rem;
    border-radius: 999px;
    background: #eff6ff;
    color: #0b4fd6;
    font-size: .8rem;
    font-weight: 700;
  }

  .bookings-panel.is-loading .kanban-board { opacity: .45; }

  .bookings-panel.is-loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 34px;
    height: 34px;
    margin: -17px 0 0 -17px;
    border: 3px solid #cfe0f7;
    border-top-color: #0b4fd6;
    border-radius: 50%;
    animation: bookings-spin .7s linear infinite;
    z-index: 5;
  }

  @keyframes bookings-spin { to { transform: rotate(360deg); } }

  .booking-card.is-hidden-booking { display: none !important; }

  .bookings-loadmore {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .55rem;
    padding-top: .35rem;
  }

  .bookings-loadmore-info {
    font-size: .78rem;
    font-weight: 600;
    color: #64748b;
  }

  .bookings-loadmore-btn {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .65rem 1.15rem;
    border: 1px solid #cfe0f7;
    border-radius: 999px;
    background: #f5faff;
    color: #0b4fd6;
    font-family: inherit;
    font-size: .85rem;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s ease, border-color .15s ease, box-shadow .15s ease;
  }

  .bookings-loadmore-btn:hover {
    background: #ffffff;
    border-color: #93c5fd;
    box-shadow: 0 8px 18px rgba(13, 43, 82, .1);
  }

  .bookings-loadmore-btn[hidden] { display: none !important; }

  .kanban-subtitle { color: var(--muted); font-size: .95rem; margin-top: .25rem; }
  .kanban-board { display: flex; gap: 1rem; margin-top: 1rem; overflow-x: auto; overflow-y: hidden; padding-bottom: .9rem; scroll-snap-type: x proximity; -webkit-overflow-scrolling: touch; transition: opacity .15s ease; }
  .kanban-column { background: #f7fbff; border: 1px solid #e6eefb; border-radius: 18px; padding: .9rem; flex: 0 0 320px; width: 320px; min-width: 320px; display: flex; flex-direction: column; gap: .75rem; scroll-snap-align: start; }
  .kanban-column.is-over { outline: 2px dashed #94a3b8; outline-offset: 2px; }
  .kanban-column__header { display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
  .kanban-column__title { margin: 0; font-size: 1rem; font-weight: 700; color: #0f172a; }
  .kanban-column__count { display: inline-flex; align-items: center; justify-content: center; min-width: 2rem; padding: .2rem .55rem; border-radius: 999px; background: #e7f0ff; color: #0b4fd6; font-weight: 800; font-size: .85rem; }
  .kanban-column__cards { display: grid; gap: .75rem; min-height: 140px; }
  .kanban-empty { border: 1px dashed #cbd5e1; border-radius: 16px; padding: 1rem; text-align: center; color: #64748b; background: rgba(255,255,255,.65); }
  .booking-card { border-radius: 16px; border: 1px solid #e3ecf8; background: #ffffff; box-shadow: 0 8px 18px rgba(13, 43, 82, .06); padding: .9rem; cursor: grab; transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease; }
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
  /* Footer fixed, sama seperti dashboard */
  body .site-footer {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 50;
    margin-top: 0;
    padding: .6rem 0;
    border-top: 1px solid rgba(148, 178, 222, .4);
    background: #ffffff;
    text-align: left;
  }

  body .site-footer-inner {
    max-width: 100%;
    padding: 0 1.25rem 0 calc(252px + 1.25rem);
  }

  body .site-footer .footer-left,
  body .site-footer .footer-right {
    font-size: .78rem;
    color: #64748b;
  }

  body.admin-shell .container { padding-bottom: 4.5rem; }

  @media (max-width: 1200px) {
    .dashboard-shell {
      grid-template-columns: minmax(0, 1fr);
      padding-left: 0;
    }

    .dashboard-sidebar { display: none; }

    .topbar-menu-btn { display: inline-flex; }

    body .site-footer-inner {
      padding-left: 1rem;
      padding-right: 1rem;
    }

    body.mobile-sidebar-open .dashboard-sidebar {
      display: flex !important;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: 270px;
      max-height: none;
      overflow-y: auto;
      z-index: 120;
      border-radius: 0;
      box-shadow: 0 0 40px rgba(0, 0, 0, .35);
    }

    body.mobile-sidebar-open::after {
      content: "";
      position: fixed;
      inset: 0;
      z-index: 110;
      background: rgba(15, 23, 42, .5);
    }

    .kanban-column { flex-basis: 300px; width: 300px; min-width: 300px; }
  }
  @media (max-width: 980px) {
    .booking-detail-grid, .booking-detail-actions { grid-template-columns: 1fr; }
    .booking-detail-modal { max-height: 92vh; }
    .notify-backdrop { width: min(320px, calc(100vw - 1.5rem)); right: .75rem; bottom: .75rem; }
    .kanban-column { flex-basis: 86vw; width: 86vw; min-width: 280px; }
  }
  @media (max-width: 768px) {
    body .site-footer { display: none; }
    body.admin-shell .container { padding-bottom: 5rem; }
  }

  @media (max-width: 640px) {
    .container { padding: .75rem .75rem 0; }
    .dashboard-topbar { top: .25rem; gap: .5rem; padding: .55rem .6rem; border-radius: 14px; }
    .topbar-search { max-width: none; }
    .topbar-search input { padding-right: 2.4rem; font-size: .84rem; }
    .topbar-shortcut { display: none; }
    .topbar-icon-btn { width: 34px; height: 34px; font-size: .92rem; }
    .topbar-user { padding: .15rem; border: none; background: transparent; }
    .topbar-user-meta, .topbar-user > i { display: none; }
    .topbar-avatar { width: 34px; height: 34px; }
    .bookings-hero { flex-direction: column; align-items: flex-start; padding: 1.1rem 1.15rem; border-radius: 18px; }
    .bookings-hero h2 { font-size: 1.3rem; }
    .bookings-hero .subtitle { font-size: .84rem; }
    .hero-actions { width: 100%; }
    .bookings-hero .btn { flex: 1 1 auto; }
    .bookings-panel { padding: .85rem .85rem 1rem; border-radius: 18px; }
    .bookings-toolbar { flex-wrap: wrap; }
    .bookings-toolbar__fields { flex: 1 1 100%; gap: .5rem; }
    .bookings-toolbar .field { flex: 1 1 30%; }
    .bookings-toolbar label { font-size: .68rem; }
    .bookings-toolbar select { padding: 0 .45rem; font-size: .82rem; }
    .kanban-board { gap: .75rem; padding-bottom: 1rem; }
    .kanban-column { flex-basis: 88vw; width: 88vw; min-width: 280px; }
  }
</style>

<div class="dashboard-shell">
  @include('partials.admin-sidebar')

  <main class="dashboard-main">
    <div class="dashboard-topbar">
      <div class="topbar-left" style="display:flex; align-items:center; gap:.6rem; flex:1 1 auto; min-width:0;">
        <button class="topbar-menu-btn" id="topbarMenuBtn" type="button" aria-label="Buka menu">
          <i class="bi bi-list"></i>
        </button>
        <form id="booking-filter-form" method="GET" action="{{ route('bookings.index') }}" class="topbar-search">
          <i class="bi bi-search"></i>
          <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, nomor, kendaraan, atau invoice..." aria-label="Cari booking" autocomplete="off" />
          <span class="topbar-shortcut">Ctrl + K</span>
        </form>
      </div>
      <div class="topbar-right">
        <a class="topbar-icon-btn" href="{{ route('bookings.trash') }}" title="Trash Bookings" aria-label="Trash Bookings">
          <i class="bi bi-trash3"></i>
        </a>
        <a class="topbar-icon-btn" href="{{ route('dashboard.calendar') }}" title="Kalender booking" aria-label="Kalender booking">
          <i class="bi bi-calendar3"></i>
        </a>
        <div class="topbar-user" title="{{ auth()->user()->name ?? 'Pengguna' }}">
          <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
          <div class="topbar-user-meta">
            <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
            <span>{{ str_replace('_', ' ', auth()->user()->role ?? 'user') }}</span>
          </div>
          <i class="bi bi-chevron-down"></i>
        </div>
      </div>
    </div>

    <section class="bookings-hero">
      <div>
        <div class="hero-eyebrow">MANAGE BOOKINGS</div>
        <h2>Kelola Booking</h2>
        <div class="subtitle">Drag card antar kolom atau buka detail untuk pindah status secara manual.</div>
      </div>
      <div class="hero-actions">
        <a class="btn btn-primary" href="{{ route('bookings.create') }}"><i class="bi bi-plus-lg"></i> Tambah Booking</a>
      </div>
    </section>

    <section class="bookings-panel">
      <div class="bookings-toolbar">
        <div class="bookings-toolbar__fields">
          <div class="field">
            <label for="has_mitra">Filter Mitra</label>
            <select id="has_mitra" name="has_mitra" form="booking-filter-form">
              <option value="" {{ empty($filters['has_mitra']) ? 'selected' : '' }}>Semua ({{ $filterCounts['total'] ?? 0 }})</option>
              <option value="yes" {{ ($filters['has_mitra'] ?? '') === 'yes' ? 'selected' : '' }}>Sudah ada Mitra ({{ $filterCounts['mitra']['yes'] ?? 0 }})</option>
              <option value="no" {{ ($filters['has_mitra'] ?? '') === 'no' ? 'selected' : '' }}>Belum ada Mitra ({{ $filterCounts['mitra']['no'] ?? 0 }})</option>
            </select>
          </div>
          <div class="field">
            <label for="group_id">Filter Group</label>
            <select id="group_id" name="group_id" form="booking-filter-form">
              <option value="">Semua Group ({{ $filterCounts['total'] ?? 0 }})</option>
              @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ (string) ($filters['group_id'] ?? '') === (string) $group->id ? 'selected' : '' }}>{{ $group->name }} ({{ $filterCounts['group'][$group->id] ?? 0 }})</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="country">Filter Negara</label>
            <select id="country" name="country" form="booking-filter-form">
              <option value="">Semua Negara ({{ $filterCounts['total'] ?? 0 }})</option>
              <option value="__none" {{ ($filters['country'] ?? '') === '__none' ? 'selected' : '' }}>Tanpa Negara ({{ $filterCounts['no_country'] ?? 0 }})</option>
              @foreach($countries as $countryName)
                <option value="{{ $countryName }}" {{ ($filters['country'] ?? '') === $countryName ? 'selected' : '' }}>{{ $countryName }} ({{ $filterCounts['country'][$countryName] ?? 0 }})</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="price">Filter Biaya</label>
            <select id="price" name="price" form="booking-filter-form">
              <option value="" {{ empty($filters['price']) ? 'selected' : '' }}>Semua ({{ $filterCounts['total'] ?? 0 }})</option>
              <option value="empty" {{ ($filters['price'] ?? '') === 'empty' ? 'selected' : '' }}>Biaya Kosong ({{ $filterCounts['price']['empty'] ?? 0 }})</option>
              <option value="filled" {{ ($filters['price'] ?? '') === 'filled' ? 'selected' : '' }}>Biaya Terisi ({{ $filterCounts['price']['filled'] ?? 0 }})</option>
            </select>
          </div>
        </div>
        <div class="bookings-total"><i class="bi bi-collection"></i> {{ $bookings->count() }} booking</div>
      </div>

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
          @php
            $phaseBookingIndex = 0;
            $phaseInitialLimit = 10;
          @endphp
          <div class="kanban-column__cards" data-drop-zone="{{ $phaseKey }}">
            @forelse(($bookingsByPhase[$phaseKey] ?? collect()) as $booking)
              @php
                $isHidden = $phaseBookingIndex >= $phaseInitialLimit;
                $phaseBookingIndex++;
              @endphp
              <article
                class="booking-card {{ $booking->kanbanCardClass() }}{{ $isHidden ? ' is-hidden-booking' : '' }}"
                draggable="true"
                data-booking-id="{{ $booking->id }}"
                data-booking-index="{{ $phaseBookingIndex - 1 }}"
                data-phase="{{ $booking->kanbanPhase() }}"
                data-phase-index="{{ $phaseBookingIndex - 1 }}"
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

            @php
              $phaseCount = $counts[$phaseKey] ?? 0;
            @endphp
            @if($phaseCount > $phaseInitialLimit)
              <div class="kanban-column__loadmore" data-phase-loadmore="{{ $phaseKey }}">
                <button type="button" class="phase-loadmore-btn"
                    data-phase="{{ $phaseKey }}"
                    data-phase-total="{{ $phaseCount }}"
                    @if($phaseCount <= $phaseInitialLimit) hidden @endif>
                  <i class="bi bi-arrow-down-circle"></i> Muat lebih ({{ $phaseCount }} booking)
                </button>
                <span class="phase-loadmore-info" data-phase-info="{{ $phaseKey }}">
                  Menampilkan {{ min($phaseInitialLimit, $phaseCount) }} dari {{ $phaseCount }} booking
                </span>
              </div>
            @endif
          </div>
        </section>
      @endforeach
    </div>
    </section>
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

<script type="application/json" id="bookingBoardData">@json($bookingBoardData)</script>
<script type="application/json" id="bookingPhaseEndpoints">@json($bookings->mapWithKeys(fn ($booking) => [$booking->id => route('bookings.phase.update', $booking)]))</script>
<script>
  window.bookingBoardData = JSON.parse(document.getElementById('bookingBoardData').textContent);
  window.bookingPhaseEndpoints = JSON.parse(document.getElementById('bookingPhaseEndpoints').textContent);
  window.bookingCsrfToken = @json(csrf_token());
  window.bookingSnapshot = @json($bookings->pluck('id'));
  window.bookingNotificationSettings = @json($notificationSoundSettings ?? []);
</script>

<script>
  (function () {
    const form = document.getElementById('booking-filter-form');
    const hasMitraEl = document.getElementById('has_mitra');
    const groupEl = document.getElementById('group_id');
    const countryEl = document.getElementById('country');
    const priceEl = document.getElementById('price');
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

    if (!form || !hasMitraEl || !groupEl || !countryEl || !priceEl || !qEl || !board || !backdrop || !modalClose || !phaseSelect || !phaseSaveBtn) {
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

    function bindCards() {
      board.querySelectorAll('.booking-card').forEach((card) => {
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

      board.querySelectorAll('[data-open-booking]').forEach((button) => {
        button.addEventListener('click', () => openModal(button.dataset.openBooking));
      });
    }

    bindCards();

    function bindDropZones() {
      board.querySelectorAll('[data-drop-zone]').forEach((zone) => {
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
    }

    bindDropZones();

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
      const groupId = groupEl.value;
      const country = countryEl.value;
      const price = priceEl.value;
      const q = qEl.value;
      if (hasMitra) params.set('has_mitra', hasMitra); else params.delete('has_mitra');
      if (groupId) params.set('group_id', groupId); else params.delete('group_id');
      if (country) params.set('country', country); else params.delete('country');
      if (price) params.set('price', price); else params.delete('price');
      if (q) params.set('q', q); else params.delete('q');
      url.search = params.toString();
      return url.toString();
    }

    const panel = document.querySelector('.bookings-panel');
    let boardRequest = null;

    function syncBoardData(doc) {
      try {
        const dataEl = doc.getElementById('bookingBoardData');
        const endpointsEl = doc.getElementById('bookingPhaseEndpoints');
        if (dataEl) window.bookingBoardData = JSON.parse(dataEl.textContent);
        if (endpointsEl) window.bookingPhaseEndpoints = JSON.parse(endpointsEl.textContent);
      } catch (_) {}
    }

    async function fetchBoard(targetUrl) {
      const url = buildUrl(targetUrl || form.action);

      if (boardRequest) boardRequest.abort();
      const controller = new AbortController();
      boardRequest = controller;

      if (panel) panel.classList.add('is-loading');

      try {
        const response = await fetch(url, {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
          cache: 'no-store',
          signal: controller.signal,
        });
        if (!response.ok) throw new Error('Gagal memuat data booking.');

        const html = await response.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const newBoard = doc.getElementById('kanbanBoard');
        if (!newBoard) throw new Error('Board tidak ditemukan.');

        const boardScrollLeft = board.scrollLeft;
        board.innerHTML = newBoard.innerHTML;
        board.scrollLeft = boardScrollLeft;

        const currentLoadmore = document.querySelector('.bookings-loadmore');
        const newLoadmore = doc.querySelector('.bookings-loadmore');
        if (currentLoadmore && newLoadmore) currentLoadmore.replaceWith(newLoadmore);

        const currentTotal = document.querySelector('.bookings-total');
        const newTotal = doc.querySelector('.bookings-total');
        if (currentTotal && newTotal) currentTotal.innerHTML = newTotal.innerHTML;

        syncBoardData(doc);

        visibleBookingLimit = loadStep;
        closeModal();
        bindCards();
        bindDropZones();
        applyVisibleLimit();

        window.history.replaceState({}, '', url);
      } catch (error) {
        if (error.name === 'AbortError') return;
        window.location.href = url;
      } finally {
        if (panel && boardRequest === controller) panel.classList.remove('is-loading');
      }
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

    const phaseLoadStep = 10;
    const phaseVisibleLimit = {};

    function applyPhaseLimit() {
      const columns = board.querySelectorAll('.kanban-column');
      columns.forEach((column) => {
        const phaseKey = column.getAttribute('data-phase');
        if (!phaseKey) return;
        if (!phaseVisibleLimit[phaseKey]) phaseVisibleLimit[phaseKey] = phaseLoadStep;

        const cards = Array.from(column.querySelectorAll('.booking-card')).sort((a, b) => {
          return Number(a.dataset.phaseIndex || 0) - Number(b.dataset.phaseIndex || 0);
        });

        cards.forEach((card, index) => {
          card.classList.toggle('is-hidden-booking', index >= phaseVisibleLimit[phaseKey]);
        });

        const info = column.querySelector('[data-phase-info="' + phaseKey + '"]');
        const btn = column.querySelector('[data-phase-loadmore="' + phaseKey + '"] button');
        if (info) {
          info.textContent = 'Menampilkan ' + Math.min(phaseVisibleLimit[phaseKey], cards.length) + ' dari ' + cards.length + ' booking';
        }
        if (btn) {
          const remaining = cards.length - phaseVisibleLimit[phaseKey];
          btn.hidden = remaining <= 0;
          if (remaining > 0) {
            btn.innerHTML = '<i class="bi bi-arrow-down-circle"></i> Muat lebih (' + Math.min(phaseLoadStep, remaining) + ' lagi)';
          }
        }
      });
    }

    document.addEventListener('click', (event) => {
      const btn = event.target.closest('.phase-loadmore-btn');
      if (!btn) return;
      const phaseKey = btn.getAttribute('data-phase');
      if (!phaseKey) return;
      phaseVisibleLimit[phaseKey] = (phaseVisibleLimit[phaseKey] || phaseLoadStep) + phaseLoadStep;
      applyPhaseLimit();
    });

    applyPhaseLimit();

    hasMitraEl.addEventListener('change', () => fetchBoard());
    groupEl.addEventListener('change', () => fetchBoard());
    countryEl.addEventListener('change', () => fetchBoard());
    priceEl.addEventListener('change', () => fetchBoard());
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

<script>
  (function () {
    const menuBtn = document.getElementById('topbarMenuBtn');
    const searchInput = document.getElementById('q');

    if (menuBtn) {
      menuBtn.addEventListener('click', function () {
        document.body.classList.toggle('mobile-sidebar-open');
      });

      document.addEventListener('click', function (e) {
        if (!document.body.classList.contains('mobile-sidebar-open')) return;
        const sidebar = document.querySelector('.dashboard-sidebar');
        if (sidebar && !sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
          document.body.classList.remove('mobile-sidebar-open');
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') document.body.classList.remove('mobile-sidebar-open');
      });
    }

    document.addEventListener('keydown', function (e) {
      if (!(e.ctrlKey || e.metaKey) || (e.key !== 'k' && e.key !== 'K')) return;
      if (!searchInput) return;
      e.preventDefault();
      searchInput.focus();
      searchInput.select();
    });
  })();
</script>
@endsection
