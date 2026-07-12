<style>
  .icon-actions.icon-actions--labeled {
    display: grid;
    grid-template-columns: 1fr;
    gap: .45rem;
    align-items: stretch;
  }
  .icon-actions--labeled .icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: flex-start;
    gap: .45rem;
    width: 100%;
    padding: .6rem .75rem;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    background: #fff;
    text-decoration: none;
    color: inherit;
    transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
  }
  .icon-actions--labeled .icon-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(0,0,0,.06);
    border-color: #94a3b8;
  }
  .icon-actions--labeled .icon-btn .icon { width: 18px; height: 18px; }
  .icon-actions--labeled .icon-label { font-size: .85rem; font-weight: 600; color: #0f172a; }
  .icon-actions--labeled form { margin: 0; }
  .icon-actions--labeled .extra-actions { display: none; }
  .icon-actions--labeled.expanded .extra-actions {
    display: flex;
    flex-direction: column;
    gap: .45rem;
  }
  .icon-actions--labeled .toggle-btn {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    color: #0f172a;
  }
  /* Mobile-friendly stacked table */
  @media (max-width: 640px) {
    .table-wrap { overflow: visible; }
    .table-wrap > table { min-width: 100% !important; }
    .table { display: block; width: 100%; border-spacing: 0; }
    .table thead { display: none; }
    .table tbody { display: flex; flex-direction: column; gap: .75rem; }
    .table tr { display: grid; grid-template-columns: 1fr; background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: .75rem .9rem; }
    .table td { border: 0 !important; padding: .35rem 0 !important; display: flex; justify-content: space-between; gap: 10px; align-items: center; }
    .table td::before { content: attr(data-label); font-weight: 700; color: #6b7280; }
    .table td:last-child { padding-top: .6rem !important; flex-direction: column; align-items: stretch; }
    .icon-actions.icon-actions--labeled { width: 100%; max-width: none; margin: 0; }
    .icon-actions--labeled .icon-btn { width: 100%; justify-content: center; text-align: center; }
    .icon-actions--labeled .extra-actions { gap: .35rem; }
  }
  @media (max-width: 640px) {
    .icon-actions.icon-actions--labeled {
      gap: .4rem;
      width: 100%;
      max-width: 220px;
      margin: 0 auto;
    }
    .icon-actions--labeled .icon-btn {
      justify-content: center;
      text-align: center;
      padding: .52rem .6rem;
      min-width: 100%;
      box-sizing: border-box;
      font-size: .9rem;
      white-space: nowrap;
    }
    .icon-actions--labeled .icon-label {
      white-space: nowrap;
      font-size: .9rem;
    }
  }
</style>
<div class="table-wrap" style="margin-top:.75rem;">
  <table class="table">
    <thead>
      <tr>
        <th>Customer</th>
        <th>Date Range</th>
        <th>Pickup Time</th>
        <th>Vehicle</th>
        <th>Service</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($bookings as $b)
      <tr class="booking-row" data-invoice-id="{{ $b->invoice?->id ?? '' }}">
        <td data-label="Customer">{{ $b->customer_name }}<div class="subtitle">{{ $b->contact_number }}</div></td>
        <td data-label="Date Range">{{ \Illuminate\Support\Carbon::parse($b->booking_date)->format('Y-m-d') }} – {{ \Illuminate\Support\Carbon::parse($b->end_date)->format('Y-m-d') }}</td>
        <td data-label="Pickup Time">{{ \Illuminate\Support\Carbon::parse($b->pickup_time)->format('H:i') }}</td>
        <td data-label="Vehicle">{{ $b->vehicle?->make }} {{ $b->vehicle?->model }}</td>
        <td data-label="Service">{{ $b->service?->name ?? '-' }}</td>
        <td data-label="Status">
          @php
            $statusValue = $b->status ?? 'baru_masuk';
            $statusLabel = match($statusValue) {
              'baru_masuk' => 'Baru Masuk',
              'konfirmasi' => 'Konfirmasi',
              'dijadwalkan' => 'Dijadwalkan',
              'selesai_pelayanan' => 'Selesai Pelayanan',
              'selesai_administrasi_fee' => 'Selesai Administrasi Fee',
              'new' => 'Baru Masuk',
              'confirmed' => 'Konfirmasi',
              'completed' => 'Selesai Pelayanan',
              default => $statusValue,
            };
          @endphp
          {{ $statusLabel }}
        </td>
        <td data-label="Aksi">
          <div class="icon-actions icon-actions--labeled" id="actions-{{ $b->id }}">
            @if($b->invoice)
            <a class="icon-btn mark-invoice-viewed" data-invoice-id="{{ $b->invoice->id }}" href="{{ route('invoice.show', $b->invoice) }}" title="Lihat Invoice">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="2" stroke-linejoin="round"/>
                <path d="M14 2v6h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 13h6M9 17h6" stroke-width="2" stroke-linecap="round"/>
              </svg>
              <span class="icon-label">Lihat Invoice</span>
            </a>
            <button type="button" class="icon-btn toggle-btn" onclick="toggleActions('{{ $b->id }}')">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 5v14" stroke-width="2" stroke-linecap="round"/>
                <path d="M5 12h14" stroke-width="2" stroke-linecap="round"/>
              </svg>
              <span class="icon-label">Aksi Lainnya</span>
            </button>
            <div class="extra-actions">
              <a class="icon-btn mark-invoice-viewed" data-invoice-id="{{ $b->invoice->id }}" href="{{ route('invoice.show', $b->invoice) }}?download=1" title="Download PDF">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M12 3v12" stroke-width="2" stroke-linecap="round"/>
                  <path d="M7 12l5 5l5-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M6 20h12" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span class="icon-label">Download Invoice</span>
              </a>
              @if(auth()->user()?->role === 'super_admin' && !empty($b->invoice->manual_invoice_path))
              <a class="icon-btn" href="{{ asset('storage/' . $b->invoice->manual_invoice_path) }}" target="_blank" rel="noopener" title="Lihat Invoice Manual">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M12 3v12" stroke-width="2" stroke-linecap="round"/>
                  <path d="M7 12l5 5l5-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M6 20h12" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span class="icon-label">Invoice Manual</span>
              </a>
              @endif
              @php
                $raw = (string)($b->contact_number ?? '');
                $digits = preg_replace('/\D+/', '', $raw);
                $country = strtolower(trim((string)($b->country_of_origin ?? '')));
                $map = [
                  'indonesia' => '62',
                  'malaysia' => '60',
                  'singapore' => '65',
                  'thailand' => '66',
                  'vietnam' => '84',
                  'philippines' => '63',
                  'united states' => '1',
                  'usa' => '1',
                  'united kingdom' => '44',
                  'uk' => '44',
                  'australia' => '61',
                  'japan' => '81',
                  'south korea' => '82',
                  'korea' => '82',
                  'united arab emirates' => '971',
                  'uae' => '971',
                  'saudi arabia' => '966',
                  'india' => '91',
                ];
                if (str_starts_with(trim($raw), '+')) {
                  $wa = $digits;
                } elseif (str_starts_with($digits, '00')) {
                  $wa = substr($digits, 2);
                } elseif (str_starts_with($digits, '0')) {
                  $code = $map[$country] ?? null;
                  $wa = $code ? ($code . substr($digits, 1)) : $digits;
                } else {
                  $wa = $digits;
                }
                $msg = $b->invoice ? ("Halo ".$b->customer_name.". Invoice: ".$b->invoice->invoice_number." Total (IDR): ".number_format($b->invoice->amount,0,',','.').". Mohon konfirmasi.") : ("Halo ".$b->customer_name.". Mohon konfirmasi booking Anda.");
              @endphp
              @if(!empty($wa))
              <a class="icon-btn" href="https://wa.me/{{ $wa }}?text={{ urlencode($msg) }}" target="_blank" rel="noopener" title="Chat via WhatsApp">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M16.5 3A7.5 7.5 0 0 0 4 10.5c0 1.3.3 2.5.9 3.6L4 20l5.9-.9c1.1.6 2.3.9 3.6.9A7.5 7.5 0 0 0 16.5 3Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8 9.5c.5 1.5 2 3 3.5 3.5M12.5 13c.5-.3 1.2-1.1 1.5-1.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="icon-label">Chat WA</span>
              </a>
              @endif
              <a class="icon-btn" href="{{ route('spj.show', $b) }}" title="Surat Perintah Jalan">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="2" stroke-linejoin="round"/>
                  <path d="M9 13h6M9 17h6" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span class="icon-label">Lihat SPJ</span>
              </a>
              <a class="icon-btn" href="{{ route('spj.show', $b) }}?download=1" title="Download SPJ">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M12 3v12" stroke-width="2" stroke-linecap="round"/>
                  <path d="M7 12l5 5l5-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M6 20h12" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span class="icon-label">Download SPJ</span>
              </a>
              <a class="icon-btn" href="{{ route('bookings.edit', $b) }}" title="Edit">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M12 20h9" stroke-width="2" stroke-linecap="round"/>
                  <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="icon-label">Edit</span>
              </a>
              <form method="POST" action="{{ route('bookings.destroy', $b) }}">
                @csrf
                @method('DELETE')
                <button class="icon-btn" type="submit" title="Hapus" onclick="return confirm('Hapus booking ini?')">
                  <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 6h18" stroke-width="2" stroke-linecap="round"/>
                    <path d="M8 6V4h8v2" stroke-width="2" stroke-linecap="round"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 11v6M14 11v6" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                  <span class="icon-label">Hapus</span>
                </button>
              </form>
            </div>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="7"><div class="subtitle">Belum ada booking.</div></td></tr>
      @endforelse
    </tbody>
  </table>
  <div style="margin-top:.75rem;">
    {{ $bookings->links() }}
  </div>
</div>
<script>
  function toggleActions(id) {
    var el = document.getElementById('actions-' + id);
    if (!el) return;
    var expanded = el.classList.toggle('expanded');
    var label = el.querySelector('.toggle-btn .icon-label');
    if (label) { label.textContent = expanded ? 'Sembunyikan Aksi' : 'Aksi Lainnya'; }
  }
</script>
