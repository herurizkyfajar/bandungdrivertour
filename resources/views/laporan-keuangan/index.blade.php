@extends('layouts.app', ['title' => 'Laporan Keuangan'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; overflow: hidden; }
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
        <h1 style="margin:0;">Laporan Keuangan</h1>
        <div class="subtitle">Ringkasan pendapatan dari semua booking.</div>
      </div>
      <button type="button" id="btnAturPajak" style="padding:.45rem 1rem; font-size:.88rem; background:#f59e0b; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600;">Atur Pajak ({{ (int) $pajakRate }}%)</button>
    </div>

    @if(session('success'))
        <div style="padding:.75rem 1rem; border-radius:8px; background:#d1fae5; color:#065f46; margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('laporan-keuangan.index') }}" style="display:flex; gap:.75rem; align-items:flex-end; margin-bottom:1.5rem; flex-wrap:wrap;">
      <div class="field" style="margin:0;">
        <label for="q" style="font-size:.85rem; font-weight:600;">Pencarian</label>
        <input id="q" name="q" type="text" value="{{ request('q') }}" placeholder="Nama, no. HP, atau invoice" style="padding:.45rem .6rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem; width:200px;">
      </div>
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
      <div class="field" style="margin:0;">
        <label for="filter_group" style="font-size:.85rem; font-weight:600;">Group</label>
        <select id="filter_group" name="filter_group" style="padding:.45rem .6rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem;">
          <option value="">Semua</option>
          @foreach($groups as $g)
            <option value="{{ $g->id }}" {{ request('filter_group') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary" style="padding:.45rem 1.2rem; height:38px; border-radius:8px; font-size:.9rem;">Filter</button>
      @if(request()->hasAny(['filter_pendapatan', 'filter_status', 'filter_group']))
        <a href="{{ route('laporan-keuangan.index') }}" class="btn" style="padding:.45rem 1.2rem; height:38px; border-radius:8px; font-size:.9rem;">Reset</a>
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
      <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:1rem;">
        <div style="font-size:.8rem; color:#b45309; font-weight:600;">Total Pajak ({{ (int) $pajakRate }}%)</div>
        <div style="font-size:1.3rem; font-weight:700; color:#92400e;">Rp {{ number_format($totalPajak, 0, ',', '.') }}</div>
      </div>
      <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:1rem;">
        <div style="font-size:.8rem; color:#a16207; font-weight:600;">Belum Diisi</div>
        <div style="font-size:1.3rem; font-weight:700; color:#713f12;">{{ $belumDiisi }} booking</div>
      </div>
    </div>

    {{-- Table --}}
    @php
      $currentSort = request('sort', 'created_at');
      $currentDir = request('dir', 'desc');
      $sortParams = ['q' => request('q'), 'filter_pendapatan' => request('filter_pendapatan'), 'filter_status' => request('filter_status'), 'filter_group' => request('filter_group')];
    @endphp
    <div style="overflow-x:auto;">
      <table style="width:100%; border-collapse:collapse; font-size:.88rem;">
        <thead>
          <tr style="border-bottom:2px solid var(--border); text-align:left;">
            @php
              $columns = [
                'invoice_number' => 'No. Invoice',
                'customer_name' => 'Customer',
                'status' => 'Status',
                'booking_date' => 'Tanggal Booking',
                'price' => 'Biaya',
                'pendapatan' => 'Pendapatan',
                'pajak' => 'Pajak',
              ];
            @endphp
            @foreach($columns as $key => $label)
              @if($key === 'pajak')
                <th style="padding:.6rem .5rem; vertical-align:top; line-height:1.3;">Pajak<br><span style="font-weight:400; font-size:.75rem; color:#94a3b8;">({{ (int) $pajakRate }}%)</span></th>
              @else
                @php
                  $isCurrentSort = $currentSort === $key;
                  $nextDir = ($isCurrentSort && $currentDir === 'asc') ? 'desc' : 'asc';
                  $arrow = $isCurrentSort ? ($currentDir === 'asc' ? ' ▲' : ' ▼') : '';
                @endphp
                <th style="padding:.6rem .5rem; vertical-align:top; {{ in_array($key, ['price','pendapatan']) ? 'text-align:right;' : '' }}">
                  <a href="{{ route('laporan-keuangan.index', array_merge($sortParams, ['sort' => $key, 'dir' => $nextDir])) }}" style="color:inherit; text-decoration:none; display:inline-flex; align-items:center; gap:.2rem; font-weight:600;">
                    {{ $label }}{{ $arrow }}
                  </a>
                </th>
              @endif
            @endforeach
            <th style="padding:.6rem .5rem; text-align:center; vertical-align:top;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bookings as $b)
          <tr style="border-bottom:1px solid var(--border);">
            <td style="padding:.6rem .5rem; font-weight:600;">
              @if($b->invoice)
                <button type="button" class="btn-invoice-detail"
                  data-invoice_number="{{ $b->invoice->invoice_number }}"
                  data-customer_name="{{ $b->customer_name }}"
                  data-contact_number="{{ $b->contact_number }}"
                  data-price="{{ number_format($b->price ?? 0, 0, ',', '.') }}"
                  data-down_payment="{{ number_format($b->invoice->down_payment ?? 0, 0, ',', '.') }}"
                  data-remaining="{{ number_format(($b->price ?? 0) - ($b->invoice->down_payment ?? 0), 0, ',', '.') }}"
                  data-status="{{ $b->statusLabel() }}"
                  data-created_at="{{ $b->invoice->created_at ? $b->invoice->created_at->format('d M Y H:i') : '-' }}"
                  style="color:#3b82f6; text-decoration:underline; cursor:pointer; background:none; border:none; font-weight:600; font-size:.88rem; padding:0;">
                  {{ $b->invoice->invoice_number }}
                </button>
              @else
                -
              @endif
            </td>
            <td style="padding:.6rem .5rem;">{{ $b->customer_name }}</td>
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
            <td style="padding:.6rem .5rem; text-align:right; font-weight:600; color:{{ ($b->pendapatan ?? 0) > 0 ? '#b45309' : '#94a3b8' }};">
              @php $pajak = ($b->pendapatan ?? 0) * $pajakRate / 100; @endphp
              {{ $pajak > 0 ? 'Rp ' . number_format($pajak, 0, ',', '.') : '-' }}
            </td>
            <td style="padding:.6rem .5rem; text-align:center;">
              <button type="button" class="btn-edit-pendapatan" data-id="{{ $b->id }}" data-value="{{ $b->pendapatan ?? '' }}" style="padding:.3rem .6rem; font-size:.8rem; background:#3b82f6; color:#fff; border:none; border-radius:6px; cursor:pointer;">Edit</button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" style="padding:2rem; text-align:center; color:#94a3b8;">Tidak ada data ditemukan.</td>
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

{{-- Modal Atur Pajak --}}
<div id="pajakModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.4); align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:1.5rem; width:90%; max-width:400px; box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
      <h3 style="margin:0; font-size:1.1rem;">Pengaturan Pajak</h3>
      <button id="closePajakModal" style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:#64748b;">&times;</button>
    </div>
    <form id="pajakForm" method="POST" action="{{ route('laporan-keuangan.pajak.update') }}" style="display:flex; flex-direction:column; gap:1rem;">
      @csrf
      @method('PUT')
      <div class="field" style="margin:0;">
        <label for="pajak_rate_input" style="font-size:.85rem; font-weight:600;">Pajak Pendapatan (%)</label>
        <input id="pajak_rate_input" type="number" name="pajak_rate" min="0" max="100" step="0.5" value="{{ $pajakRate }}" placeholder="Contoh: 11" required style="width:100%; padding:.5rem; border:1px solid var(--border); border-radius:8px; font-size:.9rem;">
      </div>
      <p style="margin:0; font-size:.8rem; color:#64748b;">Persentase pajak yang dikenakan pada seluruh pendapatan.</p>
      <div style="display:flex; gap:.5rem; justify-content:flex-end;">
        <button type="button" class="btn" id="closePajakBtn" style="padding:.45rem 1rem;">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding:.45rem 1rem;">Simpan</button>
      </div>
    </form>
  </div>
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

{{-- Modal Detail Invoice --}}
<div id="invoiceDetailModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.4); align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:12px; padding:1.5rem; width:90%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
      <h3 style="margin:0; font-size:1.1rem;">Detail Invoice</h3>
      <button id="closeInvoiceModal" style="background:none; border:none; font-size:1.3rem; cursor:pointer; color:#64748b;">&times;</button>
    </div>
    <div style="display:flex; flex-direction:column; gap:.75rem; font-size:.9rem;">
      <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:.5rem;">
        <span style="color:#64748b;">No. Invoice</span>
        <span style="font-weight:600;" id="inv_number">-</span>
      </div>
      <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:.5rem;">
        <span style="color:#64748b;">Customer</span>
        <span style="font-weight:600;" id="inv_customer">-</span>
      </div>
      <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:.5rem;">
        <span style="color:#64748b;">Kontak</span>
        <span style="font-weight:600;" id="inv_contact">-</span>
      </div>
      <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:.5rem;">
        <span style="color:#64748b;">Status</span>
        <span id="inv_status">-</span>
      </div>
      <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:.5rem;">
        <span style="color:#64748b;">Total Biaya</span>
        <span style="font-weight:600;" id="inv_price">-</span>
      </div>
      <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding-bottom:.5rem;">
        <span style="color:#64748b;">Down Payment</span>
        <span style="font-weight:600; color:#0369a1;" id="inv_dp">-</span>
      </div>
      <div style="display:flex; justify-content:space-between; padding-bottom:.5rem;">
        <span style="color:#64748b;">Sisa Bayar</span>
        <span style="font-weight:600; color:#b45309;" id="inv_remaining">-</span>
      </div>
      <div style="display:flex; justify-content:space-between;">
        <span style="color:#64748b;">Dibuat</span>
        <span style="font-size:.85rem; color:#94a3b8;" id="inv_created">-</span>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var pajakModal = document.getElementById('pajakModal');
  var btnAturPajak = document.getElementById('btnAturPajak');
  var closePajakModal = document.getElementById('closePajakModal');
  var closePajakBtn = document.getElementById('closePajakBtn');

  btnAturPajak.addEventListener('click', function(){ pajakModal.style.display = 'flex'; });
  closePajakModal.addEventListener('click', function(){ pajakModal.style.display = 'none'; });
  closePajakBtn.addEventListener('click', function(){ pajakModal.style.display = 'none'; });
  pajakModal.addEventListener('click', function(e){ if(e.target === pajakModal) pajakModal.style.display = 'none'; });
})();

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

(function(){
  var invModal = document.getElementById('invoiceDetailModal');
  var closeInv = document.getElementById('closeInvoiceModal');

  document.querySelectorAll('.btn-invoice-detail').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.getElementById('inv_number').textContent = this.dataset.invoice_number || '-';
      document.getElementById('inv_customer').textContent = this.dataset.customer_name || '-';
      document.getElementById('inv_contact').textContent = this.dataset.contact_number || '-';
      document.getElementById('inv_status').textContent = this.dataset.status || '-';
      document.getElementById('inv_price').textContent = 'Rp ' + (this.dataset.price || '0');
      document.getElementById('inv_dp').textContent = 'Rp ' + (this.dataset.down_payment || '0');
      document.getElementById('inv_remaining').textContent = 'Rp ' + (this.dataset.remaining || '0');
      document.getElementById('inv_created').textContent = this.dataset.created_at || '-';
      invModal.style.display = 'flex';
    });
  });

  closeInv.addEventListener('click', function(){ invModal.style.display = 'none'; });
  invModal.addEventListener('click', function(e){ if(e.target === invModal) invModal.style.display = 'none'; });
})();
</script>
@endsection
