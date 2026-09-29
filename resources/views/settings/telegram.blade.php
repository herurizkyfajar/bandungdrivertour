@extends('layouts.app', ['title' => 'Pengaturan Telegram'])

@section('content')
<style>
  .container { max-width: 100% !important; width: 100%; margin: 1rem auto 2rem; padding: 0 1rem 1rem; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
  .tg-template { width: 100%; min-height: 260px; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 14px; line-height: 1.6; }
  .tg-placeholder-list { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .5rem; }
  .tg-placeholder { background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; border-radius: 999px; padding: .15rem .6rem; font-size: 12px; font-family: ui-monospace, monospace; }
  .tg-preview { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: .9rem; white-space: pre-wrap; word-break: break-word; font-family: ui-monospace, monospace; font-size: 14px; }
  .tg-note { font-size: 13px; color: #64748b; margin-top: .35rem; }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="card content-card">
    <div class="card-body">
    <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Pengaturan Notifikasi Telegram</h1>
        <div class="subtitle">Booking baru (form publik & admin) otomatis dikirim ke Telegram. Webhook n8n tetap berjalan seperti biasa.</div>
      </div>
      <div><a class="btn" href="{{ route('dashboard') }}">Kembali</a></div>
    </div>

    <form method="POST" action="{{ route('settings.telegram.update') }}">
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="col-4">
          <div class="field">
            <label>Notifikasi Aktif</label>
            <select name="TELEGRAM_ENABLED">
              <option value="true" {{ old('TELEGRAM_ENABLED', $settings['TELEGRAM_ENABLED']) === 'true' ? 'selected' : '' }}>Aktif</option>
              <option value="false" {{ old('TELEGRAM_ENABLED', $settings['TELEGRAM_ENABLED']) === 'false' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Bot Token (dari @BotFather)</label>
            <input type="text" name="TELEGRAM_BOT_TOKEN" value="{{ old('TELEGRAM_BOT_TOKEN', $settings['TELEGRAM_BOT_TOKEN']) }}" placeholder="123456789:AA..." autocomplete="off">
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Chat ID (boleh lebih dari satu, pisah koma)</label>
            <input type="text" name="TELEGRAM_CHAT_ID" value="{{ old('TELEGRAM_CHAT_ID', $settings['TELEGRAM_CHAT_ID']) }}" placeholder="-5388360220,1449178699" required>
            <div class="tg-note">Contoh grup + personal: <code>-5388360220,1449178699</code></div>
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Parse Mode</label>
            <select name="TELEGRAM_PARSE_MODE">
              <option value="HTML" {{ old('TELEGRAM_PARSE_MODE', $settings['TELEGRAM_PARSE_MODE']) === 'HTML' ? 'selected' : '' }}>HTML (boleh pakai &lt;b&gt;, &lt;i&gt;, &lt;a&gt;)</option>
              <option value="None" {{ old('TELEGRAM_PARSE_MODE', $settings['TELEGRAM_PARSE_MODE']) === 'None' ? 'selected' : '' }}>None (teks polos)</option>
            </select>
          </div>
        </div>

        <div class="col-4">
          <div class="field">
            <label>Perintah Bot (balas chat, update biaya, download)</label>
            <select name="TELEGRAM_COMMANDS_ENABLED">
              <option value="true" {{ old('TELEGRAM_COMMANDS_ENABLED', $settings['TELEGRAM_COMMANDS_ENABLED']) === 'true' ? 'selected' : '' }}>Aktif</option>
              <option value="false" {{ old('TELEGRAM_COMMANDS_ENABLED', $settings['TELEGRAM_COMMANDS_ENABLED']) === 'false' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>

        <div class="col-12">
          <div class="field">
            <label>Format Pesan (template)</label>
            <textarea class="tg-template" name="TELEGRAM_TEMPLATE" required maxlength="4000">{{ old('TELEGRAM_TEMPLATE', $template) }}</textarea>
            <div class="tg-note">
              Tulis format sesuai keinginan Anda memakai placeholder di bawah. Pakai parse mode <b>HTML</b> jika ingin teks tebal/miring/link.
            </div>
            <div class="tg-placeholder-list">
              @foreach($placeholders as $p)
                <span class="tg-placeholder" title="{{ $p['desc'] }}">{{ $p['label'] }}</span>
              @endforeach
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="field">
            <label>Preview (data contoh)</label>
            <div class="tg-preview">{!! $preview !!}</div>
          </div>
        </div>
      </div>

      <div class="actions" style="margin-top:1rem; display:flex; gap:.5rem; flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary">Simpan Pengaturan Telegram</button>
      </div>
    </form>

    <form method="POST" action="{{ route('settings.telegram.test') }}" style="margin-top:.75rem;">
      @csrf
      <div class="subtitle" style="margin-bottom:.5rem;">Kirim pesan contoh memakai template di atas (setelah disimpan) untuk memastikan token & chat ID benar.</div>
      <button type="submit" class="btn">Kirim Pesan Test</button>
    </form>

    <hr style="margin:1.5rem 0; border:none; border-top:1px solid #e2e8f0;">

    <div style="margin-bottom:.75rem;">
      <h2 style="font-size:1rem; margin:0 0 .25rem;">Webhook (perintah dari Telegram)</h2>
      <div class="subtitle">Aktifkan agar bot menerima pesan dari chat Anda (update biaya, download PDF, info invoice).</div>
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
        <div style="color:#b45309;">Webhook belum aktif di Telegram.</div>
      @endif
    </div>

    <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
      <form method="POST" action="{{ route('settings.telegram.webhook') }}">
        @csrf
        <button type="submit" class="btn btn-primary">Aktifkan / Perbarui Webhook</button>
      </form>
      <form method="POST" action="{{ route('settings.telegram.webhook.delete') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn">Hapus Webhook</button>
      </form>
    </div>

    <div class="tg-note" style="margin-top:.75rem;">
      URL webhook: <code>{{ route('telegram.webhook') }}</code> — harus HTTPS (Telegram menolak HTTP).
    </div>

    <div class="tg-preview" style="margin-top:1rem;">
      <b>Cara pakai perintah:</b>
      • <code>INV-20260929-00178</code> → info invoice
      • <code>INV-20260929-00178/1500000</code> → update harga booking
      • <code>INV-20260929-00178/download</code> → kirim PDF invoice
      • Reply pesan notifikasi booking lalu kirim <code>info</code> / <code>1500000</code> / <code>download</code>
    </div>
    </div>
  </div>
</div>
@endsection
