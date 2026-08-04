@extends('layouts.app', ['title' => 'Invoice'])

@section('content')
@php
  use Illuminate\Support\Str;
  use App\Models\InvoiceSetting;
  $invSettings = InvoiceSetting::instance();
  $booking = $invoice->booking;
  $issued = optional($invoice->issued_at)->format('d/m/Y');
  $departDate = optional($booking->booking_date)->format('d/m/Y');
  $endDate = optional($booking->end_date)->format('d/m/Y');
  $pickupTime = $booking->pickup_time ? \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') : '';
  $vehicle = $booking->vehicle;
  $mitraName = optional($booking->mitra)->full_name;
  $serviceName = optional($booking->service)->name;
  $paymentStatus = match($booking->payment_plan) {
    'down_payment' => 'Down Payment',
    'payment_full_transfer' => 'Payment Full Transfer',
    'payment_full_on_driver' => 'Cash to Driver',
    default => ucfirst($invoice->status),
  };
  $downPaymentAmount = (float) ($booking->down_payment_amount ?? 0);
  $remainingPayment = max(0, (float) $invoice->amount - $downPaymentAmount);
  // Travel plans HTML for description page
  $tpHtml = $booking->travel_plans ?? '';
  $allowedTags = '<b><strong><i><em><u><p><br><ul><ol><li><h1><h2><h3><blockquote><a><span><div>';
  $descHtml = trim(strip_tags($tpHtml, $allowedTags));
  if ($descHtml === '') { $descHtml = 'Trip plan'; }
  $qrData = url('/');
  $safeInvoiceNumber = trim(preg_replace('/[\\\\\/:*?"<>|]+/', '', (string) $invoice->invoice_number));
  $safeInvoiceNumber = preg_replace('/\s+/', ' ', $safeInvoiceNumber);
  if ($safeInvoiceNumber === '') { $safeInvoiceNumber = 'invoice'; }
  $safeCustomerName = trim(preg_replace('/[\\\\\/:*?"<>|]+/', '', (string) ($booking->customer_name ?? 'Customer')));
  $safeCustomerName = preg_replace('/\s+/', ' ', $safeCustomerName);
  if ($safeCustomerName === '') { $safeCustomerName = 'Customer'; }
