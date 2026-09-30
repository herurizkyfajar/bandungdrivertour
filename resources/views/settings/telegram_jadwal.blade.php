@extends('layouts.app', ['title' => 'Tele Jadwal'])

@section('content')
<style>
  .container { max-width: 100% !important; width: 100%; margin: 1rem auto 2rem; padding: 0 1rem 1rem; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
  .tg-preview { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: .9rem; white-space: pre-wrap; word-break: break-word; font-family: ui-monospace, monospace; font-size: 14px; }
  .tg-note { font-size: 13px; color: #64748b; margin-top: .35rem; }
  .tg-status { display: inline-block; border-radius: 999px; padding: .15rem .7rem; font-size: 13px; font-weight: 600; }
  .tg-status.on { background: #dcfce7; color: #15803d; }
  .tg-status.off { background: #fee2e2; color: #b91c1c; }
  .tg-template { width: 100%; min-height: 180px; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 14px; line-height: 1.6; }
  .tg-placeholder-list { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .5rem; }
  .tg-placeholder { background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; border-radius: 999px; padding: .15rem .6rem; font-size: 12px; font-family: ui-monospace, monospace; }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="card content-card">
    <div class="card-body">
    <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
      <div>
        <h1>Pengaturan Tele Jadwal</h1>
        <div class="subtitle">Bot Telegram mengirim daftar booking bulan berjalan ke group setiap 1 minggu sekali.</div>
      </div>
      <div style="display:flex; gap:.5rem; align-items:center; flex-wrap:wrap;">
        <span class="tg-status {{ $active ? 'on' : 'off' }}">{{ $active ? 'Aktif' : 'Nonaktif' }}</span>
        <a class="btn" href="{{ route('dashboard') }}">Kembali</a>
      </div>
    </div>

    <form method="POST" action="{{ route('settings.tele-jadwal.update') }}">
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="col-4">
          <div class="field">
            <label>Jadwal Kirim Aktif</label>
            <select name="TELEGRAM_JADWAL_ENABLED">
              <option value="true" {{ old('TELEGRAM_JADWAL_ENABLED', $settings['TELEGRAM_JADWAL_ENABLED']) === 'true' ? 'selected' : '' }}>Aktif</option>
              <option value="false" {{ old('TELEGRAM_JADWAL_ENABLED', $settings['TELEGRAM_JADWAL_ENABLED']) === 'false' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Bot Token (bot baru, dari @BotFather)</label>
            <input type="text" name="TELEGRAM_JADWAL_BOT_TOKEN" value="{{ old('TELEGRAM_JADWAL_BOT_TOKEN', $settings['TELEGRAM_JADWAL_BOT_TOKEN']) }}" placeholder="123456789:AA..." autocomplete="off">
            <div class="tg-note">Opsional — jika dikosongkan{{ $fallbackToken ? ', memakai bot utama dari Telegram Settings' : '' }}.</div>
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Chat ID Group (boleh lebih dari satu, pisah koma)</label>
            <input type="text" name="TELEGRAM_JADWAL_CHAT_ID" value="{{ old('TELEGRAM_JADWAL_CHAT_ID', $settings['TELEGRAM_JADWAL_CHAT_ID']) }}" placeholder="-1004392013395">
            <div class="tg-note">Chat ID group tujuan (format -100...). Bot harus sudah di-add ke group.</div>
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Hari Kirim</label>
            <select name="TELEGRAM_JADWAL_DAY">
              @foreach($days as $value => $label)
                <option value="{{ $value }}" {{ old('TELEGRAM_JADWAL_DAY', $settings['TELEGRAM_JADWAL_DAY']) == $value ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Jam Kirim (WIB)</label>
            <input type="time" name="TELEGRAM_JADWAL_TIME" value="{{ old('TELEGRAM_JADWAL_TIME', $settings['TELEGRAM_JADWAL_TIME']) }}" required>
          </div>
        </div>

        <div class="col-12">
          <div class="field">
            <label>Format Pesan (template pesan utama)</label>
            <textarea class="tg-template" name="TELEGRAM_JADWAL_TEMPLATE" required maxlength="4000">{{ old('TELEGRAM_JADWAL_TEMPLATE', $template) }}</textarea>
            <div class="tg-note"><code>&#123;&#123;list&#125;&#125;</code> akan diisi daftar booking sesuai template per booking di bawah. Pakai <b>HTML</b> (&lt;b&gt;, &lt;i&gt;) untuk teks tebal/miring.</div>
            <div class="tg-placeholder-list">
              @foreach($placeholders as $p)
                <span class="tg-placeholder" title="{{ $p['desc'] }}">{{ $p['label'] }}</span>
              @endforeach
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="field">
            <label>Format Tiap Booking (template per data booking)</label>
            <textarea class="tg-template" name="TELEGRAM_JADWAL_ITEM_TEMPLATE" required maxlength="4000">{{ old('TELEGRAM_JADWAL_ITEM_TEMPLATE', $itemTemplate) }}</textarea>
            <div class="tg-note">Dipakai berulang untuk setiap booking. Baris kosong di awal agar antar booking berjarak.</div>
            <div class="tg-placeholder-list">
              @foreach($itemPlaceholders as $p)
                <span class="tg-placeholder" title="{{ $p['desc'] }}">{{ $p['label'] }}</span>
              @endforeach
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="field">
            <label>Preview pesan (daftar booking bulan ini, memakai template tersimpan)</label>
            <div class="tg-preview">{!! $preview !!}</div>
          </div>
        </div>
      </div>

      <div class="actions" style="margin-top:1rem; display:flex; gap:.5rem; flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary">Simpan Pengaturan Tele Jadwal</button>
      </div>
    </form>

    <form method="POST" action="{{ route('settings.tele-jadwal.test') }}" style="margin-top:.75rem;">
      @csrf
      <div class="subtitle" style="margin-bottom:.5rem;">Kirim daftar booking bulan ini sekarang ke group (setelah disimpan) untuk memastikan token & Chat ID benar.</div>
      <button type="submit" class="btn">Kirim Pesan Test</button>
    </form>

    <hr style="margin:1.5rem 0; border:none; border-top:1px solid #e2e8f0;">

    <div style="margin-bottom:.75rem;">
      <h2 style="font-size:1rem; margin:0 0 .25rem;">Perintah <code>/jadwal</code></h2>
      <div class="subtitle">Agar bot bisa merespon <code>/jadwal</code>, webhook bot ini harus aktif (butuh situs HTTPS).</div>
    </div>

    <div class="tg-preview" style="margin-bottom:.75rem;">
      @if($webhook['url'])
        <div>URL aktif: <code>{{ $webhook['url'] }}</code></div>
        <div>Menunggu diproses: {{ $webhook['pending_update_count'] ?? 0 }} update</div>
        @if($webhook['has_error'])
          <div style="color:#b91c1c;">Error terakhir: {{ $webhook['last_error_message'] }}</div>
        @else
          <div style="color:#15803d;">Status: OK</div>
        @endif
      @else
        <div style="color:#b45309;">Webhook bot ini belum aktif di Telegram.</div>
      @endif
    </div>

    <div style="display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:.75rem;">
      <form method="POST" action="{{ route('settings.tele-jadwal.webhook') }}">
        @csrf
        <button type="submit" class="btn btn-primary">Aktifkan / Perbarui Webhook</button>
      </form>
      <form method="POST" action="{{ route('settings.tele-jadwal.webhook.delete') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn">Hapus Webhook</button>
      </form>
    </div>

    <div class="tg-preview">
      <b>Cara pakai di Telegram:</b>
      • <code>/jadwal</code> → daftar booking bulan ini
      • <code>/jadwal 10</code> → daftar booking bulan Oktober
      • <code>/jadwal Oktober 2026</code> → daftar booking bulan tertentu
      • <code>/help</code> → daftar perintah
      <div class="tg-note" style="margin-top:.5rem;">Hanya di chat yang terdaftar di atas. Perintah mengikuti pengaturan "Perintah Bot" di Telegram Settings.</div>
    </div>

    <hr style="margin:1.5rem 0; border:none; border-top:1px solid #e2e8f0;">

    <div style="margin-bottom:.75rem;">
      <h2 style="font-size:1rem; margin:0 0 .25rem;">Penjadwalan Otomatis</h2>
      <div class="subtitle">Pengiriman mingguan dijalankan oleh scheduler Laravel. Pastikan cron/server sudah menjalankan:</div>
    </div>

    <div class="tg-preview" style="margin-bottom:.75rem;">* * * * * cd /home/USERNAME/DOMAIN && php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</div>

    <div class="tg-note">
      <b>Cara di cPanel:</b> menu <b>Cron Jobs</b> (Advanced) → <i>Common Settings: Every Minute</i> → isi Command di atas
      (ganti <code>USERNAME</code> &amp; <code>DOMAIN</code> dengan path hosting, mis. <code>/home/username/booking.bandungdrivertour.com</code>;
      sesuaikan <code>php</code> dengan path PHP hosting, cek lewat <code>which php</code> di Terminal cPanel).
      Redirect <code>&gt;&gt; /dev/null 2&gt;&amp;1</code> wajib agar tidak spam email cron.<br>
      Verifikasi: jalankan <code>php artisan schedule:list</code> — harus muncul <code>php artisan telegram:jadwal:send</code>.
      Perintah manual: <code>php artisan telegram:jadwal:send</code> — jadwal mengikuti hari &amp; jam di atas (WIB).
    </div>
    </div>
  </div>
</div>
@endsection
