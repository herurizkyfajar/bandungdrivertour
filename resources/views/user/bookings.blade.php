@extends('layouts.app', ['title' => 'My Bookings'])

@section('content')
<style>
  .booking-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
  .booking-table th { text-align: left; background: #f1f5f9; padding: .7rem .8rem; border: 1px solid #e2e8f0; font-size: .85rem; color: #475569; font-weight: 700; }
  .booking-table td { padding: .7rem .8rem; border: 1px solid #e2e8f0; font-size: .9rem; vertical-align: middle; }
  .booking-table tr:hover { background: #f8fafc; }
  .badge { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .78rem; font-weight: 700; }
  .badge-pending { background: #fef3c7; color: #92400e; }
  .badge-confirmed { background: #dbeafe; color: #1e40af; }
  .badge-completed { background: #d1fae5; color: #065f46; }
  .badge-cancelled { background: #fee2e2; color: #991b1b; }
  .badge-default { background: #f1f5f9; color: #475569; }
  .btn-delete { background: #ef4444; color: #fff; border: none; padding: .4rem .8rem; border-radius: 8px; font-size: .82rem; cursor: pointer; font-weight: 600; }
  .btn-delete:hover { background: #dc2626; }
  .empty-state { text-align: center; padding: 3rem 1rem; color: #64748b; }
  .empty-state h3 { margin-bottom: .5rem; color: #334155; }
  .my-booking-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  @media (max-width: 1200px) { .my-booking-wrap { grid-template-columns: 1fr; } .dashboard-sidebar { position: static; max-height: none; overflow: visible; } }
  @media (max-width: 768px) { .booking-table { min-width: 520px; } }
</style>

<div class="my-booking-wrap">
  @include('partials.admin-sidebar')

  <main class="content-card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <h2>My Bookings</h2>
      <a href="{{ route('booking.create') }}" class="btn btn-primary">New Booking</a>
    </div>

    @if(session('success'))
      <div style="margin-top:.75rem; padding:.75rem 1rem; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; color:#166534;">{{ session('success') }}</div>
    @endif

    @if($bookings->count())
      <div style="overflow-x:auto; margin-top:1rem;">
        <table class="booking-table">
          <thead>
            <tr>
              <th>Invoice</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Vehicle</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bookings as $booking)
              <tr>
                <td>{{ $booking->invoice?->invoice_number ?? '-' }}</td>
                <td>{{ $booking->customer_name }}</td>
                <td>
                  {{ optional($booking->booking_date)->format('d M Y') }}
                  @if($booking->end_date)
                    <br><span style="color:#64748b; font-size:.82rem;">to {{ optional($booking->end_date)->format('d M Y') }}</span>
                  @endif
                </td>
                <td>{{ trim((string) (($booking->vehicle?->make ?? '') . ' ' . ($booking->vehicle?->model ?? ''))) ?: '-' }}</td>
                <td>
                  @php
                    $status = $booking->status ?? 'baru_masuk';
                    $badgeClass = match($status) {
                      'baru_masuk' => 'badge-pending',
                      'konfirmasi', 'dijadwalkan' => 'badge-confirmed',
                      'selesai_pelayanan', 'selesai_administrasi_fee' => 'badge-completed',
                      'cancelled', 'cancel', 'batal' => 'badge-cancelled',
                      default => 'badge-default',
                    };
                  @endphp
                  <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                </td>
                <td>
                  <form method="POST" action="{{ route('user.bookings.destroy', $booking) }}" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete">Delete</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="margin-top:1rem; text-align:center;">
        {{ $bookings->links() }}
      </div>
    @else
      <div class="empty-state">
        <h3>No bookings yet</h3>
        <p>You haven't made any bookings. Start by creating one.</p>
        <a href="{{ route('booking.create') }}" class="btn btn-primary" style="margin-top:1rem;">Create Booking</a>
      </div>
    @endif
  </main>
</div>
@endsection
