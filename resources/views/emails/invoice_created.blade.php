@php
  $booking = $invoice->booking;
  $amount = number_format((float)($invoice->amount ?? 0), 0, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Invoice Notification</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.5;">
  <h2 style="margin-bottom: 8px;">Invoice Baru Masuk</h2>
  <p style="margin-top: 0; color: #4b5563;">Sistem berhasil membuat invoice baru.</p>

  <table cellpadding="6" cellspacing="0" border="0" style="border-collapse: collapse;">
    <tr><td><strong>Invoice</strong></td><td>: {{ $invoice->invoice_number }}</td></tr>
    <tr><td><strong>Customer</strong></td><td>: {{ $booking?->customer_name ?? '-' }}</td></tr>
    <tr><td><strong>Contact</strong></td><td>: {{ $booking?->contact_number ?? '-' }}</td></tr>
    <tr><td><strong>Service</strong></td><td>: {{ $booking?->service?->name ?? '-' }}</td></tr>
    <tr><td><strong>Driver</strong></td><td>: {{ $booking?->mitra?->full_name ?? '-' }}</td></tr>
    <tr><td><strong>Vehicle</strong></td><td>: {{ trim(($booking?->vehicle?->make ?? '') . ' ' . ($booking?->vehicle?->model ?? '')) ?: '-' }}</td></tr>
    <tr><td><strong>Pickup</strong></td><td>: {{ $booking?->pickup_location ?? '-' }}</td></tr>
    <tr><td><strong>Total</strong></td><td>: IDR {{ $amount }}</td></tr>
  </table>

  <p style="margin-top: 16px;">
    <a href="{{ route('invoice.show', $invoice) }}" style="display:inline-block; padding:10px 14px; background:#2563eb; color:#fff; text-decoration:none; border-radius:8px;">Lihat Invoice</a>
  </p>
</body>
</html>
