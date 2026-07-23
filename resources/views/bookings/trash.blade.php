@extends('layouts.app', ['title' => 'Trash Bookings'])

@section('content')
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <main class="content-card">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem;">
      <div>
        <h1 style="margin:0;">Trash Bookings</h1>
        <div class="subtitle">Booking yang dihapus akan muncul di sini. Anda bisa restore atau hapus permanen.</div>
      </div>
      <a href="{{ route('bookings.index') }}" class="btn">Kembali ke Bookings</a>
    </div>

    @if(session('success'))
        <div style="padding:.75rem 1rem; border-radius:8px; background:#d1fae5; color:#065f46; margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    @if($bookings->isEmpty())
        <div style="text-align:center; padding:3rem 1rem; color:#94a3b8;">
            <div style="font-size:2rem; margin-bottom:.5rem;">🗑️</div>
            <div style="font-size:1.1rem;">Trash kosong.</div>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:.9rem;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border); text-align:left;">
                        <th style="padding:.6rem .5rem;">Customer</th>
                        <th style="padding:.6rem .5rem;">Phone</th>
                        <th style="padding:.6rem .5rem;">Tanggal</th>
                        <th style="padding:.6rem .5rem;">Mobil</th>
                        <th style="padding:.6rem .5rem;">Status</th>
                        <th style="padding:.6rem .5rem;">Dihapus</th>
                        <th style="padding:.6rem .5rem; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $b)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:.6rem .5rem;">{{ $b->customer_name }}</td>
                        <td style="padding:.6rem .5rem;">{{ $b->contact_number }}</td>
                        <td style="padding:.6rem .5rem;">{{ $b->booking_date ? $b->booking_date->format('d M Y') : '-' }}</td>
                        <td style="padding:.6rem .5rem;">{{ $b->vehicle ? $b->vehicle->make . ' ' . $b->vehicle->model : '-' }}</td>
                        <td style="padding:.6rem .5rem;">
                            <span style="display:inline-block; padding:.15rem .5rem; border-radius:6px; font-size:.8rem; background:#fee2e2; color:#991b1b;">{{ $b->statusLabel() }}</span>
                        </td>
                        <td style="padding:.6rem .5rem; color:#94a3b8; font-size:.85rem;">{{ $b->deleted_at->format('d M Y H:i') }}</td>
                        <td style="padding:.6rem .5rem; text-align:center;">
                            <div style="display:flex; gap:.4rem; justify-content:center;">
                                <form method="POST" action="{{ route('bookings.restore', $b->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn" style="font-size:.8rem; padding:.3rem .7rem; background:#3b82f6; color:#fff; border:none; border-radius:6px; cursor:pointer;">Restore</button>
                                </form>
                                <form method="POST" action="{{ route('bookings.force-delete', $b->id) }}" style="display:inline;" onsubmit="return confirm('Hapus permanen booking ini? Data tidak bisa dikembalikan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn" style="font-size:.8rem; padding:.3rem .7rem; background:#ef4444; color:#fff; border:none; border-radius:6px; cursor:pointer;">Hapus Permanen</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.5rem; text-align:center;">
            {{ $bookings->links() }}
        </div>
    @endif
  </main>
</div>
@endsection
