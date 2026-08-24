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
  .drag-handle { cursor: grab; color: var(--muted); font-size: 1.2rem; user-select: none; padding: 0 .25rem; }
  .drag-handle:active { cursor: grabbing; }
  .sortable-ghost { opacity: 0.4; background: #e0e7ff; }
  .sortable-chosen { box-shadow: 0 4px 12px rgba(37,99,235,0.25); }
  .sort-badge { display: inline-flex; align-items: center; gap: .3rem; background: #e0e7ff; color: #2563eb; font-size: .75rem; font-weight: 600; padding: .2rem .6rem; border-radius: 999px; }
  .btn-sort-toggle { display: inline-flex; align-items: center; gap: .4rem; }
  .sort-save-toast { position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%); background: #059669; color: #fff; padding: .6rem 1.2rem; border-radius: 10px; font-size: .85rem; font-weight: 600; z-index: 9999; opacity: 0; transition: opacity .3s; pointer-events: none; }
  .sort-save-toast.show { opacity: 1; }
</style>

<div class="sort-save-toast" id="sortToast">Urutan berhasil disimpan!</div>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')
  <main class="content-card">
    <div class="actions" style="justify-content: space-between;">
      <h2>Manage Vehicles</h2>
      <div style="display:flex; gap:.5rem; align-items:center;">
        <button class="btn btn-sort-toggle" id="btnToggleSort" onclick="toggleSortMode()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h12M3 18h6"/></svg>
          Urutkan
        </button>
        <a class="btn btn-primary" href="{{ route('vehicles.create') }}">Add Vehicle</a>
      </div>
    </div>

    {{-- Normal mode: paginated table --}}
    <div id="normalView">
      <div class="table-wrap desktop-table" style="margin-top:.75rem;">
        <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
          <thead>
            <tr>
              <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">#</th>
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
            @forelse($vehicles as $i => $v)
            <tr>
              <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--muted); font-size:.85rem;">{{ ($vehicles->currentPage()-1)*$vehicles->perPage()+$i+1 }}</td>
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
              <td colspan="8"><div class="subtitle">No vehicles yet.</div></td>
            </tr>
            @endforelse
          </tbody>
        </table>
        <div style="margin-top:.75rem;">
          {{ $vehicles->links() }}
        </div>
      </div>

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
    </div>

    {{-- Sort mode: draggable list --}}
    <div id="sortView" style="display:none; margin-top:.75rem;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem;">
        <span class="sort-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h12M3 18h6"/></svg>
          Mode Pengurutan Aktif
        </span>
        <div style="display:flex; gap:.5rem;">
          <button class="btn btn-primary" id="btnSaveSort" onclick="saveSortOrder()" disabled>Simpan Urutan</button>
          <button class="btn" onclick="toggleSortMode()">Batal</button>
        </div>
      </div>
      <div id="sortableList" style="display:flex; flex-direction:column; gap:.5rem;"></div>
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

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
let sortMode = false;
let sortableInstance = null;
let allVehicles = [];

function toggleSortMode() {
  sortMode = !sortMode;
  document.getElementById('normalView').style.display = sortMode ? 'none' : 'block';
  document.getElementById('sortView').style.display = sortMode ? 'block' : 'none';
  document.getElementById('btnToggleSort').innerHTML = sortMode
    ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg> Tutup'
    : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h12M3 18h6"/></svg> Urutkan';

  if (sortMode) {
    loadAllVehicles();
  } else if (sortableInstance) {
    sortableInstance.destroy();
    sortableInstance = null;
  }
}

function loadAllVehicles() {
  const list = document.getElementById('sortableList');
  list.innerHTML = '<div style="text-align:center; padding:1.5rem; color:var(--muted);">Memuat data...</div>';

  fetch('{{ route("vehicles.all") }}')
    .then(r => r.json())
    .then(vehicles => {
      allVehicles = vehicles;
      renderSortableList(vehicles);
    })
    .catch(() => {
      list.innerHTML = '<div style="text-align:center; padding:1.5rem; color:#dc2626;">Gagal memuat data.</div>';
    });
}

function renderSortableList(vehicles) {
  const list = document.getElementById('sortableList');
  if (!vehicles.length) {
    list.innerHTML = '<div style="text-align:center; padding:2rem; color:var(--muted);">Tidak ada kendaraan.</div>';
    return;
  }

  list.innerHTML = vehicles.map((v, i) => `
    <div class="mobile-card" data-id="${v.id}" style="margin-bottom:0; border:1px solid var(--border); border-radius:10px;">
      <span class="drag-handle" title="Drag untuk mengurutkan">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="5" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="19" r="1"/></svg>
      </span>
      <span style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:#f1f5f9; color:var(--muted); font-size:.8rem; font-weight:700; flex-shrink:0;">${i + 1}</span>
      ${v.photo_path ? `<img class="mobile-card-avatar" src="/storage/${v.photo_path}" alt="${v.plate_number}">` : ''}
      <div class="mobile-card-body">
        <div class="mobile-card-title">${v.plate_number}</div>
        <div class="mobile-card-meta">${v.make || ''} ${v.model || ''} &middot; ${v.color || ''} &middot; ${v.mitras && v.mitras.length ? v.mitras.map(m => m.full_name).join(', ') : '-'}</div>
      </div>
    </div>
  `).join('');

  if (sortableInstance) sortableInstance.destroy();
  sortableInstance = new Sortable(list, {
    handle: '.drag-handle',
    animation: 200,
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    onEnd: function() {
      document.getElementById('btnSaveSort').disabled = false;
      updateSortNumbers();
    }
  });

  document.getElementById('btnSaveSort').disabled = true;
}

function updateSortNumbers() {
  const items = document.querySelectorAll('#sortableList .mobile-card');
  items.forEach((item, i) => {
    const badge = item.querySelector('span:not(.drag-handle):not(img)');
    if (badge && !badge.classList.contains('mobile-card-avatar')) {
      badge.textContent = i + 1;
    }
  });
}

function saveSortOrder() {
  const btn = document.getElementById('btnSaveSort');
  const items = document.querySelectorAll('#sortableList .mobile-card');
  const ids = Array.from(items).map(item => parseInt(item.dataset.id));

  btn.disabled = true;
  btn.textContent = 'Menyimpan...';

  fetch('{{ route("vehicles.reorder") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ ids: ids })
  })
  .then(r => r.json())
  .then(data => {
    btn.textContent = 'Simpan Urutan';
    if (data.success) {
      const toast = document.getElementById('sortToast');
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 2500);
    }
  })
  .catch(() => {
    btn.disabled = false;
    btn.textContent = 'Simpan Urutan';
    alert('Gagal menyimpan urutan. Coba lagi.');
  });
}
</script>
@endsection
