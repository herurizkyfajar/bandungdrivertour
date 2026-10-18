@extends('layouts.app', ['title' => 'Pengaturan WhatsApp'])

@section('content')
<style>
  .container { max-width: 100% !important; width: 100%; margin: 1rem auto 2rem; padding: 0 1rem 1rem; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
  .wa-template { width: 100%; min-height: 260px; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 14px; line-height: 1.6; }
  .wa-placeholder-list { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .5rem; }
  .wa-placeholder { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 999px; padding: .15rem .6rem; font-size: 12px; font-family: ui-monospace, monospace; }
  .wa-preview { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: .9rem; white-space: pre-wrap; word-break: break-word; font-family: ui-monospace, monospace; font-size: 14px; }
  .wa-note { font-size: 13px; color: #64748b; margin-top: .35rem; }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="card content-card">
    <div class="card-body">
    <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Pengaturan Notifikasi WhatsApp (Fonnte)</h1>
        <div class="subtitle">Booking baru (form publik & admin) otomatis dikirim ke group WhatsApp lewat Fonnte, langsung tanpa n8n.</div>
      </div>
      <div><a class="btn" href="{{ route('dashboard') }}">Kembali</a></div>
    </div>

    <form method="POST" action="{{ route('settings.whatsapp.update') }}">
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="col-4">
          <div class="field">
            <label>Notifikasi Aktif</label>
            <select name="WHATSAPP_ENABLED">
              <option value="true" {{ old('WHATSAPP_ENABLED', $settings['WHATSAPP_ENABLED']) === 'true' ? 'selected' : '' }}>Aktif</option>
              <option value="false" {{ old('WHATSAPP_ENABLED', $settings['WHATSAPP_ENABLED']) === 'false' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Token Fonnte (menu Settings → Authorization di dashboard Fonnte)</label>
            <input type="text" name="FONNTE_TOKEN" value="{{ old('FONNTE_TOKEN', $settings['FONNTE_TOKEN']) }}" placeholder="AaBbCcDd12345678" autocomplete="off">
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Target Group WA (ID group Fonnte, boleh lebih dari satu, pisah koma)</label>
            <input type="text" name="FONNTE_TARGET" value="{{ old('FONNTE_TARGET', $settings['FONNTE_TARGET']) }}" placeholder="120363043xxxxxxxx" required>
            <div class="wa-note">Bukan nomor HP. Ambil ID group dari dashboard Fonnte (menu Group) atau dari response API <code>GET /group</code>.</div>
          </div>
        </div>

        <div class="col-12">
          <div class="field">
            <label>Format Pesan (template)</label>
            <textarea class="wa-template" name="WHATSAPP_TEMPLATE" required maxlength="4000">{{ old('WHATSAPP_TEMPLATE', $template) }}</textarea>
            <div class="wa-note">
              Tulis format sesuai keinginan Anda memakai placeholder di bawah. Pakai <code>*tebal*</code> untuk teks tebal ala WhatsApp.
            </div>
            <div class="wa-placeholder-list">
              @foreach($placeholders as $p)
                <span class="wa-placeholder" title="{{ $p['desc'] }}">{{ $p['label'] }}</span>
              @endforeach
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="field">
            <label>Preview (data contoh)</label>
            <div class="wa-preview">{!! $preview !!}</div>
          </div>
        </div>
      </div>

      <div class="actions" style="margin-top:1rem; display:flex; gap:.5rem; flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary">Simpan Pengaturan WhatsApp</button>
      </div>
    </form>

    <form method="POST" action="{{ route('settings.whatsapp.test') }}" style="margin-top:.75rem;">
      @csrf
      <div class="subtitle" style="margin-bottom:.5rem;">Kirim pesan contoh memakai template di atas (setelah disimpan) untuk memastikan token & target group benar.</div>
      <button type="submit" class="btn">Kirim Pesan Test</button>
    </form>

    <div class="wa-preview" style="margin-top:1rem;">
      <b>Cara mendapatkan Target Group:</b><br>
      • Buka dashboard Fonnte → menu <b>Group</b> → salin ID group (format <code>120363...</code>).<br>
      • Pastikan nomor HP device Fonnte sudah menjadi anggota group tersebut, lalu invite bot Fonnte ke group.
    </div>
    </div>
  </div>
</div>
@endsection
