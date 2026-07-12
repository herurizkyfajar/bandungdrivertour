@extends('layouts.app', ['title' => 'Edit Kendaraan'])

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
  .content-card { max-width: none; margin: 0; }
  .search-select { position: relative; }
  .search-input { width: 100%; padding: .6rem .75rem; border: 1px solid var(--border); border-radius: 10px; }
  .search-dropdown { position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 10; background: #fff; border: 1px solid var(--border); border-radius: 10px; max-height: 220px; overflow: auto; display: none; }
  .search-select.open .search-dropdown { display: block; }
  .search-item { padding: .5rem .75rem; cursor: pointer; display: flex; align-items: center; gap: .5rem; font-size: .9rem; }
  .search-item:hover { background: #f8fafc; }
  .search-item.selected { background: #eff6ff; }
  .search-item input[type="checkbox"] { pointer-events: none; }
  .search-empty { padding: .6rem .75rem; color: var(--muted); }
  .selected-tags { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .4rem; }
  .selected-tag { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .5rem; border-radius: 999px; font-size: .8rem; background: #eef2ff; color: #3730a3; border: 1px solid #e0e7ff; }
  .selected-tag button { background: none; border: none; color: #6366f1; cursor: pointer; font-size: .9rem; line-height: 1; padding: 0; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
</style>
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="content-card">
    <div class="actions" style="justify-content: space-between;">
      <h2>Edit Kendaraan</h2>
      <a class="btn" href="{{ route('vehicles.index') }}">Kembali</a>
    </div>
    <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="col-6">
          <div class="field">
            <label>Nomor Mobil</label>
            <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required>
          </div>
          @error('plate_number')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Merk Mobil</label>
            <input type="text" name="make" value="{{ old('make', $vehicle->make) }}">
          </div>
          @error('make')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Type Mobil</label>
            <input type="text" name="model" value="{{ old('model', $vehicle->model) }}">
          </div>
          @error('model')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Warna Mobil</label>
            <input type="text" name="color" value="{{ old('color', $vehicle->color) }}">
          </div>
          @error('color')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Foto Mobil</label>
            <input type="file" name="photo" accept="image/*">
            @if($vehicle->photo_path)
              <div style="margin-top:.5rem;">
                <img src="{{ asset('storage/'.$vehicle->photo_path) }}" alt="{{ $vehicle->plate_number }}" style="width:80px;height:80px;border-radius:8px;object-fit:cover;border:1px solid var(--border);">
              </div>
            @endif
          </div>
          @error('photo')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Pemilik (Mitra)</label>
            <div class="search-select" id="mitra_select">
              <input id="mitra_search" class="search-input" type="text" placeholder="Cari nama mitra" autocomplete="off">
              <div id="mitra_hidden_inputs">
                @foreach($vehicle->mitras as $m)
                  <input type="hidden" name="mitra_ids[]" value="{{ $m->id }}">
                @endforeach
              </div>
              <div class="selected-tags" id="mitra_tags"></div>
              <div class="search-dropdown" id="mitra_dropdown"></div>
            </div>
          </div>
          @error('mitra_ids')<div style="color:red">{{ $message }}</div>@enderror
          @error('mitra_ids.*')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Harga Mobil (IDR / hari)</label>
            <input id="price_per_day" type="text" inputmode="numeric" name="price_per_day" value="{{ old('price_per_day', number_format($vehicle->price_per_day ?? 0, 0, '.', '')) }}" placeholder="contoh: 1.000.000">
          </div>
          @error('price_per_day')<div style="color:red">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="actions">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
<script>
  const MITRAS = [
    @foreach($mitras as $m)
      { id: {{ $m->id }}, name: "{{ addslashes($m->full_name) }}", wa: "{{ addslashes($m->whatsapp_contact) }}" },
    @endforeach
  ];
  const selectWrap = document.getElementById('mitra_select');
  const searchInput = document.getElementById('mitra_search');
  const hiddenInputs = document.getElementById('mitra_hidden_inputs');
  const tagsContainer = document.getElementById('mitra_tags');
  const dropdown = document.getElementById('mitra_dropdown');
  let selectedMitraIds = @json($vehicle->mitras->pluck('id')->toArray());

  function syncHiddenInputs() {
    hiddenInputs.innerHTML = '';
    selectedMitraIds.forEach(function(id) {
      const inp = document.createElement('input');
      inp.type = 'hidden';
      inp.name = 'mitra_ids[]';
      inp.value = id;
      hiddenInputs.appendChild(inp);
    });
  }

  function renderTags() {
    tagsContainer.innerHTML = '';
    selectedMitraIds.forEach(function(id) {
      const m = MITRAS.find(x => x.id === id);
      if (!m) return;
      const tag = document.createElement('span');
      tag.className = 'selected-tag';
      tag.innerHTML = m.name + ' <button type="button" onclick="removeMitra(' + id + ')">&times;</button>';
      tagsContainer.appendChild(tag);
    });
  }

  function removeMitra(id) {
    selectedMitraIds = selectedMitraIds.filter(x => x !== id);
    syncHiddenInputs();
    renderTags();
    render(filter(searchInput.value));
  }
  window.removeMitra = removeMitra;

  function render(items) {
    dropdown.innerHTML = '';
    if (!items.length) {
      const empty = document.createElement('div');
      empty.className = 'search-empty';
      empty.textContent = 'Tidak ada hasil';
      dropdown.appendChild(empty);
      return;
    }
    items.forEach(function(item) {
      const el = document.createElement('div');
      el.className = 'search-item' + (selectedMitraIds.includes(item.id) ? ' selected' : '');
      const cb = document.createElement('input');
      cb.type = 'checkbox';
      cb.checked = selectedMitraIds.includes(item.id);
      el.appendChild(cb);
      const txt = document.createTextNode(item.name + (item.wa ? (' (' + item.wa + ')') : ''));
      el.appendChild(txt);
      el.addEventListener('click', function() {
        if (selectedMitraIds.includes(item.id)) {
          selectedMitraIds = selectedMitraIds.filter(x => x !== item.id);
        } else {
          selectedMitraIds.push(item.id);
        }
        syncHiddenInputs();
        renderTags();
        render(filter(searchInput.value));
      });
      dropdown.appendChild(el);
    });
  }

  function filter(q) {
    const s = (q || '').toLowerCase().trim();
    return MITRAS.filter(m => m.name.toLowerCase().includes(s) || (m.wa || '').toLowerCase().includes(s));
  }

  renderTags();
  syncHiddenInputs();

  searchInput.addEventListener('focus', function() {
    selectWrap.classList.add('open');
    render(filter(searchInput.value));
  });
  searchInput.addEventListener('input', function() {
    selectWrap.classList.add('open');
    render(filter(searchInput.value));
  });
  document.addEventListener('click', function(e) {
    if (!selectWrap.contains(e.target)) selectWrap.classList.remove('open');
  });
</script>
<script>
  (function(){
    var el = document.getElementById('price_per_day');
    if (!el) return;
    function fmt(v){
      var s = String(v || '').replace(/\D/g,'');
      if (!s) return '';
      var n = parseInt(s, 10);
      if (!isFinite(n)) return '';
      return new Intl.NumberFormat('id-ID').format(n);
    }
    function unfmt(v){ return String(v || '').replace(/\D/g,''); }
    el.value = fmt(el.value);
    el.addEventListener('input', function(){ this.value = fmt(this.value); });
    var f = el.closest('form');
    if (f) { f.addEventListener('submit', function(){ el.value = unfmt(el.value); }); }
  })();
</script>
@endsection
