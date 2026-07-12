@extends('layouts.app', ['title' => 'Tambah Layanan'])

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="content-card">
    <form method="POST" action="{{ route('services.store') }}">
      @csrf
      <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
        <div>
          <h1>Tambah Layanan</h1>
          <div class="subtitle">Tambahkan layanan untuk pilihan di booking form.</div>
        </div>
        <div><a class="btn" href="{{ route('services.index') }}">Kembali</a></div>
      </div>
      <div class="form-grid">
        <div class="col-12">
          <div class="field">
            <label for="name">Nama Layanan</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
          </div>
        </div>
        <div class="col-12">
          <div class="field">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" style="width:100%; border:1px solid #cbd5e1; border-radius:12px; padding:.75rem .9rem;">{{ old('description') }}</textarea>
          </div>
        </div>
        <div class="col-12">
          <div class="field">
            <label><input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}> Aktif</label>
          </div>
        </div>
      </div>
      <div class="actions">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection
