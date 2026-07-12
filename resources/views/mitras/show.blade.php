@extends('layouts.app', ['title' => 'Mitra Details'])

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; padding-bottom: 80px; }
  .content-card { max-width: none; margin: 0; background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; overflow: hidden; }
  .info-grid { display: grid; grid-template-columns: 160px 1fr; gap: .5rem 1rem; font-size: .95rem; }
  .info-label { font-weight: 600; color: var(--muted); }
  .info-value { color: var(--text); word-break: break-word; }
  .avatar { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; border: 1px solid var(--border); }
  .chips { display: flex; flex-wrap: wrap; gap: .35rem; }
  .chip { display: inline-block; padding: .15rem .5rem; border-radius: 999px; font-size: .85rem; background: #eef2ff; color: #3730a3; border: 1px solid #e0e7ff; }
  .booking-card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem; margin-bottom: .75rem; }
  .booking-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: .5rem; }
  .booking-card-title { font-weight: 700; font-size: 1rem; color: #0f172a; }
  .booking-card-meta { font-size: .85rem; color: var(--muted); margin-bottom: .5rem; }
  .booking-card-price { font-weight: 700; color: var(--primary); font-size: 1rem; }
  .badge { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
  .badge-masuk { background: #f1f5f9; color: #475569; }
  .badge-proses { background: #fef3c7; color: #d97706; }
  .badge-selesai { background: #dcfce7; color: #16a34a; }
  .badge-cancel { background: #fee2e2; color: #dc2626; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
  @media (max-width: 768px) {
    .info-grid { grid-template-columns: 1fr; gap: .25rem 0; }
    .info-label { margin-bottom: 0; }
    .info-value { margin-bottom: .5rem; }
    .booking-card-header { flex-direction: column; align-items: flex-start; gap: .5rem; }
  }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="content-card">
    <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;">
      <div>
        <h1 style="margin:0;">Mitra Details</h1>
        <div class="subtitle">Profile and booking history for {{ $mitra->full_name }}</div>
      </div>
      <div style="display:flex; gap:.5rem;">
        <a class="btn" href="{{ route('mitras.edit', $mitra) }}">Edit</a>
        <a class="btn" href="{{ route('mitras.index') }}">Back</a>
      </div>
    </div>

    {{-- Profile --}}
    <div style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:1rem; margin-bottom:1.5rem;">
      <div style="display:flex; gap:1rem; align-items:flex-start; flex-wrap:wrap;">
        @if($mitra->photo_path)
          <img class="avatar" src="{{ asset('storage/'.$mitra->photo_path) }}" alt="{{ $mitra->full_name }}">
        @endif
        <div style="flex:1; min-width:200px;">
          <h3 style="margin:0 0 .75rem;">{{ $mitra->full_name }}</h3>
          <div class="info-grid">
            <div class="info-label">Email</div>
            <div class="info-value">{{ $mitra->email }}</div>
            <div class="info-label">WhatsApp</div>
            <div class="info-value">{{ $mitra->whatsapp_contact }}</div>
            <div class="info-label">Apps</div>
            <div class="info-value">
              @php $apps = is_array($mitra->apps) ? $mitra->apps : (json_decode($mitra->apps ?? '[]', true) ?: []); @endphp
              @if(count($apps))
                <div class="chips">
                  @foreach($apps as $a)
                    <span class="chip">{{ ucfirst($a) }}</span>
                  @endforeach
                  @if($mitra->other_app)
                    <span class="chip">{{ $mitra->other_app }}</span>
                  @endif
                </div>
              @else
                -
              @endif
            </div>
            <div class="info-label">Registered</div>
            <div class="info-value">{{ $mitra->created_at->format('d M Y H:i') }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Vehicles --}}
    <div style="margin-bottom:1.5rem;">
      <h3 style="margin:0 0 .5rem;">Vehicles ({{ $mitra->vehicles()->count() }})</h3>
      @forelse($mitra->vehicles as $v)
        <span style="display:inline-block; padding:.3rem .65rem; border-radius:8px; font-size:.85rem; background:#f1f5f9; border:1px solid #e2e8f0; margin:0 .3rem .35rem 0; color:#334155;">
          <strong>{{ $v->make }} {{ $v->model }}</strong> &middot; {{ $v->plate_number }}@if($v->color) &middot; {{ $v->color }}@endif
        </span>
      @empty
        <div style="font-size:.85rem; color:var(--muted);">No vehicles registered.</div>
      @endforelse
    </div>

    {{-- Bookings --}}
    <div>
      <h3 style="margin:0 0 .75rem;">Bookings ({{ $mitra->bookings()->count() }})</h3>

      {{-- Desktop table --}}
      <div class="table-wrap" style="margin-top:.75rem;">
        <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
          <thead>
            <tr>
              <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Invoice</th>
              <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Customer</th>
              <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Service</th>
              <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Date</th>
              <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bookings as $b)
            <tr>
              <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
                {{ $b->invoice->invoice_number ?? '-' }}
              </td>
              <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
                {{ $b->customer_name }}
              </td>
              <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
                {{ optional($b->service)->name ?? '-' }}
              </td>
              <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
                {{ optional($b->booking_date)->format('d/m/Y') }}
              </td>
              <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
                @php $phase = $b->kanbanPhase(); @endphp
                <span class="badge badge-{{ $phase }}">{{ $b->statusLabel() }}</span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5"><div class="subtitle" style="text-align:center; padding:1.5rem;">No bookings linked to this mitra yet.</div></td>
            </tr>
            @endforelse
          </tbody>
        </table>
        <div style="margin-top:.75rem;">
          {{ $bookings->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
