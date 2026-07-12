@extends('layouts.app', ['title' => 'Surat Perintah Jalan'])

@section('content')
@php
  use App\Models\InvoiceSetting;
  $invSettings = InvoiceSetting::instance();
  $b = $booking;
  $start = optional($b->booking_date)->format('d/m/Y');
  $end = optional($b->end_date)->format('d/m/Y');
  $pickupTime = $b->pickup_time ? \Carbon\Carbon::parse($b->pickup_time)->format('H:i') : '';
  $vehicle = $b->vehicle;
  $price = $b->price ? (float) $b->price : 0;
  $paymentPlan = match($b->payment_plan) {
    'down_payment' => 'Uang Muka',
    'payment_full_transfer' => 'Pembayaran Penuh (Transfer)',
    'payment_full_on_driver' => 'Pembayaran Penuh (Bayar ke Sopir)',
    default => ucfirst($b->payment_plan ?? 'Tidak diketahui'),
  };
  $tpHtml = $b->travel_plans ?? '';
  $tpText = trim(strip_tags(str_replace(['<br>','<br/>','<br />'], "\n", $tpHtml)));
  $tpLines = array_values(array_filter(array_map(fn($l) => trim($l), preg_split("/\r\n|\n|\r/", $tpText))));
  $destination = $tpLines[0] ?? ($b->pickup_location ?? '-');
  $pickupAddr = $b->pickup_address_en ?? ($b->pickup_location ?? '-');
  $days = 0;
  if ($b->booking_date && $b->end_date) {
    $days = \Illuminate\Support\Carbon::parse($b->booking_date)->diffInDays(\Illuminate\Support\Carbon::parse($b->end_date)) + 1;
  }
  $carPriceDb = (float) ($vehicle?->price_per_day ?? 0);
  $marketingFee = max(0, ((float)$price) - $carPriceDb);
@endphp
<style>
  .spj-wrap {
    position: relative;
    isolation: isolate;
    background:#fff;
    border: 2px solid #1f2937;
    border-radius: 6px;
    padding: 12px;
    max-width: 880px;
    margin: 0 auto;
    overflow: hidden;
  }
  .spj-wrap::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: url('/Logo-Bandung-Driver-Tour.webp');
    background-repeat: repeat;
    background-size: 120px auto;
    background-position: 0 0;
    opacity: 0.06;
    pointer-events: none;
    z-index: 0;
  }
  .spj-wrap > * { position: relative; z-index: 1; }
  .spj-header { display:grid; grid-template-columns: 1fr 220px; align-items:start; gap:12px; }
  .spj-brand h2 { margin:0; font-size: 1.25rem; font-weight: 800; }
  .spj-brand .muted { line-height:1.4; }
  .spj-title { text-align:right; font-weight:800; font-size: 1.25rem; }
  .grid-2 { display:grid; grid-template-columns: 180px 1fr; gap:6px; }
  .label { color:#6b7280; }
  .section { border:1px solid #1f2937; padding:8px; margin-top:8px; }
  .muted { color:#6b7280; }
  .wysiwyg-view .w-content p { margin: 0 0 .35rem; }
  .wysiwyg-view .w-content ul, .wysiwyg-view .w-content ol { margin: .25rem 0 .35rem 1.2rem; padding-left: 1.2rem; }
  .wysiwyg-view .w-content ul { list-style: disc; }
  .wysiwyg-view .w-content ol { list-style: decimal; }
  .wysiwyg-view .w-content li { margin: 0 0 .2rem; }
</style>
@if(request()->boolean('download'))
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script>
  window.addEventListener('load', function() {
    var el = document.querySelector('.spj-wrap');
    if (!el) return;
    el.style.width = '190mm';
    el.style.maxWidth = '190mm';
    el.style.margin = '0';
    el.style.borderWidth = '1px';
    var opt = {
      margin: 5,
      filename: 'SPJ-{{ $b->id }}.pdf',
      html2canvas: { scale: 2, useCORS: true, letterRendering: true, scrollY: 0 },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
      pagebreak: { mode: ['avoid-all'] }
    };
    html2pdf().set(opt).from(el).save();
  });
</script>
@endif
<div class="spj-wrap">
  <div class="spj-header">
    <div class="spj-brand">
      <h2>{{ $invSettings->company_name }}</h2>
      <div class="muted">{!! nl2br(e($invSettings->company_address)) !!}</div>
      <div class="muted">Number: {{ $invSettings->company_phone }} • Email: {{ $invSettings->company_email }}</div>
      <div class="muted">Website: {{ $invSettings->company_website }}</div>
    </div>
    <div style="text-align:right;">
      <div class="spj-title">Surat Perintah Jalan</div>
      <div class="muted">Tanggal: {{ now()->format('d/m/Y') }}</div>
    </div>
  </div>
  <div class="section">
    <div class="grid-2">
      <div class="label">Nama Pelanggan</div><div>{{ $b->customer_name }}</div>
      <div class="label">Nomor Kontak</div><div>{{ $b->contact_number }}</div>
      <div class="label">Negara Asal</div><div>{{ $b->country_of_origin ?? '-' }}</div>
      <div class="label">Jumlah Penumpang</div><div>{{ $b->number_of_passengers ?? '-' }}</div>
      <div class="label">Tanggal Mulai</div><div>{{ $start }}</div>
      <div class="label">Waktu Penjemputan</div><div>{{ $pickupTime }}</div>
      <div class="label">Alamat Penjemputan</div><div>{{ $pickupAddr }}</div>
      <div class="label">Layanan</div><div>{{ ($vehicle?->make).' '.($vehicle?->model) }} (Sewa Mobil dengan Sopir)</div>
      <div class="label">Durasi Layanan</div><div>{{ $start }} – {{ $end }}</div>
      <div class="label">Rencana Pembayaran</div><div>{{ $paymentPlan }}</div>
      <div class="label">Rincian Perjalanan</div>
      <div>
        @if(trim($tpHtml) !== '')
          <div class="wysiwyg-view">
            <div class="w-content">{!! $tpHtml !!}</div>
          </div>
        @else
          -
        @endif
      </div>
    </div>
  </div>
  <div class="section" style="text-align:right;">
    <div class="muted">Disetujui oleh</div>
    @if($invSettings->signature_path)
      <img src="{{ asset('storage/' . $invSettings->signature_path) }}" alt="Tanda tangan {{ $invSettings->signer_name }}" style="width:120px; height:auto; margin-top:8px; margin-bottom:6px; display:inline-block;">
    @else
      <img src="{{ asset('ttd_aldi.png') }}" alt="Tanda tangan {{ $invSettings->signer_name }}" style="width:120px; height:auto; margin-top:8px; margin-bottom:6px; display:inline-block;">
    @endif
    <div style="font-weight:700; font-size:1.1rem; margin-top:6px;">{{ $invSettings->signer_name }}</div>
    <div class="muted">{{ $invSettings->signer_title }}</div>
  </div>
</div>
@endsection
