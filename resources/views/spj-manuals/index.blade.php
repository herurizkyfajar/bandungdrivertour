@extends('layouts.app', ['title' => 'SPJ Manual'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr) 320px; gap: 1.25rem; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; overflow: hidden; }
  .calendar-side { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; position: sticky; top: 76px; height: fit-content; }
  @media (max-width: 1200px) {
    .dashboard-wrap { grid-template-columns: 1fr; }
    .dashboard-sidebar { position: static; max-height: none; overflow: hidden; min-width: 0; }
    .calendar-side { display: none !important; }
  }
  @media (max-width: 768px) {
    .card { box-shadow: none; }
    .content-card { overflow: hidden; }
    .desktop-table { display: none !important; }
    .mobile-cards { display: block !important; }
  }
  .mobile-cards { display: none; }
  .mobile-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 1rem; margin-bottom: .75rem; }
  .mobile-card-header { display: flex; justify-content: space-between; align-items: center; gap: .5rem; margin-bottom: .25rem; }
  .mobile-card-title { font-weight: 700; color: #0f172a; font-size: .95rem; }
  .mobile-card-meta { font-size: .85rem; color: var(--muted); margin-bottom: .5rem; }
  .mobile-card-actions { display: flex; gap: .35rem; flex-wrap: wrap; }
</style>
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')
  <main class="content-card">
    <div class="actions" style="justify-content: space-between;">
      <h2>SPJ Manual</h2>
      <a class="btn btn-primary" href="{{ route('spj-manuals.create') }}">Tambah SPJ Manual</a>
    </div>

    {{-- Desktop table --}}
    <div class="table-wrap desktop-table" style="margin-top:.75rem;">
      <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
        <thead>
          <tr>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Pengemudi</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Pelanggan</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Negara</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Penumpang</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Tanggal</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Layanan</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($spjManuals as $item)
          <tr>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $item->driver_name }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $item->customer_name }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $item->country_of_origin ?: '-' }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $item->passenger_count }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $item->start_date->format('d/m/Y') }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $item->service_type ?: '-' }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              <div style="display:flex; gap:.35rem; flex-wrap:wrap;">
                <a class="btn" href="{{ route('spj-manuals.show', $item) }}?download=1" target="_blank">Download SPJ</a>
                <a class="btn" href="{{ route('spj-manuals.edit', $item) }}">Edit</a>
                <form method="POST" action="{{ route('spj-manuals.destroy', $item) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn" type="submit" onclick="return confirm('Hapus SPJ Manual ini?')">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7"><div class="subtitle">Belum ada data SPJ Manual.</div></td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div style="margin-top:.75rem;">
        {{ $spjManuals->links() }}
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards" style="margin-top:.75rem;">
      @forelse($spjManuals as $item)
      <div class="mobile-card">
        <div class="mobile-card-header">
          <div class="mobile-card-title">{{ $item->customer_name }}</div>
          <span style="font-size:.78rem; font-weight:600; padding:.2rem .6rem; border-radius:999px; background:#eff6ff; color:#2563eb;">{{ $item->passenger_count }} pax</span>
        </div>
        <div class="mobile-card-meta">
          <strong>Pengemudi:</strong> {{ $item->driver_name }}<br>
          <strong>Tanggal:</strong> {{ $item->start_date->format('d/m/Y') }} {{ $item->pickup_time }}<br>
          <strong>Layanan:</strong> {{ $item->service_type ?: '-' }}
        </div>
        <div class="mobile-card-actions">
          <a class="btn" href="{{ route('spj-manuals.show', $item) }}?download=1" target="_blank">Download SPJ</a>
          <a class="btn" href="{{ route('spj-manuals.edit', $item) }}">Edit</a>
          <form method="POST" action="{{ route('spj-manuals.destroy', $item) }}">
            @csrf
            @method('DELETE')
            <button class="btn" type="submit" onclick="return confirm('Hapus SPJ Manual ini?')">Hapus</button>
          </form>
        </div>
      </div>
      @empty
      <div class="subtitle" style="text-align:center; padding:2rem;">Belum ada data SPJ Manual.</div>
      @endforelse
      @if($spjManuals->hasPages())
      <div style="margin-top:.75rem;">
        {{ $spjManuals->links() }}
      </div>
      @endif
    </div>
  </main>
  <aside class="calendar-side">
    <h2>Quick Actions</h2>
    <div class="form-grid">
      <div class="col-12">
        <a class="btn btn-primary" href="{{ route('spj-manuals.create') }}">Tambah SPJ Manual</a>
      </div>
    </div>
  </aside>
</div>
@endsection
