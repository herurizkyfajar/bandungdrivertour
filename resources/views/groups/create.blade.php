@extends('layouts.app', ['title' => 'Add Group'])

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
  .logo-preview { width: 120px; height: 120px; border-radius: 12px; border: 2px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8fafc; cursor: pointer; }
  .logo-preview img { width: 100%; height: 100%; object-fit: cover; }
  .logo-preview span { color: #9ca3af; font-size: .85rem; text-align: center; }
</style>
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')
  <div class="content-card">
    <div class="actions" style="justify-content: space-between;">
      <h2>Add Group</h2>
      <a class="btn" href="{{ route('groups.index') }}">Back</a>
    </div>
    <form method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-grid">
        <div class="col-6">
          <div class="field">
            <label>Group Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
          </div>
          @error('name')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Logo (1:1 ratio)</label>
            <div class="logo-preview" id="logoPreview" onclick="document.getElementById('logoInput').click()">
              <span id="logoPlaceholder">Click to upload<br>1:1 ratio</span>
              <img id="logoImg" style="display:none;" alt="Logo preview">
            </div>
            <input type="file" id="logoInput" name="logo" accept="image/*" style="display:none;" onchange="previewLogo(this)">
          </div>
          @error('logo')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Website</label>
            <input type="text" name="website" value="{{ old('website') }}" placeholder="https://example.com">
          </div>
          @error('website')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-6">
          <div class="field">
            <label>Contact</label>
            <input type="text" name="contact" value="{{ old('contact') }}" placeholder="Phone / WhatsApp / Email">
          </div>
          @error('contact')<div style="color:red">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <div class="field">
            <label>Address</label>
            <textarea name="address" rows="3" style="width:100%; border:1px solid #cbd5e1; border-radius:12px; padding:.75rem .9rem;">{{ old('address') }}</textarea>
          </div>
          @error('address')<div style="color:red">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="actions">
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
<script>
function previewLogo(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('logoImg').src = e.target.result;
      document.getElementById('logoImg').style.display = 'block';
      document.getElementById('logoPlaceholder').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
@endsection
