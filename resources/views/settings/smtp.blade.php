@extends('layouts.app', ['title' => 'Pengaturan SMTP'])

@section('content')
<style>
  .container { max-width: 100% !important; width: 100%; margin: 1rem auto 2rem; padding: 0 1rem 1rem; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="card content-card">
    <div class="card-body">
    <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Pengaturan SMTP</h1>
        <div class="subtitle">Konfigurasi email notifikasi invoice untuk production.</div>
      </div>
      <div><a class="btn" href="{{ route('dashboard') }}">Kembali</a></div>
    </div>

    <form method="POST" action="{{ route('settings.smtp.update') }}">
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="col-4"><div class="field"><label>MAIL_MAILER</label><input type="text" name="MAIL_MAILER" value="{{ old('MAIL_MAILER', $settings['MAIL_MAILER']) }}" required></div></div>
        <div class="col-4"><div class="field"><label>MAIL_HOST</label><input type="text" name="MAIL_HOST" value="{{ old('MAIL_HOST', $settings['MAIL_HOST']) }}" required></div></div>
        <div class="col-4"><div class="field"><label>MAIL_PORT</label><input type="number" name="MAIL_PORT" value="{{ old('MAIL_PORT', $settings['MAIL_PORT']) }}" required></div></div>

        <div class="col-6"><div class="field"><label>MAIL_USERNAME</label><input type="text" name="MAIL_USERNAME" value="{{ old('MAIL_USERNAME', $settings['MAIL_USERNAME']) }}" required></div></div>
        <div class="col-6"><div class="field"><label>MAIL_PASSWORD</label><input type="text" name="MAIL_PASSWORD" value="{{ old('MAIL_PASSWORD', $settings['MAIL_PASSWORD']) }}" required></div></div>

        <div class="col-4"><div class="field"><label>MAIL_ENCRYPTION</label><input type="text" name="MAIL_ENCRYPTION" value="{{ old('MAIL_ENCRYPTION', $settings['MAIL_ENCRYPTION']) }}" placeholder="ssl atau tls"></div></div>
        <div class="col-4"><div class="field"><label>MAIL_FROM_ADDRESS</label><input type="email" name="MAIL_FROM_ADDRESS" value="{{ old('MAIL_FROM_ADDRESS', $settings['MAIL_FROM_ADDRESS']) }}" required></div></div>
        <div class="col-4"><div class="field"><label>MAIL_FROM_NAME</label><input type="text" name="MAIL_FROM_NAME" value="{{ old('MAIL_FROM_NAME', $settings['MAIL_FROM_NAME']) }}" required></div></div>

        <div class="col-12"><div class="field"><label>INVOICE_NOTIFY_EMAIL (tujuan notifikasi)</label><input type="email" name="INVOICE_NOTIFY_EMAIL" value="{{ old('INVOICE_NOTIFY_EMAIL', $settings['INVOICE_NOTIFY_EMAIL']) }}" required></div></div>

        <div class="col-4">
          <div class="field">
            <label>MAIL_VERIFY_PEER</label>
            <select name="MAIL_VERIFY_PEER">
              <option value="true" {{ old('MAIL_VERIFY_PEER', (string)$settings['MAIL_VERIFY_PEER']) === 'true' || old('MAIL_VERIFY_PEER', (string)$settings['MAIL_VERIFY_PEER']) === '1' ? 'selected' : '' }}>true</option>
              <option value="false" {{ old('MAIL_VERIFY_PEER', (string)$settings['MAIL_VERIFY_PEER']) === 'false' || old('MAIL_VERIFY_PEER', (string)$settings['MAIL_VERIFY_PEER']) === '0' ? 'selected' : '' }}>false</option>
            </select>
          </div>
        </div>
        <div class="col-4">
          <div class="field">
            <label>MAIL_VERIFY_PEER_NAME</label>
            <select name="MAIL_VERIFY_PEER_NAME">
              <option value="true" {{ old('MAIL_VERIFY_PEER_NAME', (string)$settings['MAIL_VERIFY_PEER_NAME']) === 'true' || old('MAIL_VERIFY_PEER_NAME', (string)$settings['MAIL_VERIFY_PEER_NAME']) === '1' ? 'selected' : '' }}>true</option>
              <option value="false" {{ old('MAIL_VERIFY_PEER_NAME', (string)$settings['MAIL_VERIFY_PEER_NAME']) === 'false' || old('MAIL_VERIFY_PEER_NAME', (string)$settings['MAIL_VERIFY_PEER_NAME']) === '0' ? 'selected' : '' }}>false</option>
            </select>
          </div>
        </div>
        <div class="col-4">
          <div class="field">
            <label>MAIL_ALLOW_SELF_SIGNED</label>
            <select name="MAIL_ALLOW_SELF_SIGNED">
              <option value="false" {{ old('MAIL_ALLOW_SELF_SIGNED', (string)$settings['MAIL_ALLOW_SELF_SIGNED']) === 'false' || old('MAIL_ALLOW_SELF_SIGNED', (string)$settings['MAIL_ALLOW_SELF_SIGNED']) === '0' ? 'selected' : '' }}>false</option>
              <option value="true" {{ old('MAIL_ALLOW_SELF_SIGNED', (string)$settings['MAIL_ALLOW_SELF_SIGNED']) === 'true' || old('MAIL_ALLOW_SELF_SIGNED', (string)$settings['MAIL_ALLOW_SELF_SIGNED']) === '1' ? 'selected' : '' }}>true</option>
            </select>
          </div>
        </div>
      </div>

      <div class="subtitle" style="margin-top:.75rem;">
        Jika sertifikat SSL SMTP belum valid, gunakan sementara: `MAIL_VERIFY_PEER=false`, `MAIL_VERIFY_PEER_NAME=false`, `MAIL_ALLOW_SELF_SIGNED=true`.
      </div>

      <div class="actions" style="margin-top:1rem;">
        <button type="submit" class="btn btn-primary">Simpan Pengaturan SMTP</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection
