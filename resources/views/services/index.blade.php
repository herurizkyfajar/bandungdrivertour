@extends('layouts.app', ['title' => 'Manage Services'])

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
      <h2>Manage Services</h2>
      <a class="btn btn-primary" href="{{ route('services.create') }}">Add Service</a>
    </div>

    {{-- Desktop table --}}
    <div class="table-wrap desktop-table" style="margin-top:.75rem;">
      <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
        <thead>
          <tr>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Name</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Description</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Status</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($services as $service)
          <tr>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $service->name }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $service->description ?: '-' }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $service->is_active ? 'Active' : 'Inactive' }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              <div style="display:flex; gap:.35rem; flex-wrap:wrap;">
                <a class="btn" href="{{ route('services.edit', $service) }}">Edit</a>
                <form method="POST" action="{{ route('services.destroy', $service) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn" type="submit" onclick="return confirm('Delete this service?')">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4"><div class="subtitle">No services yet.</div></td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div style="margin-top:.75rem;">
        {{ $services->links() }}
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards" style="margin-top:.75rem;">
      @forelse($services as $service)
      <div class="mobile-card">
        <div class="mobile-card-header">
          <div class="mobile-card-title">{{ $service->name }}</div>
          <span style="font-size:.78rem; font-weight:600; padding:.2rem .6rem; border-radius:999px; {{ $service->is_active ? 'background:#f0fdf4; color:#16a34a;' : 'background:#f8fafc; color:#64748b;' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        @if($service->description)
          <div class="mobile-card-meta">{{ $service->description }}</div>
        @endif
        <div class="mobile-card-actions">
          <a class="btn" href="{{ route('services.edit', $service) }}">Edit</a>
          <form method="POST" action="{{ route('services.destroy', $service) }}">
            @csrf
            @method('DELETE')
            <button class="btn" type="submit" onclick="return confirm('Delete this service?')">Delete</button>
          </form>
        </div>
      </div>
      @empty
      <div class="subtitle" style="text-align:center; padding:2rem;">No services yet.</div>
      @endforelse
      @if($services->hasPages())
      <div style="margin-top:.75rem;">
        {{ $services->links() }}
      </div>
      @endif
    </div>
  </main>
  <aside class="calendar-side">
    <h2>Quick Actions</h2>
    <div class="form-grid">
      <div class="col-12">
        <a class="btn btn-primary" href="{{ route('services.create') }}">Add Service</a>
      </div>
    </div>
  </aside>
</div>
@endsection
