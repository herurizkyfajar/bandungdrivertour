@extends('layouts.app', ['title' => 'Manage Vehicles'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr) 320px; gap: 1.25rem; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; overflow: hidden; }
  .calendar-side { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; position: sticky; top: 76px; height: fit-content; }
  .avatar { width: 48px; height: 48px; border-radius: 10px; object-fit: cover; border: 1px solid var(--border); }
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
  .mobile-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 1rem; margin-bottom: .75rem; display: flex; gap: .75rem; align-items: flex-start; }
  .mobile-card-avatar { width: 48px; height: 48px; border-radius: 10px; object-fit: cover; border: 1px solid var(--border); flex-shrink: 0; }
  .mobile-card-body { flex: 1; min-width: 0; }
  .mobile-card-title { font-weight: 700; color: #0f172a; font-size: .95rem; margin-bottom: .25rem; }
  .mobile-card-meta { font-size: .85rem; color: var(--muted); margin-bottom: .5rem; }
  .mobile-card-actions { display: flex; gap: .35rem; flex-wrap: wrap; }
</style>
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')
  <main class="content-card">
    <div class="actions" style="justify-content: space-between;">
      <h2>Manage Vehicles</h2>
      <a class="btn btn-primary" href="{{ route('vehicles.create') }}">Add Vehicle</a>
    </div>

    {{-- Desktop table --}}
    <div class="table-wrap desktop-table" style="margin-top:.75rem;">
      <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
        <thead>
          <tr>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Photo</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Plate Number</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Make</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Model</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Color</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Owner</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($vehicles as $v)
          <tr>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              @if($v->photo_path)
                <img class="avatar" src="{{ asset('storage/'.$v->photo_path) }}" alt="{{ $v->plate_number }}">
              @else
                -
              @endif
            </td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $v->plate_number }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $v->make }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $v->model }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $v->color }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $v->mitras->pluck('full_name')->implode(', ') ?: '-' }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              <div style="display:flex; gap:.35rem; flex-wrap:wrap;">
                <a class="btn" href="{{ route('vehicles.edit', $v) }}">Edit</a>
                <form method="POST" action="{{ route('vehicles.destroy', $v) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn" type="submit" onclick="return confirm('Delete this vehicle?')">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7"><div class="subtitle">No vehicles yet.</div></td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div style="margin-top:.75rem;">
        {{ $vehicles->links() }}
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards" style="margin-top:.75rem;">
      @forelse($vehicles as $v)
      <div class="mobile-card">
        @if($v->photo_path)
          <img class="mobile-card-avatar" src="{{ asset('storage/'.$v->photo_path) }}" alt="{{ $v->plate_number }}">
        @endif
        <div class="mobile-card-body">
          <div class="mobile-card-title">{{ $v->plate_number }}</div>
          <div class="mobile-card-meta">{{ $v->make }} {{ $v->model }} &middot; {{ $v->color }} &middot; {{ $v->mitras->pluck('full_name')->implode(', ') ?: '-' }}</div>
          <div class="mobile-card-actions">
            <a class="btn" href="{{ route('vehicles.edit', $v) }}">Edit</a>
            <form method="POST" action="{{ route('vehicles.destroy', $v) }}">
              @csrf
              @method('DELETE')
              <button class="btn" type="submit" onclick="return confirm('Delete this vehicle?')">Delete</button>
            </form>
          </div>
        </div>
      </div>
      @empty
      <div class="subtitle" style="text-align:center; padding:2rem;">No vehicles yet.</div>
      @endforelse
      @if($vehicles->hasPages())
      <div style="margin-top:.75rem;">
        {{ $vehicles->links() }}
      </div>
      @endif
    </div>
  </main>
  <aside class="calendar-side">
    <h2>Quick Actions</h2>
    <div class="form-grid">
      <div class="col-12">
        <a class="btn btn-primary" href="{{ route('vehicles.create') }}">Add Vehicle</a>
      </div>
    </div>
  </aside>
</div>
@endsection