@endphp
<style>
  @page { size: A4; margin: 12mm; }
  @media print {
    html, body { width: 210mm; height: auto; }
    body * { visibility: hidden !important; }
    .inv-pages, .inv-pages * { visibility: visible !important; }
    .inv-pages { position: absolute; left: 0; top: 0; width: 186mm; max-width: 186mm; margin: 0; }
    .inv-wrap {
      width: 186mm;
      max-width: 186mm;
      margin: 0;
      border: 0;
      padding: 0;
      overflow: hidden;
      page-break-inside: avoid;
    }
    .inv-wrap::before {
      opacity: 0.05;
      background-size: 110px auto;
    }
    .inv-wrap.page-break { page-break-after: always; }
    .terms-page { font-size: 10.4pt; }
    .terms-page .terms-title { margin: 0 0 4px; font-size: 1.1rem; }
    .terms-page .terms-block { padding: 0 0 4px; margin-bottom: 4px; }
    .terms-page .terms-block h4 { margin: 0 0 3px; font-size: .92rem; }
    .terms-page .terms-block p { margin: 0 0 3px; line-height: 1.4; }
    .terms-page .terms-list { line-height: 1.4; }
    .terms-page .terms-list li { margin: 0 0 2px; }
    .terms-page ul { list-style: disc; padding-left: 20px; margin: 0 0 4px; line-height: 1.4; }
    .terms-page ol { list-style: decimal; padding-left: 20px; margin: 0 0 4px; line-height: 1.4; }
    .terms-page li { margin: 0 0 2px; }
    .terms-page .overtime-table th, .terms-page .overtime-table td { padding: 5px 7px; }
    .inv-header, .inv-two, .inv-bottom, .inv-table, .box { page-break-inside: avoid; }
    table.inv-table { font-size: 11pt; }
    table.inv-table th, table.inv-table td { padding: 4px 6px; }
    .inv-brand h2 { font-size: 14pt; }
    .muted { color:#6b7280; }
    .desc-page { font-size: 11pt; }
    .desc-page .desc-title { margin: 0 0 10px; font-size: 1.1rem; font-weight: 800; }
    .desc-page .desc-content { line-height: 1.6; }
    .desc-page .desc-content p { margin: 0.25rem 0; }
    .desc-page .desc-content div { margin: 0.25rem 0; }
    .desc-page .desc-content ul { list-style: disc; padding-left: 1.25rem; }
    .desc-page .desc-content ol { list-style: decimal; padding-left: 1.25rem; }
    .desc-page .desc-content li { margin: 0.15rem 0; }
    .desc-page .desc-content h1, .desc-page .desc-content h2, .desc-page .desc-content h3 { margin: 0.25rem 0; font-size: 1rem; font-weight: 700; }
    .desc-page .desc-content blockquote { border-left: 3px solid #cbd5e1; padding-left: 0.5rem; margin: 0.25rem 0; color: #334155; }
  }
  .inv-pages { max-width: 880px; margin: 0 auto; }
  .inv-wrap {
    position: relative;
    isolation: isolate;
    background:#fff;
    border: 2px solid #1f2937;
    border-radius: 6px;
    padding: 12px;
    max-width: 880px;
    margin: 0 auto 12px;
    overflow: hidden;
  }
  .inv-wrap::before {
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
  .inv-wrap > * {
    position: relative;
    z-index: 1;
  }
  .inv-header { display:grid; grid-template-columns: 1fr 200px; align-items:start; gap: 12px; }
  .inv-brand h2 { margin:0; font-size: 1.25rem; font-weight: 800; }
  .muted { color:#6b7280; }
  .inv-title { text-align:right; font-weight:800; font-size: 1.25rem; }
  .inv-two { display:grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 8px; }
  .box { border:1px solid #1f2937; padding:8px; min-height: 112px; }
  .box h4 { margin:0 0 6px; font-size: .95rem; }
  .grid-2 { display:grid; grid-template-columns: 140px 1fr; gap:6px; }
  .label { color:#6b7280; }
  table.inv-table { width:100%; border-collapse: collapse; margin-top:8px; }
  table.inv-table th, table.inv-table td { border:1px solid #1f2937; padding:6px 8px; vertical-align:top; }
  table.inv-table th { background:#f1f5f9; font-weight:600; }
  .right { text-align:right; }
  .inv-bottom { display:grid; grid-template-columns: 1fr 220px; gap: 8px; margin-top:8px; align-items:start; }
  .info-list { font-size:.9rem; line-height:1.5; }
  .signature { text-align:right; }
  .pay-box { border:1px solid #1f2937; padding:8px; }
  .terms-title { margin: 0 0 8px; font-size: 1.2rem; font-weight: 800; }
  .terms-block { padding: 0 0 6px; margin-bottom: 6px; border-bottom: 1px solid #e5e7eb; }
  .terms-block:last-child { border-bottom: none; }
  .terms-block h4 { margin:0 0 4px; font-size: .95rem; }
  .terms-block p { margin:0 0 4px; line-height:1.5; }
  .terms-list { margin:0; padding-left:18px; line-height:1.5; }
  .terms-list li { margin:0 0 4px; }
  .terms-page ul, .terms-page ul ul { list-style: disc; padding-left: 20px; margin: 0 0 6px; }
  .terms-page ol, .terms-page ol ol { list-style: decimal; padding-left: 20px; margin: 0 0 6px; }
  .terms-page li { margin: 0 0 4px; }
  .overtime-table { width:100%; border-collapse: collapse; margin-top:6px; }
  .overtime-table th, .overtime-table td { border:1px solid #1f2937; padding:6px 8px; }
  .overtime-table th { background:#f1f5f9; text-align:left; }
  /* Description page styles */
  .desc-page { line-height:1.6; }
  .desc-page .desc-title { margin: 0 0 10px; font-size: 1.2rem; font-weight: 800; }
  .desc-page .desc-content p { margin:.25rem 0; }
  .desc-page .desc-content div { margin:.25rem 0; }
  .desc-page .desc-content ul { list-style: disc; padding-left: 1.25rem; }
  .desc-page .desc-content ol { list-style: decimal; padding-left: 1.25rem; }
  .desc-page .desc-content li { margin:.15rem 0; }
  .desc-page .desc-content ul, .desc-page .desc-content ol { margin:.25rem 0 .25rem 1rem; }
  .desc-page .desc-content h1, .desc-page .desc-content h2, .desc-page .desc-content h3 { margin:.25rem 0; font-size:1rem; font-weight:700; }
  .desc-page .desc-content blockquote { border-left:3px solid #cbd5e1; padding-left:.5rem; margin:.25rem 0; color:#334155; }
</style>
@if(request()->boolean('print'))
<script>
  window.addEventListener('load', function() {
    window.print();
  });
</script>
@endif
@if(request()->boolean('download'))
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script>
  window.addEventListener('load', async function() {
    document.body.setAttribute('data-download', '1');
    var allPages = Array.from(document.querySelectorAll('.inv-pages .inv-wrap'));
    var pages = allPages;
    if (!pages.length) return;

    try {
      if (!window.jspdf || !window.jspdf.jsPDF || !window.html2canvas) {
        throw new Error('PDF libraries not ready');
      }

      var jsPDF = window.jspdf.jsPDF;
      var pdf = new jsPDF({ unit: 'mm', format: 'a4', orientation: 'portrait' });
      var margin = 8;
      var pageWidth = 210;
      var pageHeight = 297;
      var maxW = pageWidth - (margin * 2);
      var maxH = pageHeight - (margin * 2);

      for (var i = 0; i < pages.length; i++) {
        var canvas = await window.html2canvas(pages[i], {
          scale: 2,
          useCORS: true,
          letterRendering: true,
          backgroundColor: '#ffffff',
          scrollY: 0
        });

        var imgW = canvas.width;
        var imgH = canvas.height;
        var ratio = Math.min(maxW / imgW, maxH / imgH);
        var renderW = imgW * ratio;
        var renderH = imgH * ratio;
        var x = (pageWidth - renderW) / 2;
        var y = margin;

        if (i > 0) pdf.addPage();
        pdf.addImage(canvas.toDataURL('image/jpeg', 0.98), 'JPEG', x, y, renderW, renderH, undefined, 'FAST');
      }

      pdf.save(@json($safeInvoiceNumber . '_' . $safeCustomerName . '.pdf'));
    } catch (e) {
      // Fallback to html2pdf default flow if custom renderer fails.
      if (window.html2pdf) {
        await window.html2pdf().set({
          margin: [8, 8, 8, 8],
          filename: @json($safeInvoiceNumber . '_' . $safeCustomerName . '.pdf'),
          html2canvas: { scale: 2, useCORS: true, letterRendering: true, scrollY: 0 },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
          pagebreak: { mode: ['css'] }
        }).from(document.querySelector('.inv-pages .inv-wrap')).save();
      }
    }
    if (history.length > 1) { history.back(); }
  });
  </script>
@endif
<div class="inv-pages">
  {{-- PAGE 1: Invoice (Service instead of Description) --}}
  <div class="inv-wrap page-break">
    <div class="inv-header">
      <div class="inv-brand">
        @php($groups = \App\Models\Group::whereNotNull('logo_path')->orderBy('name')->get())
        @if($groups->count())
        <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:6px;">
          @foreach($groups as $g)
            @if($g->website)
              <a href="{{ $g->website }}" target="_blank" rel="noopener" title="{{ $g->name }}">
                <img src="{{ asset('storage/' . $g->logo_path) }}" alt="{{ $g->name }}" style="height:70px; width:auto; object-fit:contain;">
              </a>
            @else
              <img src="{{ asset('storage/' . $g->logo_path) }}" alt="{{ $g->name }}" title="{{ $g->name }}" style="height:70px; width:auto; object-fit:contain;">
            @endif
          @endforeach
        </div>
        @endif
        <h2>{{ $invSettings->company_name }}</h2>
        <div class="muted">{!! nl2br(e($invSettings->company_address)) !!}</div>
        <div class="muted">Number: {{ $invSettings->company_phone }} | Email: {{ $invSettings->company_email }}</div>
        <div class="muted">Website: {{ $invSettings->company_website }}</div>
      </div>
      <div class="inv-title">INVOICE</div>
    </div>
    <div class="inv-two">
      <div class="box">
        <h4>Bill To:</h4>
        <div class="grid-2">
          <div class="label">Name</div><div>{{ $booking->customer_name }}</div>
          <div class="label">Phone</div><div>{{ $booking->contact_number }}</div>
          <div class="label">Country</div><div>{{ $booking->country_of_origin ?? '-' }}</div>
          <div class="label">Departure Date</div><div>{{ $departDate }}</div>
          <div class="label">Pickup Time</div><div>{{ $pickupTime }}</div>
        </div>
      </div>
      <div class="box">
        <div class="grid-2">
          <div class="label">Invoice No.</div><div>{{ $invoice->invoice_number }}</div>
          <div class="label">Date</div><div>{{ $issued }}</div>
          <div class="label">Driver</div><div>{{ $mitraName ?? '-' }}</div>
          <div class="label">Car Type</div><div>{{ ($vehicle?->make).' '.($vehicle?->model) }}</div>
          <div class="label">Pickup Point</div><div>{{ $booking->pickup_location }}</div>
          <div class="label">Service</div><div>{{ $serviceName ?? '-' }}</div>
        </div>
      </div>
    </div>
    <table class="inv-table">
      <thead>
        <tr>
          <th>Service</th>
          <th style="width:220px;">Date & Time</th>
          <th style="width:160px;" class="right">Price (IDR)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ $serviceName ?? '-' }}</td>
          <td>
            @if(!empty($endDate)) {{ $departDate . ' - ' . $endDate }} @else {{ $departDate }} @endif
            @if($pickupTime)<br>{{ $pickupTime }}@endif
          </td>
          <td class="right">{{ number_format($invoice->amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td colspan="2" class="right" style="font-weight:700;">Total (IDR)</td>
          <td class="right" style="font-weight:700;">{{ number_format($invoice->amount, 0, ',', '.') }}</td>
        </tr>
        @if($booking->payment_plan === 'down_payment')
        <tr>
          <td colspan="2" class="right" style="font-weight:700;">Down Payment Paid (IDR)</td>
          <td class="right" style="font-weight:700;">
            {{ number_format($downPaymentAmount, 0, ',', '.') }}
          </td>
        </tr>
        <tr>
          <td colspan="2" class="right" style="font-weight:700;">Remaining Payment (IDR)</td>
          <td class="right" style="font-weight:700;">
            {{ number_format($remainingPayment, 0, ',', '.') }}
          </td>
        </tr>
        @endif
      </tbody>
    </table>
    <div class="inv-bottom">
    <div>
      <div class="box">
        <div style="display:grid; grid-template-columns: 1fr; gap:8px; align-items:start; margin-top:8px;">
          <div>
            <div class="label">Payment Status</div>
            <div>{{ $paymentStatus }}</div>
          </div>
        </div>
        <div class="pay-box" style="margin-top:8px;">
          <div class="label">Payment:</div>
          <div>{{ $invSettings->bank_name }}</div>
          <div>{{ $invSettings->bank_account_number }}</div>
          <div>{{ $invSettings->bank_account_name }}</div>
        </div>
      </div>
    </div>
    <div>
      <div class="signature">
        <div class="muted">Best regards</div>
        @if($invSettings->signature_path)
          <img src="{{ asset('storage/' . $invSettings->signature_path) }}" alt="Tanda tangan {{ $invSettings->signer_name }}" style="width:120px; height:auto; margin-top:8px; margin-bottom:6px; display:inline-block;">
        @else
          <img src="{{ asset('ttd_aldi.png') }}" alt="Tanda tangan {{ $invSettings->signer_name }}" style="width:120px; height:auto; margin-top:8px; margin-bottom:6px; display:inline-block;">
        @endif
        <div style="font-weight:700; font-size:1.1rem; margin-top:6px;">{{ $invSettings->signer_name }}</div>
        <div class="muted">{{ $invSettings->signer_title }}</div>
      </div>
    </div>
  </div>
  </div>

  {{-- PAGE 2: Description / Travel Plans --}}
  <div class="inv-wrap page-break desc-page">
    <h3 class="desc-title">Description</h3>
    <div class="desc-content">
      {!! $descHtml !!}
    </div>
  </div>

  {{-- PAGE 3: Registered & Licensed Business + Rental Duration & Service Terms --}}
  <div class="inv-wrap terms-page">
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px 20px; margin-bottom:20px; font-size:.9rem; line-height:1.6; color:#166534;">
      <div style="font-weight:700; font-size:.95rem; margin-bottom:6px;">✔ Registered & Licensed Business</div>
      <p style="margin:0 0 6px;">This business is legally registered and recognized under the Republic of Indonesia's business registration system.</p>
      @if(!empty($invSettings->ahu_certificate_number))
      <p style="margin:0 0 6px;"><strong>AHU Registration Number:</strong> {{ $invSettings->ahu_certificate_number }}</p>
      <p style="margin:0 0 12px;">This AHU certificate issued by the Ministry of Law and Human Rights of the Republic of Indonesia confirms the official legal entity status of the company.</p>
      @endif
      <p style="margin:0 0 6px;"><strong>Business Registration Number (NIB):</strong> {{ $invSettings->nib }}</p>
      <p style="margin:0;">This NIB confirms that the company has fulfilled all legal requirements including business licensing, location approval, environmental permits, and building compliance in accordance with Indonesian Government Regulation No. 24 of 2018 concerning Electronic Integrated Business Licensing Services (OSS - Online Single Submission).</p>
    </div>

    <h3 class="terms-title">Rental Duration & Service Terms</h3>

    @if(!empty($invSettings->terms_html))
      {!! $invSettings->terms_html !!}
    @else
    <div class="terms-block">
      <h4>Rental Duration</h4>
      <ul class="terms-list">
        <li>The rental duration for City Tour services (Lembang, Ciwidey, Bandung, and Jakarta tours) is a maximum of 12 hours.</li>
        <li>The rental duration for Company Visit services is a maximum of 12 hours.</li>
        <li>The 12-hour duration for City Tour and Company Visit services is calculated from the agreed pickup time.</li>
      </ul>
    </div>

    <div class="terms-block">
      <h4>Pickup & Drop-off Duration</h4>
      <ul class="terms-list">
        <li>Pickup and drop-off duration is limited to a maximum of 60 minutes.</li>
      </ul>
    </div>

    <div class="terms-block">
      <h4>Overtime Charges</h4>
      <p>Additional charges will apply if vehicle usage exceeds the 12-hour rental duration.</p>
      <table class="overtime-table">
        <thead>
          <tr>
            <th>Vehicle Type</th>
            <th>Overtime Rate</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Avanza & Innova (MPV)</td>
            <td>IDR 100,000 / hour</td>
          </tr>
          <tr>
            <td>Hiace (Mini Bus)</td>
            <td>IDR 150,000 / hour</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="terms-block">
      <h4>Cancellation Policy</h4>
      <p>We understand that plans may change. However, the following cancellation terms apply to all confirmed bookings:</p>
      <ul class="terms-list">
        <li><strong>Deposit Non-Refundable:</strong> Any down payment (DP) made at the time of booking is non-refundable in the event of cancellation by the customer.</li>
        <li><strong>Late Cancellation Fee:</strong> Cancellations made within 24 hours before the scheduled service will incur a penalty of 30% of the total booking cost.</li>
        <li><strong>No-Show:</strong> If the customer fails to appear at the agreed pickup location without prior notice, the full booking amount shall be deemed non-refundable.</li>
        <li><strong>Force Majeure:</strong> Cancellations due to unforeseeable circumstances beyond the customer's or company's control (e.g., natural disasters, government restrictions) will be reviewed on a case-by-case basis. A rescheduling option may be offered at no additional cost, subject to availability.</li>
      </ul>
    </div>

    <div class="terms-block">
      <h4>Rescheduling Policy</h4>
      <p>Rescheduling requests must be made at least 24 hours before the original pickup time. Rescheduling is subject to vehicle and driver availability. No additional fees will be charged for the first rescheduling, provided the notice period is met.</p>
    </div>

    <div class="terms-block">
      <h4>Pickup & Drop-off Service</h4>
      <p>Pickup and drop-off / point-to-point services are provided according to the agreed schedule and location.</p>
    </div>

    <div class="terms-block">
      <h4>Guest Delay</h4>
      <p>If the guest/customer is delayed beyond the agreed time, additional charges may apply based on operational considerations.</p>
    </div>

    <div class="terms-block">
      <h4>Additional Pickup or Drop-off Locations</h4>
      <p>Any request for additional pickup or drop-off locations outside the previously agreed route, location, or schedule will incur extra charges based on:</p>
      <ul class="terms-list">
        <li>Additional travel distance</li>
        <li>Extra usage duration</li>
        <li>Type of vehicle used</li>
      </ul>
    </div>

    <div class="terms-block">
      <h4>Notes</h4>
      <p>For a smooth and comfortable trip, please confirm all schedules and travel locations before the departure date.</p>
    </div>
    @endif
  </div>
</div>
@endsection
