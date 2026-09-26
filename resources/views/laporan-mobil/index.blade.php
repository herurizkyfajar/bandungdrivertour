@extends('layouts.app', ['title' => 'Laporan Mobil'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; overflow: hidden; }
  .period-btn { padding: .45rem .9rem; font-size: .85rem; background: #fff; color: #334155; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; font-weight: 600; }
  .period-btn:hover { background: #f8fafc; }
  .period-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
  .usage-bar { background: #e2e8f0; border-radius: 999px; height: 8px; overflow: hidden; min-width: 80px; }
  .usage-bar > span { display: block; height: 100%; background: #2563eb; border-radius: 999px; }
  @media (max-width: 1200px) {
    .dashboard-wrap { grid-template-columns: 1fr; }
    .dashboard-sidebar { position: static; max-height: none; overflow: hidden; min-width: 0; }
  }
  @media (max-width: 768px) {
    .content-card { overflow: hidden; }
  }
</style>
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <main class="content-card">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
      <div>
        <h1 style="margin:0;">Laporan Mobil</h1>
        <div class="subtitle">Jumlah penggunaan setiap mobil pada periode yang dipilih.</div>
      </div>
    </div>

    {{-- Filter Periode --}}
    <form method="GET" action="{{ route('laporan-mobil.index') }}" id="filterForm"
          style="display:flex; gap:.75rem; align-items:flex-end; margin-bottom:1rem; flex-wrap:wrap;">
      <div style="margin:0;">
        <label style="font-size:.85rem; font-weight:600; display:block; margin-bottom:.3rem;">Periode</label>
        <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
          <button type="submit" name="period" value="all" class="period-btn {{ $period === 'all' ? 'active' : '' }}">Semua</button>
          <button type="submit" name="period" value="day" class="period-btn {{ $period === 'day' ? 'active' : '' }}">Hari</button>
          <button type="submit" name="period" value="week" class="period-btn {{ $period === 'week' ? 'active' : '' }}">Minggu</button>
          <button type="submit" name="period" value="month" class="period-btn {{ $period === 'month' ? 'active' : '' }}">Bulan</button>
          <button type="submit" name="period" value="year" class="period-btn {{ $period === 'year' ? 'active' : '' }}">Tahun</button>
        </div>
      </div>

      <div class="field" style="margin:0;">
        <label for="start_date" style="font-size:.85rem; font-weight:600;">Dari Tanggal</label>
        <input id="start_date" name="start_date" type="date" value="{{ request('start_date', $rangeStart?->format('Y-m-d')) }}"
               {{ $period !== 'custom' ? 'disabled' : '' }}
               style="padding:.45rem .6rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem;">
      </div>
      <div class="field" style="margin:0;">
        <label for="end_date" style="font-size:.85rem; font-weight:600;">Sampai Tanggal</label>
        <input id="end_date" name="end_date" type="date" value="{{ request('end_date', $rangeEnd?->format('Y-m-d')) }}"
               {{ $period !== 'custom' ? 'disabled' : '' }}
               style="padding:.45rem .6rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem;">
      </div>
      <button type="submit" name="period" value="custom" class="period-btn {{ $period === 'custom' ? 'active' : '' }}">Rentang Kustom</button>

      <div class="field" style="margin:0;">
        <label for="status_filter" style="font-size:.85rem; font-weight:600;">Status</label>
        <select id="status_filter" name="status_filter" style="padding:.45rem .6rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem;">
          <option value="" {{ $statusFilter === '' ? 'selected' : '' }}>Selain Batal</option>
          <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
          @foreach($statuses as $value => $label)
            <option value="{{ $value }}" {{ $statusFilter === $value ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" name="period" value="{{ $period }}" style="padding:.45rem 1.2rem; height:38px; border-radius:8px; font-size:.9rem; background:#2563eb; color:#fff; border:none; cursor:pointer; font-weight:600;">Terapkan</button>

      @if(request()->hasAny(['period', 'start_date', 'end_date', 'status_filter']))
        <a href="{{ route('laporan-mobil.index') }}" class="btn" style="padding:.45rem 1.2rem; height:38px; border-radius:8px; font-size:.9rem; line-height:26px; border:1px solid var(--border); color:var(--text); text-decoration:none;">Reset</a>
      @endif
    </form>

    <div style="font-size:.85rem; color:#64748b; margin-bottom:1.25rem;">
      Rentang: <strong style="color:#0f172a;">{{ $rangeLabel }}</strong>
      @if($rangeDays)
        &middot; {{ $rangeDays }} hari
      @endif
      &middot; data booking belum dihapus, {{ $statusFilter === '' ? 'status dibatalkan dikecualikan' : ($statusFilter === 'all' ? 'semua status' : 'status: ' . ($statuses[$statusFilter] ?? $statusFilter)) }}
    </div>

    {{-- Summary --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
      <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:1rem;">
        <div style="font-size:.8rem; color:#0369a1; font-weight:600;">Total Penggunaan</div>
        <div style="font-size:1.3rem; font-weight:700; color:#0c4a6e;">{{ $totalBookings }} booking</div>
      </div>
      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:1rem;">
        <div style="font-size:.8rem; color:#15803d; font-weight:600;">Total Hari Pakai</div>
        <div style="font-size:1.3rem; font-weight:700; color:#14532d;">{{ $totalDays }} hari</div>
      </div>
      <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:1rem;">
        <div style="font-size:.8rem; color:#a16207; font-weight:600;">Mobil Digunakan</div>
        <div style="font-size:1.3rem; font-weight:700; color:#713f12;">{{ $vehiclesUsed }} / {{ $totalVehicles }} mobil</div>
      </div>
      <div style="background:#f5f3ff; border:1px solid #ddd6fe; border-radius:10px; padding:1rem;">
        <div style="font-size:.8rem; color:#6d28d9; font-weight:600;">Rata-rata / Mobil</div>
        <div style="font-size:1.3rem; font-weight:700; color:#4c1d95;">{{ $vehiclesUsed > 0 ? round($totalBookings / $vehiclesUsed, 1) : 0 }} booking</div>
      </div>
    </div>

    {{-- Chart --}}
    @if($chartData->isNotEmpty())
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:1rem; margin-bottom:1.5rem;">
      <h3 style="margin:0 0 .75rem; font-size:1rem; font-weight:700; color:#0f172a;">10 Mobil Paling Banyak Digunakan</h3>
      <div style="position:relative; height:280px;">
        <canvas id="vehicleUsageChart"></canvas>
      </div>
    </div>
    @endif

    {{-- Table --}}
    <div style="overflow-x:auto;">
      <table style="width:100%; border-collapse:collapse; font-size:.88rem;">
        <thead>
          <tr style="border-bottom:2px solid var(--border); text-align:left;">
            <th style="padding:.6rem .5rem; width:40px;">No</th>
            <th style="padding:.6rem .5rem;">Nama Mobil</th>
            <th style="padding:.6rem .5rem; text-align:right;">Jumlah Booking</th>
            <th style="padding:.6rem .5rem; text-align:right;">Total Hari Pakai</th>
            <th style="padding:.6rem .5rem;">Utilisasi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $index => $row)
            @php
              $vehicle = $row['vehicle'];
              $util = ($rangeDays && $rangeDays > 0) ? round($row['days'] / $rangeDays * 100, 1) : null;
              $utilBar = $util === null ? 0 : min(100, $util);
            @endphp
            <tr style="border-bottom:1px solid var(--border); {{ $row['count'] > 0 ? '' : 'opacity:.55;' }}">
              <td style="padding:.6rem .5rem;">{{ $index + 1 }}</td>
              <td style="padding:.6rem .5rem; font-weight:600;">
                {{ $row['name'] }}
                @if($vehicle->color)
                  <span style="color:#94a3b8; font-weight:400;">&middot; {{ $vehicle->color }}</span>
                @endif
              </td>
              <td style="padding:.6rem .5rem; text-align:right; font-weight:600;">{{ $row['count'] }}</td>
              <td style="padding:.6rem .5rem; text-align:right;">{{ $row['days'] }}</td>
              <td style="padding:.6rem .5rem; min-width:150px;">
                @if($util === null)
                  <span style="color:#94a3b8;">-</span>
                @else
                  <div style="display:flex; align-items:center; gap:.5rem;">
                    <div class="usage-bar" style="flex:1;"><span style="width:{{ $utilBar }}%;"></span></div>
                    <span style="font-size:.8rem; color:#475569; white-space:nowrap;">{{ rtrim(rtrim(number_format($util, 1, ',', '.'), '0'), ',') }}%</span>
                  </div>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="padding:2rem; text-align:center; color:#94a3b8;">Belum ada data mobil.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </main>
</div>

@isset($chartData)
@if($chartData->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
  const data = @json($chartData);
  const canvas = document.getElementById('vehicleUsageChart');
  if (!canvas || typeof Chart === 'undefined') return;

  new Chart(canvas, {
    type: 'bar',
    data: {
      labels: data.map(d => d.label),
      datasets: [{
        label: 'Jumlah Booking',
        data: data.map(d => d.count),
        backgroundColor: 'rgba(37, 99, 235, .75)',
        borderRadius: 6
      }, {
        label: 'Total Hari Pakai',
        data: data.map(d => d.days),
        backgroundColor: 'rgba(22, 163, 74, .75)',
        borderRadius: 6
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' }
      },
      scales: {
        x: { beginAtZero: true, ticks: { precision: 0 } },
        y: { ticks: { autoSkip: false } }
      }
    }
  });
})();
</script>
@endif
@endisset

<script>
(function () {
  const startInput = document.getElementById('start_date');
  const endInput = document.getElementById('end_date');
  if (!startInput || !endInput) return;

  document.querySelectorAll('button[name="period"]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const enabled = this.value === 'custom';
      startInput.disabled = !enabled;
      endInput.disabled = !enabled;
    });
  });
})();
</script>
@endsection
