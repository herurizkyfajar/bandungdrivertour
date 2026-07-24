@extends('layouts.app', ['title' => 'Laporan Keuangan'])

@section('content')
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <main class="content-card">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
      <div>
        <h1 style="margin:0;">Laporan Keuangan</h1>
        <div class="subtitle">Ringkasan pendapatan dari semua booking.</div>
      </div>
    </div>

    @if(session('success'))
        <div style="padding:.75rem 1rem; border-radius:8px; background:#d1fae5; color:#065f46; margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('laporan-keuangan.index') }}" style="display:flex; gap:.75rem; align-items:flex-end; margin-bottom:1.5rem; flex-wrap:wrap;">
      <div class="field" style="margin:0;">
        <label for="filter_pendapatan" style="font-size:.85rem; font-weight:600;">Pendapatan</label>
        <select id="filter_pendapatan" name="filter_pendapatan" style="padding:.45rem .6rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem;">
          <option value="">Semua</option>
          <option value="kosong" {{ request('filter_pendapatan') === 'kosong' ? 'selected' : '' }}>Belum Diisi</option>
        </select>
      </div>
      <div class="field" style="margin:0;">
        <label for="filter_status" style="font-size:.85rem; font-weight:600;">Status</label>
        <select id="filter_status" name="filter_status" style="padding:.45rem .6rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem;">
          <option value="">Semua</option>
          @foreach(\App\Models\Booking::KANBAN_PHASES as $phase => $def)
            @foreach($def['statuses'] as $s)
              <option value="{{ $s }}" {{ request('filter_status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary" style="height:38px;">Filter</button>
      @if(request()->hasAny(['filter_pendapatan', 'filter_status']))
        <a href="{{ route('laporan-keuangan.index') }}" class="btn" style="height:38px;">Reset</a>
      @endif
    </form>

    {{-- Summary Cards --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
      <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:1rem;">
        <div style="font-size:.8rem; color:#0369a1; font-weight:600;">Pendapatan Kotor</div>
        <div style="font-size:1.3rem; font-weight:700; color:#0c4a6e;">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
      </div>
      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:1rem;">
        <div style="font-size:.8rem; color:#15803d; font-weight:600;">Total Pendapatan</div>
        <div style="font-size:1.3rem; font-weight:700; color:#14532d;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
      </div>
      <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:1rem;">
        <div style="font-size:.8rem; color:#a16207; font-weight:600;">Belum Diisi</div>
        <div style="font-size:1.3rem; font-weight:700; color:#713f12;">{{ $belumDiisi }} booking</div>
      </div>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;">
      <table style="width:100%; border-collapse:collapse; font-size:.88rem;">
        <thead>
          <tr style="border-bottom:2px solid var(--border); text-align:left;">
            <th style="padding:.6rem .5rem;">No. Invoice</th>
            <th style="padding:.6rem .5rem;">Status</th>
            <th style="padding:.6rem .5rem;">Tanggal Booking</th>
            <th style="padding:.6rem .5rem; text-align:right;">Biaya</th>
            <th style="padding:.6rem .5rem; text-align:right;">Pendapatan</th>
            <th style="padding:.6rem .5rem; text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bookings as $b)
          <tr style="border-bottom:1px solid var(--border);">
            <td style="padding:.6rem .5rem; font-weight:600;">{{ $b->invoice->invoice_number ?? '-' }}</td>
            <td style="padding:.6rem .5rem;">
              <span style="display:inline-block; padding:.15rem .5rem; border-radius:6px; font-size:.78rem;
                @if(in_array($b->status, ['baru_masuk'])) background:#e0e7ff; color:#3730a3;
                @elseif(in_array($b->status, ['konfirmasi','dijadwalkan'])) background:#fef3c7; color:#92400e;
                @elseif(in_array($b->status, ['cancelled','cancel','batal'])) background:#fee2e2; color:#991b1b;
                @elseif(in_array($b->status, ['selesai_pelayanan','selesai_administrasi_fee'])) background:#d1fae5; color:#065f46;
                @else background:#f1f5f9; color:#475569;
                @endif
              ">{{ $b->statusLabel() }}</span>
            </td>
            <td style="padding:.6rem .5rem;">{{ $b->booking_date ? $b->booking_date->format('d M Y') : '-' }}</td>
            <td style="padding:.6rem .5rem; text-align:right;">{{ $b->price ? 'Rp ' . number_format($b->price, 0, ',', '.') : '-' }}</td>
            <td style="padding:.6rem .5rem; text-align:right; font-weight:600; color:{{ $b->pendapatan ? '#15803d' : '#94a3b8' }};">
              {{ $b->pendapatan ? 'Rp ' . number_format($b->pendapatan, 0, ',', '.') : '-' }}
            </td>
            <td style="padding:.6rem .5rem; text-align:center;">
              <button type="button" class="btn-edit-pendapatan" data-id="{{ $b->id }}" data-value="{{ $b->pendapatan ?? '' }}" style="padding:.3rem .6rem; font-size:.8rem; background:#3b82f6; color:#fff; border:none; border-radius:6px; cursor:pointer;">Edit</button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" style="padding:2rem; text-align:center; color:#94a3b8;">Tidak ada data ditemukan.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:1.5rem; text-align:center;">
      {{ $bookings->links() }}
    </div>
  </main>
</div>

{{-- Modal Edit Pendapatan --}}
<div id="editPendapatanModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.4); align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:1.5rem; width:90%; max-width:400px; box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <h3 style="margin:0 0 1rem; font-size:1.1rem;">Edit Pendapatan</h3>
    <form id="editPendapatanForm" method="POST" style="display:flex; flex-direction:column; gap:1rem;">
      @csrf
      @method('PUT')
      <div class="field" style="margin:0;">
        <label for="pendapatan_input" style="font-size:.85rem; font-weight:600;">Pendapatan (IDR)</label>
        <input id="pendapatan_input" type="text" inputmode="numeric" name="pendapatan" placeholder="Masukkan nominal" required style="width:100%; padding:.5rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem;">
      </div>
      <div style="display:flex; gap:.5rem; justify-content:flex-end;">
        <button type="button" id="closeModalBtn" class="btn" style="padding:.45rem 1rem;">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding:.45rem 1rem;">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  var modal = document.getElementById('editPendapatanModal');
  var form = document.getElementById('editPendapatanForm');
  var input = document.getElementById('pendapatan_input');
  var closeBtn = document.getElementById('closeModalBtn');

  document.querySelectorAll('.btn-edit-pendapatan').forEach(function(btn){
    btn.addEventListener('click', function(){
      var id = this.dataset.id;
      var val = this.dataset.value;
      form.action = '/bookings/' + id + '/pendapatan';
      input.value = val ? new Intl.NumberFormat('id-ID').format(val) : '';
      modal.style.display = 'flex';
    });
  });

  closeBtn.addEventListener('click', function(){ modal.style.display = 'none'; });
  modal.addEventListener('click', function(e){ if(e.target === modal) modal.style.display = 'none'; });

  input.addEventListener('input', function(){
    var v = this.value.replace(/\D/g,'');
    this.value = v ? new Intl.NumberFormat('id-ID').format(v) : '';
  });

  form.addEventListener('submit', function(){
    input.value = input.value.replace(/\D/g,'');
  });
})();
</script>
@endsection
