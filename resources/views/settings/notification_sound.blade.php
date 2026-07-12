@extends('layouts.app', ['title' => 'Pengaturan Suara Notifikasi'])

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
    <div style="margin-bottom:1rem; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
      <div>
        <h1>Pengaturan Suara Notifikasi</h1>
        <div class="subtitle">Atur suara, volume, dan pengulangan notifikasi booking baru.</div>
      </div>
      <div><a class="btn" href="{{ route('dashboard') }}">Kembali</a></div>
    </div>

    <form method="POST" action="{{ route('settings.notification-sound.update') }}" id="notification-sound-form">
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="col-4">
          <div class="field">
            <label>NOTIFY_SOUND_ENABLED</label>
            <select name="NOTIFY_SOUND_ENABLED">
              <option value="true" {{ old('NOTIFY_SOUND_ENABLED', (string) $settings['NOTIFY_SOUND_ENABLED']) === 'true' || old('NOTIFY_SOUND_ENABLED', (string) $settings['NOTIFY_SOUND_ENABLED']) === '1' ? 'selected' : '' }}>Aktif</option>
              <option value="false" {{ old('NOTIFY_SOUND_ENABLED', (string) $settings['NOTIFY_SOUND_ENABLED']) === 'false' || old('NOTIFY_SOUND_ENABLED', (string) $settings['NOTIFY_SOUND_ENABLED']) === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="col-4">
          <div class="field">
            <label>NOTIFY_SOUND_TYPE</label>
            <select name="NOTIFY_SOUND_TYPE">
              <option value="beep" {{ old('NOTIFY_SOUND_TYPE', $settings['NOTIFY_SOUND_TYPE']) === 'beep' ? 'selected' : '' }}>Beep</option>
              <option value="chime" {{ old('NOTIFY_SOUND_TYPE', $settings['NOTIFY_SOUND_TYPE']) === 'chime' ? 'selected' : '' }}>Chime</option>
              <option value="bell" {{ old('NOTIFY_SOUND_TYPE', $settings['NOTIFY_SOUND_TYPE']) === 'bell' ? 'selected' : '' }}>Bell</option>
              <option value="soft" {{ old('NOTIFY_SOUND_TYPE', $settings['NOTIFY_SOUND_TYPE']) === 'soft' ? 'selected' : '' }}>Soft</option>
            </select>
          </div>
        </div>
        <div class="col-12">
          <div class="field">
            <label style="display:flex; align-items:center; justify-content:space-between; gap:.75rem;">
              <span>NOTIFY_SOUND_VOLUME</span>
              <span class="subtitle" id="volume-value-label">{{ old('NOTIFY_SOUND_VOLUME', $settings['NOTIFY_SOUND_VOLUME']) }}%</span>
            </label>
            <div class="volume-control" id="volume-control">
              <span class="volume-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M11 5 6 9H3v6h3l5 4V5Z" stroke-width="2" stroke-linejoin="round"/>
                  <path d="M15 9a4 4 0 0 1 0 6" stroke-width="2" stroke-linecap="round"/>
                  <path d="M17.5 6.5a8 8 0 0 1 0 11" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </span>
              <input
                type="range"
                name="NOTIFY_SOUND_VOLUME"
                min="1"
                max="100"
                value="{{ old('NOTIFY_SOUND_VOLUME', $settings['NOTIFY_SOUND_VOLUME']) }}"
                required
                id="notify-sound-volume"
              >
              <span class="volume-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M14 5h7v7" stroke-width="2" stroke-linecap="round"/>
                  <path d="m21 5-7 7" stroke-width="2" stroke-linecap="round"/>
                  <path d="M5 12h6" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </span>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label>NOTIFY_SOUND_REPEAT</label>
            <input type="number" name="NOTIFY_SOUND_REPEAT" min="1" max="5" value="{{ old('NOTIFY_SOUND_REPEAT', $settings['NOTIFY_SOUND_REPEAT']) }}" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label>NOTIFY_SOUND_INTERVAL_MS</label>
            <input type="number" name="NOTIFY_SOUND_INTERVAL_MS" min="50" max="1000" value="{{ old('NOTIFY_SOUND_INTERVAL_MS', $settings['NOTIFY_SOUND_INTERVAL_MS']) }}" required>
          </div>
        </div>
      </div>

      <div class="subtitle" style="margin-top:.75rem;">
        Gunakan `beep` untuk suara pendek, `chime` untuk lebih lembut, `bell` untuk lebih tegas, dan `soft` untuk lebih halus.
      </div>

      <div class="actions" style="margin-top:1rem; justify-content:space-between; flex-wrap:wrap;">
        <button type="button" class="btn" id="test-notify-sound">Coba Suara</button>
        <button type="submit" class="btn btn-primary">Simpan Pengaturan Suara</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function () {
    const form = document.getElementById('notification-sound-form');
    const testButton = document.getElementById('test-notify-sound');
    const volumeSlider = document.getElementById('notify-sound-volume');
    const volumeLabel = document.getElementById('volume-value-label');
    const volumeControl = document.getElementById('volume-control');
    if (!form || !testButton) return;

    function paintVolume() {
      if (!volumeSlider || !volumeControl || !volumeLabel) return;
      const value = Number(volumeSlider.value || 80);
      const percentage = Math.max(1, Math.min(100, value));
      volumeLabel.textContent = percentage + '%';
      const fill = `linear-gradient(90deg, #3b82f6 0%, #60a5fa ${percentage}%, #cbd5e1 ${percentage}%, #cbd5e1 100%)`;
      volumeSlider.style.background = fill;
      volumeControl.style.setProperty('--volume-fill', fill);
    }

    async function unlockAudio() {
      try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return null;
        const audioCtx = window.__bdtNotificationSoundTester || new Ctx();
        window.__bdtNotificationSoundTester = audioCtx;
        if (audioCtx.state === 'suspended') {
          await audioCtx.resume();
        }
        return audioCtx;
      } catch (_) {
        return null;
      }
    }

    function playTestTone() {
      const volume = Number(form.NOTIFY_SOUND_VOLUME.value || 80) / 100;
      const type = form.NOTIFY_SOUND_TYPE.value || 'beep';
      const repeat = Number(form.NOTIFY_SOUND_REPEAT.value || 3);
      const intervalMs = Number(form.NOTIFY_SOUND_INTERVAL_MS.value || 140);
      const presets = {
        beep: { wave: 'sine', base: 980, peak: 1320 },
        chime: { wave: 'triangle', base: 740, peak: 1020 },
        bell: { wave: 'square', base: 620, peak: 860 },
        soft: { wave: 'sine', base: 520, peak: 760 },
      };
      const preset = presets[type] || presets.beep;

      unlockAudio().then((audioCtx) => {
        if (!audioCtx) return;
        const start = audioCtx.currentTime + 0.05;
        const toneDuration = 0.18;
        const gap = Math.max(0.05, intervalMs / 1000);

        for (let i = 0; i < repeat; i += 1) {
          const now = start + (i * (toneDuration + gap));
          const osc = audioCtx.createOscillator();
          const gain = audioCtx.createGain();
          osc.type = preset.wave;
          osc.frequency.setValueAtTime(preset.base, now);
          osc.frequency.exponentialRampToValueAtTime(preset.peak, now + 0.08);
          gain.gain.setValueAtTime(0.0001, now);
          gain.gain.exponentialRampToValueAtTime(Math.max(0.08, volume), now + 0.02);
          gain.gain.exponentialRampToValueAtTime(0.0001, now + toneDuration);
          osc.connect(gain);
          gain.connect(audioCtx.destination);
          osc.start(now);
          osc.stop(now + toneDuration + 0.03);
        }
      });
    }

    testButton.addEventListener('click', playTestTone);
    volumeSlider?.addEventListener('input', paintVolume);
    paintVolume();
    document.addEventListener('pointerdown', unlockAudio, { once: true });
  document.addEventListener('keydown', unlockAudio, { once: true });
  })();
</script>
<style>
  .volume-control {
    display: flex;
    align-items: center;
    gap: .75rem;
    background: #111827;
    border-radius: 14px;
    padding: .9rem 1rem;
    border: 1px solid #1f2937;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
  }
  .volume-control input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 5px;
    border-radius: 999px;
    outline: none;
    background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 80%, #cbd5e1 80%, #cbd5e1 100%);
  }
  .volume-control input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 999px;
    background: #60a5fa;
    border: 2px solid #e5e7eb;
    box-shadow: 0 0 0 4px rgba(96,165,250,.18);
    cursor: pointer;
  }
  .volume-control input[type="range"]::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 999px;
    background: #60a5fa;
    border: 2px solid #e5e7eb;
    box-shadow: 0 0 0 4px rgba(96,165,250,.18);
    cursor: pointer;
  }
  .volume-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    color: #f8fafc;
    flex: 0 0 auto;
  }
  .volume-icon svg {
    width: 22px;
    height: 22px;
  }
  @media (max-width: 640px) {
    .volume-control { padding: .8rem .85rem; gap: .6rem; }
  }
</style>
</div>
</div>
@endsection
