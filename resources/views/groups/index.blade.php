@extends('layouts.app', ['title' => 'Manage Groups'])

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
  .mobile-card-title { font-weight: 700; color: #0f172a; font-size: .95rem; margin-bottom: .25rem; }
  .mobile-card-meta { font-size: .85rem; color: var(--muted); margin-bottom: .5rem; }
  .mobile-card-actions { display: flex; gap: .35rem; flex-wrap: wrap; }
</style>
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')
  <main class="content-card">
    <div class="actions" style="justify-content: space-between;">
      <h2>Manage Groups</h2>
      <a class="btn btn-primary" href="{{ route('groups.create') }}">Add Group</a>
    </div>

    {{-- Desktop table --}}
    <div class="table-wrap desktop-table" style="margin-top:.75rem;">
      <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
        <thead>
          <tr>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Logo</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Name</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Website</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Contact</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Address</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($groups as $g)
          <tr>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              @if($g->logo_path)
                <img src="{{ asset('storage/'.$g->logo_path) }}" alt="{{ $g->name }}" style="width:40px; height:40px; border-radius:8px; object-fit:cover; border:1px solid var(--border);">
              @else
                <div style="width:40px; height:40px; border-radius:8px; background:#f1f5f9; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:.75rem;">N/A</div>
              @endif
            </td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $g->name }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              @if($g->website)
                <a href="{{ $g->website }}" target="_blank" rel="noopener" style="color:var(--primary);">{{ $g->website }}</a>
              @else
                -
              @endif
            </td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $g->contact ?? '-' }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $g->address ?? '-' }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              <div style="display:flex; gap:.35rem; flex-wrap:wrap;">
                <a class="btn" href="{{ route('groups.edit', $g) }}">Edit</a>
                <form method="POST" action="{{ route('groups.destroy', $g) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn" type="submit" onclick="return confirm('Delete this group?')">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6"><div class="subtitle">No groups yet.</div></td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div style="margin-top:.75rem;">
        {{ $groups->links() }}
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards" style="margin-top:.75rem;">
      @forelse($groups as $g)
      <div class="mobile-card" style="display:flex; gap:.75rem; align-items:flex-start;">
        @if($g->logo_path)
          <img src="{{ asset('storage/'.$g->logo_path) }}" alt="{{ $g->name }}" style="width:48px; height:48px; border-radius:10px; object-fit:cover; border:1px solid var(--border); flex-shrink:0;">
        @endif
        <div style="flex:1; min-width:0;">
          <div class="mobile-card-title">{{ $g->name }}</div>
          <div class="mobile-card-meta">
          @if($g->website)<a href="{{ $g->website }}" target="_blank" rel="noopener" style="color:var(--primary);">{{ $g->website }}</a><br>@endif
          @if($g->contact){{ $g->contact }}<br>@endif
          @if($g->address){{ $g->address }}@endif
          @if(!$g->website && !$g->contact && !$g->address)No details @endif
        </div>
        <div class="mobile-card-actions">
          <a class="btn" href="{{ route('groups.edit', $g) }}">Edit</a>
          <form method="POST" action="{{ route('groups.destroy', $g) }}">
            @csrf
            @method('DELETE')
            <button class="btn" type="submit" onclick="return confirm('Delete this group?')">Delete</button>
          </form>
        </div>
        </div>
      </div>
      @empty
      <div class="subtitle" style="text-align:center; padding:2rem;">No groups yet.</div>
      @endforelse
      @if($groups->hasPages())
      <div style="margin-top:.75rem;">
        {{ $groups->links() }}
      </div>
      @endif
    </div>
  </main>
  <aside class="calendar-side">
    <h2>Quick Actions</h2>
    <div class="form-grid">
      <div class="col-12">
        <a class="btn btn-primary" href="{{ route('groups.create') }}">Add Group</a>
      </div>
    </div>
  </aside>
</div>
@endsection
