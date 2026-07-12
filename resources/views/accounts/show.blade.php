@extends('layouts.app', ['title' => 'Account Details'])

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
  .info-grid { display: grid; grid-template-columns: 160px 1fr; gap: .5rem 1rem; font-size: .95rem; }
  .info-label { font-weight: 600; color: var(--muted); }
  .info-value { color: var(--text); }
  .badge { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
  .badge-super_admin { background: #fef2f2; color: #dc2626; }
  .badge-mitra { background: #f0fdf4; color: #16a34a; }
  .badge-user { background: #f8fafc; color: #64748b; }
  .badge-draft { background: #f8fafc; color: #64748b; }
  .badge-active { background: #f0fdf4; color: #16a34a; }
  .badge-done { background: #eff6ff; color: #2563eb; }
  .itinerary-card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem; margin-bottom: .75rem; }
  .itinerary-card h4 { margin: 0 0 .5rem; }
  .day-block { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: .75rem; margin-bottom: .5rem; }
  .day-block h5 { margin: 0 0 .5rem; font-size: .9rem; }
  .act-table { width: 100%; font-size: .85rem; border-collapse: collapse; }
  .act-table td { padding: .3rem .5rem; border-bottom: 1px solid #f1f5f9; }
  .act-time { white-space: nowrap; font-weight: 600; color: var(--primary); width: 110px; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="content-card">
    <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
      <div>
          <h1>Account Details</h1>
        <div class="subtitle">Informasi akun dan itinerary milik {{ $account->name }}.</div>
      </div>
      <div style="display:flex; gap:.5rem;">
        <a class="btn" href="{{ route('accounts.edit', $account) }}">Edit</a>
        <a class="btn" href="{{ route('accounts.index') }}">Kembali</a>
      </div>
    </div>

    <div style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:1rem; margin-bottom:1.5rem;">
      <h3 style="margin:0 0 .75rem;">Data Diri</h3>
      <div class="info-grid">
        <div class="info-label">Nama</div>
        <div class="info-value">{{ $account->name }}</div>
        <div class="info-label">Email</div>
        <div class="info-value">{{ $account->email }}</div>
        <div class="info-label">Role</div>
        <div class="info-value"><span class="badge badge-{{ $account->role }}">{{ $account->role }}</span></div>
        <div class="info-label">Dibuat</div>
        <div class="info-value">{{ $account->created_at->format('d M Y H:i') }}</div>
        <div class="info-label">Diupdate</div>
        <div class="info-value">{{ $account->updated_at->format('d M Y H:i') }}</div>
      </div>
    </div>

    <div>
      <h3 style="margin:0 0 .75rem;">Itinerary</h3>
      @forelse($account->itineraries as $itinerary)
        <div class="itinerary-card">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <h4>{{ $itinerary->title }}</h4>
            <div style="display:flex; gap:.35rem; align-items:center;">
              <span class="badge badge-{{ $itinerary->status }}">{{ $itinerary->status }}</span>
              <a class="btn" href="{{ route('itineraries.pdf', $itinerary) }}" style="font-size:.8rem; padding:.3rem .7rem;">PDF</a>
            </div>
          </div>
          <div style="font-size:.9rem; color:var(--muted); margin-bottom:.5rem;">
            {{ $itinerary->start_date->format('d M Y') }} - {{ $itinerary->end_date->format('d M Y') }}
          </div>
          @if($itinerary->description)
            <div style="font-size:.9rem; margin-bottom:.5rem;">{{ $itinerary->description }}</div>
          @endif

          @foreach($itinerary->days as $day)
            <div class="day-block">
              <h5>Day {{ $day->day_number }} — {{ $day->date->format('d M Y') }}</h5>
              @if($day->activities->count())
                <table class="act-table">
                  @foreach($day->activities as $act)
                    <tr>
                      <td class="act-time">{{ substr($act->time_from, 0, 5) }} - {{ substr($act->time_to, 0, 5) }}</td>
                      <td>{{ $act->activity }}</td>
                    </tr>
                  @endforeach
                </table>
              @else
                <div style="font-size:.85rem; color:var(--muted);">No activities yet.</div>
              @endif
            </div>
          @endforeach

          @if(!$itinerary->days->count())
            <div style="font-size:.9rem; color:var(--muted);">Belum ada detail hari.</div>
          @endif
        </div>
      @empty
        <div style="padding:1rem; background:#f8fafc; border:1px solid var(--border); border-radius:12px; color:var(--muted); text-align:center;">
          Belum ada itinerary.
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
