<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
  @page { margin: 10mm 12mm; }
  * { box-sizing: border-box; }
  body { font-family: "DejaVu Sans", Arial, Helvetica, sans-serif; font-size: 10.5pt; color:#111827; margin:0; }
  .inv-page {
    position: relative;
    border: 2px solid #1f2937;
    border-radius: 6px;
    padding: 4mm;
    margin: 0 0 6mm;
    page-break-after: always;
  }
  .inv-page.last { page-break-after: auto; }
  .wm {
    position: absolute;
    left: 0; top: 0; right: 0; bottom: 0;
    background-image: url('{{ asset('Logo-Bandung-Driver-Tour.webp') }}');
    background-repeat: repeat;
    background-size: 110px auto;
    opacity: 0.06;
  }
  .inv-page > .content { position: relative; }
  .muted { color:#6b7280; }
  .label { color:#6b7280; }
  table.hdr { width:100%; border-collapse: collapse; }
  table.hdr td { vertical-align: top; }
  td.brand h2 { margin:0 0 2px; font-size: 14pt; font-weight: 800; }
  td.title { text-align:right; font-weight:800; font-size: 16pt; width: 45mm; vertical-align: top; }
  .logos { margin-bottom: 5px; }
  .logos img { height: 60px; width: auto; }
  table.two { width:100%; border-collapse: collapse; margin-top: 7px; }
  table.two td { width:50%; vertical-align: top; padding-right: 4px; }
  table.two td + td { padding-right: 0; padding-left: 4px; }
  .box { border:1px solid #1f2937; padding:7px; min-height: 40mm; }
  .box h4 { margin:0 0 5px; font-size: 10.5pt; }
  table.kv { width:100%; border-collapse: collapse; }
  table.kv td { padding:1.5px 0; vertical-align: top; line-height: 1.45; }
  table.kv td.lbl { color:#6b7280; width: 33mm; }
  table.inv-table { width:100%; border-collapse: collapse; margin-top:8px; }
  table.inv-table th, table.inv-table td { border:1px solid #1f2937; padding:5px 7px; vertical-align:top; }
  table.inv-table th { background:#f1f5f9; font-weight:600; text-align:left; }
  .right { text-align:right; }
  table.bottom { width:100%; border-collapse: collapse; margin-top:8px; }
  table.bottom td { vertical-align: top; }
  .pay-box { border:1px solid #1f2937; padding:7px; margin-top:6px; }
  .signature { text-align:right; }
  .signature img { width: 110px; margin-top:6px; margin-bottom:4px; }
  .sig-name { font-weight:700; font-size: 12pt; margin-top: 4px; }
  .stamp {
    position: absolute;
    top: 42%; left: 50%;
    transform: translate(-50%, -50%) rotate(-12deg);
    z-index: 5;
    font-size: 34pt;
    font-weight: 900;
    letter-spacing: 5px;
    padding: 4px 16px;
    border: 5px solid;
    border-radius: 10px;
    opacity: 0.45;
    white-space: nowrap;
  }
  .stamp-paid { color:#16a34a; border-color:#16a34a; }
  .stamp-unpaid { color:#dc2626; border-color:#dc2626; }
  .desc-title { margin: 0 0 8px; font-size: 14pt; font-weight: 800; }
  .desc-content { line-height: 1.6; }
  .desc-content p, .desc-content div { margin: 4px 0; }
  .desc-content ul { list-style: disc; padding-left: 18px; margin: 4px 0; }
  .desc-content ol { list-style: decimal; padding-left: 18px; margin: 4px 0; }
  .desc-content h1, .desc-content h2, .desc-content h3 { margin: 6px 0; font-size: 12pt; font-weight: 700; }
  .desc-content blockquote { border-left: 3px solid #cbd5e1; padding-left: 8px; margin: 4px 0; color:#334155; }
  .lic-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius: 8px; padding:5px 7px; margin-bottom:5px; font-size:7pt; line-height:1.18; color:#166534; }
  .lic-box p { margin: 0 0 3px; }
  .terms-title { margin: 0 0 3px; font-size: 8.5pt; font-weight: 800; }
  .terms-block { padding: 0 0 1px; margin-bottom: 2px; border-bottom: 1px solid #e5e7eb; }
  .terms-block:last-child { border-bottom: none; }
  .terms-block h4 { margin:0 0 1px; font-size: 7.3pt; }
  .terms-block p { margin:0 0 2px; line-height:1.18; font-size: 7pt; }
  .terms-list { margin:0; padding-left: 14px; line-height:1.18; font-size: 7pt; }
  .terms-list li { margin:0; }
  table.overtime { width:100%; border-collapse: collapse; margin-top:3px; }
  table.overtime th, table.overtime td { border:1px solid #1f2937; padding:2px 5px; font-size: 7pt; }
  table.overtime th { background:#f1f5f9; text-align:left; }
</style>
</head>
<body>
@php
  use App\Models\InvoiceSetting;
  $invSettings = InvoiceSetting::instance();
  $booking = $invoice->booking;
  $issued = optional($invoice->issued_at)->format('d/m/Y');
  $departDate = optional($booking->booking_date)->format('d/m/Y');
  $endDate = optional($booking->end_date)->format('d/m/Y');
  $pickupTime = $booking->pickup_time ? \Carbon\Carbon::parse($booking->pickup_time)->format('H:i') : '';
  $vehicle = $booking->vehicle;
  $mitraName = optional($booking->mitra)->full_name;
  $serviceName = $booking->services->pluck('name')->implode(', ') ?: optional($booking->service)->name;
  $paymentStatus = match($booking->payment_plan) {
    'down_payment' => 'Down Payment',
    'payment_full_transfer' => 'Payment Full Transfer',
    'payment_full_on_driver' => 'Cash to Driver',
    default => ucfirst($invoice->status),
  };
  $downPaymentAmount = (float) ($booking->down_payment_amount ?? 0);
  $remainingPayment = max(0, (float) $invoice->amount - $downPaymentAmount);
  $tpHtml = $booking->travel_plans ?? '';
  $allowedTags = '<b><strong><i><em><u><p><br><ul><ol><li><h1><h2><h3><blockquote><a><span><div>';
  $descHtml = trim(strip_tags($tpHtml, $allowedTags));
  if ($descHtml === '') { $descHtml = 'Trip plan'; }
@endphp

{{-- PAGE 1: INVOICE --}}
<div class="inv-page">
  <div class="wm"></div>
  @if($invoice->show_stamp)
    <div class="stamp {{ $invoice->status === 'paid' ? 'stamp-paid' : 'stamp-unpaid' }}">
      {{ $invoice->status === 'paid' ? 'PAID' : 'UNPAID' }}
    </div>
  @endif
  <div class="content">
    <table class="hdr">
      <tr>
        <td class="brand">
          @php($groups = \App\Models\Group::whereNotNull('logo_path')->orderBy('name')->get())
          @if($groups->count())
          <div class="logos">
            @foreach($groups as $g)
              <img src="{{ asset('storage/' . $g->logo_path) }}" alt="{{ $g->name }}">
            @endforeach
          </div>
          @endif
          <h2>{{ $invSettings->company_name }}</h2>
          <div class="muted">{!! nl2br(e($invSettings->company_address)) !!}</div>
          <div class="muted">Number: {{ $invSettings->company_phone }} | Email: {{ $invSettings->company_email }}</div>
          <div class="muted">Website: {{ $invSettings->company_website }}</div>
        </td>
        <td class="title">INVOICE</td>
      </tr>
    </table>

    <table class="two">
      <tr>
        <td>
          <div class="box">
            <h4>Bill To:</h4>
            <table class="kv">
              <tr><td class="lbl">Name</td><td>{{ $booking->customer_name }}</td></tr>
              <tr><td class="lbl">Phone</td><td>{{ $booking->contact_number }}</td></tr>
              <tr><td class="lbl">Country</td><td>{{ $booking->country_of_origin ?? '-' }}</td></tr>
              <tr><td class="lbl">Departure Date</td><td>{{ $departDate }}</td></tr>
              <tr><td class="lbl">Pickup Time</td><td>{{ $pickupTime }}</td></tr>
            </table>
          </div>
        </td>
        <td>
          <div class="box">
            <h4>&nbsp;</h4>
            <table class="kv">
              <tr><td class="lbl">Invoice No.</td><td>{{ $invoice->invoice_number }}</td></tr>
              <tr><td class="lbl">Date</td><td>{{ $issued }}</td></tr>
              <tr><td class="lbl">Driver</td><td>{{ $mitraName ?? '-' }}</td></tr>
              <tr><td class="lbl">Car Type</td><td>{{ ($vehicle?->make).' '.($vehicle?->model) }}</td></tr>
              <tr><td class="lbl">Pickup Point</td><td>{{ $booking->pickup_location }}</td></tr>
              <tr><td class="lbl">Service</td><td>{{ $serviceName ?? '-' }}</td></tr>
            </table>
          </div>
        </td>
      </tr>
    </table>

    <table class="inv-table">
      <thead>
        <tr>
          <th>Service</th>
          <th style="width:52mm;">Date &amp; Time</th>
          <th style="width:38mm;" class="right">Price (IDR)</th>
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
          <td class="right" style="font-weight:700;">{{ number_format($downPaymentAmount, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td colspan="2" class="right" style="font-weight:700;">Remaining Payment (IDR)</td>
          <td class="right" style="font-weight:700;">{{ number_format($remainingPayment, 0, ',', '.') }}</td>
        </tr>
        @endif
      </tbody>
    </table>

    <table class="bottom">
      <tr>
        <td style="width:62%;">
          <div class="box">
            <div class="label">Payment Status</div>
            <div>{{ $paymentStatus }}</div>
            <div class="pay-box">
              <div class="label">Payment:</div>
              <div>{{ $invSettings->bank_name }}</div>
              <div>{{ $invSettings->bank_account_number }}</div>
              <div>{{ $invSettings->bank_account_name }}</div>
            </div>
          </div>
        </td>
        <td>
          <div class="signature">
            <div class="muted">Best regards</div>
            @if($invSettings->signature_path)
              <img src="{{ asset('storage/' . $invSettings->signature_path) }}" alt="Signature">
            @else
              <img src="{{ asset('ttd_aldi.png') }}" alt="Signature">
            @endif
            <div class="sig-name">{{ $invSettings->signer_name }}</div>
            <div class="muted">{{ $invSettings->signer_title }}</div>
          </div>
        </td>
      </tr>
    </table>
  </div>
</div>

{{-- PAGE 2: DESCRIPTION --}}
<div class="inv-page">
  <div class="wm"></div>
  <div class="content desc-content">
    <h3 class="desc-title">Description</h3>
    {!! $descHtml !!}
  </div>
</div>

{{-- PAGE 3: LICENSE & TERMS --}}
<div class="inv-page last">
  <div class="wm"></div>
  <div class="content">
    <div class="lic-box">
      <div style="font-weight:700; font-size:10pt; margin-bottom:5px;">&#10004; Registered &amp; Licensed Business</div>
      <p>This business is legally registered and recognized under the Republic of Indonesia's business registration system.</p>
      @if(!empty($invSettings->ahu_certificate_number))
      <p><strong>AHU Registration Number:</strong> {{ $invSettings->ahu_certificate_number }}</p>
      <p style="margin-bottom:10px;">This AHU certificate issued by the Ministry of Law and Human Rights of the Republic of Indonesia confirms the official legal entity status of the company.</p>
      @endif
      <p><strong>Business Registration Number (NIB):</strong> {{ $invSettings->nib }}</p>
      <p>This NIB confirms that the company has fulfilled all legal requirements including business licensing, location approval, environmental permits, and building compliance in accordance with Indonesian Government Regulation No. 24 of 2018 concerning Electronic Integrated Business Licensing Services (OSS - Online Single Submission).</p>
    </div>

    <h3 class="terms-title">Rental Duration &amp; Service Terms</h3>

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
      <h4>Pickup &amp; Drop-off Duration</h4>
      <ul class="terms-list">
        <li>Pickup and drop-off duration is limited to a maximum of 60 minutes.</li>
      </ul>
    </div>

    <div class="terms-block">
      <h4>Overtime Charges</h4>
      <p>Additional charges will apply if vehicle usage exceeds the 12-hour rental duration.</p>
      <table class="overtime">
        <thead>
          <tr><th>Vehicle Type</th><th>Overtime Rate</th></tr>
        </thead>
        <tbody>
          <tr><td>Avanza &amp; Innova (MPV)</td><td>IDR 100,000 / hour</td></tr>
          <tr><td>Hiace (Mini Bus)</td><td>IDR 150,000 / hour</td></tr>
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
      <h4>Pickup &amp; Drop-off Service</h4>
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

</body>
</html>
