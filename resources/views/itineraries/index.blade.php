@extends('layouts.app', ['title' => 'Manage Itineraries'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr) 320px; gap: 1.25rem; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; overflow: hidden; }
  .calendar-side { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; position: sticky; top: 76px; height: fit-content; }
  .badge { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
  .badge-draft { background: #f8fafc; color: #64748b; }
  .badge-active { background: #f0fdf4; color: #16a34a; }
  .badge-done { background: #eff6ff; color: #2563eb; }
  .search-bar { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; }
  .search-bar input { padding: .6rem .9rem; border: 1px solid #cbd5e1; border-radius: 10px; height: 40px; min-width: 0; flex: 1 1 200px; }
  .search-bar button { padding: .6rem 1rem; border-radius: 10px; background: var(--primary); color: #fff; border: none; font-weight: 600; cursor: pointer; }
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
  .mobile-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; margin-bottom: .5rem; }
  .mobile-card-title { font-weight: 700; color: #0f172a; font-size: .95rem; }
  .mobile-card-meta { display: flex; flex-wrap: wrap; gap: .5rem .75rem; font-size: .85rem; color: var(--muted); margin-bottom: .75rem; }
  .mobile-card-meta span { display: flex; align-items: center; gap: .25rem; }
  .mobile-card-actions { display: flex; gap: .35rem; flex-wrap: wrap; }
</style>
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')
  <main class="content-card">
    <div class="actions" style="justify-content: space-between;">
      <h2>Manage Itineraries</h2>
      <a class="btn btn-primary" href="{{ route('itineraries.create') }}">Add Itinerary</a>
    </div>
    @if(session('success'))
      <div style="margin-top:.75rem; padding:.75rem 1rem; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; color:#166534;">{{ session('success') }}</div>
    @endif
    <form method="GET" class="search-bar" style="margin-top:.75rem;">
      <input type="text" name="search" placeholder="Search by user name..." value="{{ request('search') }}">
      <button type="submit">Search</button>
      @if(request('search'))
        <a href="{{ route('itineraries.index') }}" style="color:var(--muted); text-decoration:none;">Reset</a>
      @endif
    </form>

    {{-- Desktop table --}}
    <div class="table-wrap desktop-table" style="margin-top:.75rem;">
      <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
        <thead>
          <tr>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Title</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">User</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Date</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Status</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($itineraries as $itinerary)
          <tr>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $itinerary->title }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $itinerary->user->name }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $itinerary->start_date->format('d M Y') }} - {{ $itinerary->end_date->format('d M Y') }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;"><span class="badge badge-{{ $itinerary->status }}">{{ $itinerary->status }}</span></td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              <div style="display:flex; gap:.35rem; flex-wrap:wrap;">
                <a class="btn" href="{{ route('itineraries.pdf', $itinerary) }}">PDF</a>
                <a class="btn" href="{{ route('itineraries.edit', $itinerary) }}">Edit</a>
                <form method="POST" action="{{ route('itineraries.destroy', $itinerary) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn" type="submit" onclick="return confirm('Delete this itinerary?')">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5"><div class="subtitle">No itineraries yet.</div></td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div style="margin-top:.75rem;">
        {{ $itineraries->links() }}
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards" style="margin-top:.75rem;">
      @forelse($itineraries as $itinerary)
      <div class="mobile-card">
        <div class="mobile-card-header">
          <div class="mobile-card-title">{{ $itinerary->title }}</div>
          <span class="badge badge-{{ $itinerary->status }}">{{ $itinerary->status }}</span>
        </div>
        <div class="mobile-card-meta">
          <span>{{ $itinerary->user->name }}</span>
          <span>{{ $itinerary->start_date->format('d M Y') }} - {{ $itinerary->end_date->format('d M Y') }}</span>
        </div>
        <div class="mobile-card-actions">
          <a class="btn" href="{{ route('itineraries.pdf', $itinerary) }}">PDF</a>
          <a class="btn" href="{{ route('itineraries.edit', $itinerary) }}">Edit</a>
          <form method="POST" action="{{ route('itineraries.destroy', $itinerary) }}">
            @csrf
            @method('DELETE')
            <button class="btn" type="submit" onclick="return confirm('Delete this itinerary?')">Delete</button>
          </form>
        </div>
      </div>
      @empty
      <div class="subtitle" style="text-align:center; padding:2rem;">No itineraries yet.</div>
      @endforelse
      @if($itineraries->hasPages())
      <div style="margin-top:.75rem;">
        {{ $itineraries->links() }}
      </div>
      @endif
    </div>
  </main>
  <aside class="calendar-side">
    <h2>Quick Actions</h2>
    <div class="form-grid">
      <div class="col-12">
        <a class="btn btn-primary" href="{{ route('itineraries.create') }}">Add Itinerary</a>
      </div>
    </div>
  </aside>
</div>
@endsection
