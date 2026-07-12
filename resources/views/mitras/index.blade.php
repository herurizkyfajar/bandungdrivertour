@extends('layouts.app', ['title' => 'Manage Mitras'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr) 320px; gap: 1.25rem; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; overflow: hidden; }
  .calendar-side { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; position: sticky; top: 76px; height: fit-content; }
  .chips { display: flex; flex-wrap: wrap; gap: .35rem; }
  .chip { display: inline-block; padding: .15rem .5rem; border-radius: 999px; font-size: .85rem; background: #eef2ff; color: #3730a3; border: 1px solid #e0e7ff; }
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
      <h2>Manage Mitras</h2>
      <a class="btn btn-primary" href="{{ route('mitras.create') }}">Add Mitra</a>
    </div>

    {{-- Desktop table --}}
    <div class="table-wrap desktop-table" style="margin-top:.75rem;">
      <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
        <thead>
          <tr>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Photo</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Full Name</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Email</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">WhatsApp</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Apps</th>
            <th style="text-align:center; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Bookings</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($mitras as $m)
          <tr>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              @if($m->photo_path)
                <img class="avatar" src="{{ asset('storage/'.$m->photo_path) }}" alt="{{ $m->full_name }}">
              @else
                -
              @endif
            </td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $m->full_name }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $m->email }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $m->whatsapp_contact }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              @php $apps = is_array($m->apps) ? $m->apps : (json_decode($m->apps ?? '[]', true) ?: []); @endphp
              <div class="chips">
                @foreach($apps as $a)
                  <span class="chip">{{ ucfirst($a) }}</span>
                @endforeach
                @if($m->other_app)
                  <span class="chip">{{ $m->other_app }}</span>
                @endif
              </div>
            </td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word; text-align:center;">
              <span style="display:inline-block; min-width:24px; padding:.2rem .5rem; border-radius:999px; font-size:.85rem; font-weight:600; background:#eef2ff; color:#3730a3;">{{ $m->bookings_count }}</span>
            </td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              <div style="display:flex; gap:.35rem; flex-wrap:wrap;">
                <a class="btn" href="{{ route('mitras.show', $m) }}">View</a>
                <a class="btn" href="{{ route('mitras.edit', $m) }}">Edit</a>
                <form method="POST" action="{{ route('mitras.destroy', $m) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn" type="submit" onclick="return confirm('Delete this mitra?')">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7"><div class="subtitle">No mitras yet.</div></td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div style="margin-top:.75rem;">
        {{ $mitras->links() }}
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards" style="margin-top:.75rem;">
      @forelse($mitras as $m)
      <div class="mobile-card">
        @if($m->photo_path)
          <img class="mobile-card-avatar" src="{{ asset('storage/'.$m->photo_path) }}" alt="{{ $m->full_name }}">
        @endif
        <div class="mobile-card-body">
          <div class="mobile-card-title">{{ $m->full_name }}</div>
          <div class="mobile-card-meta">
            {{ $m->email }} &middot; <strong>{{ $m->bookings_count }}</strong> bookings<br>
            @php $apps = is_array($m->apps) ? $m->apps : (json_decode($m->apps ?? '[]', true) ?: []); @endphp
            @if(count($apps))
              <span class="chips" style="margin-top:.25rem;">
                @foreach($apps as $a)
                  <span class="chip">{{ ucfirst($a) }}</span>
                @endforeach
                @if($m->other_app)
                  <span class="chip">{{ $m->other_app }}</span>
                @endif
              </span>
            @endif
          </div>
          <div class="mobile-card-actions">
            <a class="btn" href="{{ route('mitras.show', $m) }}">View</a>
            <a class="btn" href="{{ route('mitras.edit', $m) }}">Edit</a>
            <form method="POST" action="{{ route('mitras.destroy', $m) }}">
              @csrf
              @method('DELETE')
              <button class="btn" type="submit" onclick="return confirm('Delete this mitra?')">Delete</button>
            </form>
          </div>
        </div>
      </div>
      @empty
      <div class="subtitle" style="text-align:center; padding:2rem;">No mitras yet.</div>
      @endforelse
      @if($mitras->hasPages())
      <div style="margin-top:.75rem;">
        {{ $mitras->links() }}
      </div>
      @endif
    </div>
  </main>
  <aside class="calendar-side">
    <h2>Quick Actions</h2>
    <div class="form-grid">
      <div class="col-12">
        <a class="btn btn-primary" href="{{ route('mitras.create') }}">Add Mitra</a>
      </div>
    </div>
  </aside>
</div>
@endsection
