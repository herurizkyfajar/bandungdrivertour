@extends('layouts.app', ['title' => 'Edit Mitra'])

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="content-card">
    <div class="actions" style="justify-content: space-between;">
      <h2>Edit Mitra</h2>
      <a class="btn" href="{{ route('mitras.index') }}">Kembali</a>
    </div>
    <form method="POST" action="{{ route('mitras.update', $mitra) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="col-6">
          <div class="field">
            <label>Nama Lengkap</label>
            <input type="text" name="full_name" value="{{ old('full_name', $mitra->full_name) }}" required>
          </div>
          @error('full_name')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $mitra->email) }}" required>
          </div>
          @error('email')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Kontak WA</label>
            <input type="text" name="whatsapp_contact" value="{{ old('whatsapp_contact', $mitra->whatsapp_contact) }}" required>
          </div>
          @error('whatsapp_contact')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Foto</label>
            <input type="file" name="photo" accept="image/*">
          </div>
          @error('photo')<div style="color:red">{{ $message }}</div>@enderror
          @if($mitra->photo_path)
            <div style="margin-top:.5rem;">
              <img src="{{ asset('storage/'.$mitra->photo_path) }}" alt="{{ $mitra->full_name }}" style="width:80px;height:80px;border-radius:8px;object-fit:cover;">
            </div>
          @endif
        </div>
        <div class="col-12">
          <div class="field">
            <label>Pilihan Aplikasi Online</label>
            @php $selected = is_array($mitra->apps) ? $mitra->apps : (json_decode($mitra->apps ?? '[]', true) ?: []); @endphp
            <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:.5rem;">
              @foreach($appOptions as $opt)
                <label><input type="checkbox" name="apps[]" value="{{ $opt }}" {{ in_array($opt, (array)old('apps', $selected)) ? 'checked' : '' }}> {{ ucfirst($opt) }}</label>
              @endforeach
            </div>
          </div>
          @error('apps')<div style="color:red">{{ $message }}</div>@enderror
          @error('apps.*')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <div class="field">
            <label>Lainnya</label>
            <input type="text" name="other_app" value="{{ old('other_app', $mitra->other_app) }}" placeholder="Sebutkan aplikasi lain">
          </div>
          @error('other_app')<div style="color:red">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="actions">
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection
