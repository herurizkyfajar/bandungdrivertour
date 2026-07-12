@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<style>
  .container {
    max-width: 100% !important;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    margin-top: 1rem;
    padding: 0 1rem 1rem;
  }

  .dashboard-shell {
    display: grid;
    grid-template-columns: 250px minmax(0, 1fr) 320px;
    gap: 1.25rem;
    align-items: start;
    min-height: calc(100vh - 7rem);
  }

  .dashboard-sidebar,
  .dashboard-main,
  .dashboard-right {
    background: rgba(255,255,255,.9);
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 24px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
    backdrop-filter: blur(10px);
  }

  .dashboard-sidebar,
  .dashboard-right {
    padding: 1rem;
    position: sticky;
    top: 5.75rem;
    max-height: calc(100vh - 7.25rem);
    overflow-y: auto;
  }

  .dashboard-brand {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding-bottom: .95rem;
    border-bottom: 1px solid #e2e8f0;
  }

  .dashboard-brand img {
    width: 44px;
    height: 44px;
    object-fit: contain;
  }

  .dashboard-brand h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: -.02em;
  }

  .dashboard-brand .subtitle {
    font-size: .85rem;
  }

  .dashboard-menu {
    list-style: none;
    margin: 1rem 0 0;
    padding: 0;
    display: grid;
    gap: .45rem;
  }

  .dashboard-menu a,
  .dashboard-menu button {
    display: flex;
    align-items: center;
    gap: .7rem;
    width: 100%;
    padding: .76rem .9rem;
    border-radius: 14px;
    text-decoration: none;
    color: var(--text);
    border: 1px solid #e2e8f0;
    background: #fff;
    font-weight: 600;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
  }

  .dashboard-menu a:hover,
  .dashboard-menu button:hover {
    transform: translateY(-1px);
    border-color: #c7d2fe;
    box-shadow: 0 10px 18px rgba(15, 23, 42, .06);
    background: #f8fafc;
  }

  .dashboard-menu a.active {
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 12px 24px rgba(37, 99, 235, .25);
  }

  .dashboard-main {
    padding: 1.15rem;
    min-height: calc(100vh - 7rem);
    display: grid;
    gap: 1rem;
    align-content: start;
  }

  .dashboard-hero {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: flex-start;
    padding: 1rem 1.1rem;
    border-radius: 22px;
    background: linear-gradient(135deg, rgba(37,99,235,.08), rgba(99,102,241,.08));
    border: 1px solid rgba(191,219,254,.8);
  }

  .dashboard-hero h2 {
    margin: 0;
    font-size: 1.45rem;
    letter-spacing: -.03em;
  }

  .dashboard-hero .subtitle {
    margin-top: .3rem;
    max-width: 60ch;
  }

  .hero-actions {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
  }

  .dashboard-cards {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
    margin-top: 1rem;
  }

  .metric-card {
    border-radius: 20px;
    padding: 1rem;
    color: #fff;
    box-shadow: 0 16px 28px rgba(15, 23, 42, .14);
  }

  .metric-card h4 {
    margin: 0 0 .3rem;
    font-size: .98rem;
  }

  .metric-value {
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -.04em;
    line-height: 1.1;
  }

  .metric-note {
    margin-top: .2rem;
    font-size: .9rem;
    opacity: .88;
  }

  .summary-card {
    margin-top: 1rem;
    border-radius: 22px;
    border: 1px solid #e2e8f0;
    background: #fff;
    overflow: hidden;
  }

  .summary-card .summary-head {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
  }

  .summary-card .summary-head h3 {
    margin: 0;
    font-size: 1.05rem;
    letter-spacing: -.02em;
  }

  .summary-card .summary-body {
    padding: 1.1rem;
  }

  .quick-links {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .75rem;
  }

  .quick-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    padding: .9rem 1rem;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
    text-decoration: none;
    color: var(--text);
    font-weight: 700;
  }

  .quick-link:hover {
    border-color: #c7d2fe;
    box-shadow: 0 12px 20px rgba(15, 23, 42, .06);
  }

  .dashboard-right {
    display: grid;
    gap: 1rem;
  }

  .right-card {
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    background: #fff;
    overflow: hidden;
  }

  .right-card-header {
    padding: 1rem 1.05rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
  }

  .right-card-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: -.02em;
  }

  .schedule {
    display: grid;
    gap: .7rem;
    padding: 1rem;
  }

  .schedule-item {
    display: grid;
    grid-template-columns: 68px minmax(0, 1fr);
    gap: .75rem;
    padding: .85rem;
    border-radius: 18px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
  }

  .schedule-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 24px rgba(15, 23, 42, .08);
  }

  .schedule-time {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 66px;
    border-radius: 16px;
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    color: #fff;
    text-align: center;
    padding: .35rem;
  }

  .schedule-time strong {
    font-size: 1rem;
    line-height: 1.1;
  }

  .schedule-time span {
    font-size: .78rem;
    opacity: .9;
  }

  .schedule-title {
    font-weight: 800;
    color: #0f172a;
    margin-bottom: .2rem;
  }

  .schedule-meta {
    color: var(--muted);
    font-size: .9rem;
    line-height: 1.45;
  }

  .empty-state {
    padding: 1rem;
    text-align: center;
    color: var(--muted);
  }

  .modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 50;
    padding: 1rem;
    backdrop-filter: blur(4px);
  }

  .modal-card {
    width: 760px;
    max-width: 96vw;
    padding: 1.2rem;
    border-radius: 24px;
    background: rgba(255,255,255,.96);
    border: 1px solid rgba(226,232,240,.95);
    box-shadow: 0 30px 60px rgba(15, 23, 42, .25);
  }

  .modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: .9rem;
  }

  .modal-title {
    font-size: 1.35rem;
    font-weight: 800;
    letter-spacing: -.02em;
  }

  .modal-close {
    border: 1px solid #cbd5e1;
    background: #fff;
    border-radius: 12px;
    padding: .45rem .75rem;
    cursor: pointer;
    font-weight: 700;
  }

  .modal-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .75rem 1rem;
  }

  .modal-row {
    padding: .85rem .9rem;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
  }

  .modal-row .label {
    display: block;
    font-size: .8rem;
    color: var(--muted);
    margin-bottom: .25rem;
  }

  .modal-row .value {
    font-weight: 700;
    color: #0f172a;
    line-height: 1.45;
    word-break: break-word;
  }

  .modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: .5rem;
    margin-top: 1rem;
  }

  .wysiwyg-view {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    overflow: hidden;
    background: #fff;
    margin-top: .55rem;
  }

  .w-content {
    min-height: 150px;
    padding: .9rem 1rem;
    line-height: 1.6;
    font-size: .95rem;
  }

  @media (max-width: 1200px) {
    .dashboard-shell {
      grid-template-columns: 1fr;
    }

    .dashboard-sidebar,
    .dashboard-right {
      position: static;
      max-height: none;
      overflow: visible;
    }
  }

  @media (max-width: 768px) {
    .dashboard-hero,
    .modal-header {
      flex-direction: column;
    }

    .dashboard-cards,
    .quick-links,
    .modal-grid {
      grid-template-columns: 1fr;
    }

    .schedule-item {
      grid-template-columns: 56px minmax(0, 1fr);
    }
  }
</style>

<div class="dashboard-shell">
  @include('partials.admin-sidebar')

  <main class="dashboard-main">
    <div class="dashboard-hero">
      <div>
        <h2>Dashboard</h2>
        @if($role === 'super_admin')
          <div class="subtitle">Pantau booking, kendaraan, dan mitra dalam satu tampilan yang lebih bersih dan modern.</div>
        @else
          <div class="subtitle">Welcome back, {{ $user->name }}. Here's your booking summary.</div>
        @endif
      </div>
      <div class="hero-actions">
        @if($role === 'super_admin')
          <a class="btn btn-primary" href="{{ route('bookings.index') }}">Kelola Booking</a>
        @endif
        <a class="btn" href="{{ route('booking.create') }}">Tambah Booking</a>
      </div>
    </div>

    @if($role === 'super_admin')
    <div class="dashboard-cards">
      <div class="metric-card" style="background: linear-gradient(135deg, #6b21a8 0%, #8b5cf6 100%);">
        <h4>Total Booking</h4>
        <div class="metric-value">{{ $metrics['bookings'] ?? 0 }}</div>
        <div class="metric-note">Jumlah semua booking</div>
      </div>
      <div class="metric-card" style="background: linear-gradient(135deg, #0891b2 0%, #22d3ee 100%);">
        <h4>Total Kendaraan</h4>
        <div class="metric-value">{{ $metrics['vehicles'] ?? 0 }}</div>
        <div class="metric-note">Unit kendaraan terdaftar</div>
      </div>
      <div class="metric-card" style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);">
        <h4>Total Mitra</h4>
        <div class="metric-value">{{ $metrics['mitras'] ?? 0 }}</div>
        <div class="metric-note">Mitra aktif di sistem</div>
      </div>
    </div>

    <div class="summary-card">
      <div class="summary-head">
        <div>
          <h3>Ringkasan</h3>
          <div class="subtitle" style="margin-top:.25rem;">Akses cepat ke modul utama dan pantau jadwal di panel kanan.</div>
        </div>
      </div>
      <div class="summary-body">
        <div class="quick-links">
          <a class="quick-link" href="{{ route('bookings.index') }}">Kelola Booking <span>→</span></a>
          <a class="quick-link" href="{{ route('dashboard.calendar') }}">Buka Kalender <span>→</span></a>
          <a class="quick-link" href="{{ route('settings.smtp') }}">SMTP Setting <span>→</span></a>
          <a class="quick-link" href="{{ route('email-logs.index') }}">Email Logs <span>→</span></a>
        </div>
      </div>
    </div>
    @else
    <div class="dashboard-cards">
      <div class="metric-card" style="background: linear-gradient(135deg, #6b21a8 0%, #8b5cf6 100%);">
        <h4>My Bookings</h4>
        <div class="metric-value">{{ $metrics['my_bookings'] ?? 0 }}</div>
        <div class="metric-note">Total bookings you made</div>
      </div>
      <div class="metric-card" style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);">
        <h4>Pending</h4>
        <div class="metric-value">{{ $metrics['pending_bookings'] ?? 0 }}</div>
        <div class="metric-note">Awaiting confirmation</div>
      </div>
      <div class="metric-card" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
        <h4>Completed</h4>
        <div class="metric-value">{{ $metrics['completed_bookings'] ?? 0 }}</div>
        <div class="metric-note">Successfully completed</div>
      </div>
    </div>

    <div class="summary-card">
      <div class="summary-head">
        <div>
          <h3>Quick Links</h3>
          <div class="subtitle" style="margin-top:.25rem;">Manage your bookings and itinerary.</div>
        </div>
      </div>
      <div class="summary-body">
        <div class="quick-links">
          <a class="quick-link" href="{{ route('booking.create') }}">Create Booking <span>→</span></a>
          <a class="quick-link" href="{{ route('user.bookings.history') }}">My Bookings <span>→</span></a>
          <a class="quick-link" href="{{ route('itineraries.index') }}">My Itineraries <span>→</span></a>
          <a class="quick-link" href="{{ route('accounts.show', auth()->id()) }}">My Account <span>→</span></a>
        </div>
      </div>
    </div>
    @endif
  </main>

  @if($role === 'super_admin')
  <aside class="dashboard-right">
    <div class="right-card">
      <div class="right-card-header">
        <h3>Jadwal Booking Terbaru</h3>
        <a href="{{ route('dashboard.calendar') }}" class="subtitle" style="text-decoration:none; font-weight:700;">Lihat kalender</a>
      </div>
      <div class="schedule" id="schedule_list">
        <div class="empty-state">Memuat jadwal...</div>
      </div>
    </div>
  </aside>
  @endif
</div>

<div id="booking_modal" class="modal-backdrop">
  <div class="modal-card">
    <div class="modal-header">
      <div id="booking_modal_title" class="modal-title">Detail Booking</div>
      <button id="booking_modal_close" class="modal-close">Tutup</button>
    </div>
    <div id="booking_modal_body" class="modal-grid"></div>
    <div class="modal-section" style="margin-top:.75rem;">
      <div class="subtitle" style="margin-bottom:.35rem; font-weight:700;">Travel Plans</div>
      <div class="wysiwyg-view">
        <div id="booking_modal_travel_content" class="w-content"></div>
      </div>
    </div>
    @if(auth()->user()?->role === 'super_admin')
    <div class="modal-actions">
      <a id="booking_modal_edit" href="#" class="btn btn-primary" style="display:none;">Edit Booking</a>
    </div>
    @endif
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
  const scheduleEl = document.getElementById('schedule_list');
  const modal = document.getElementById('booking_modal');
  const modalBody = document.getElementById('booking_modal_body');
  const modalTitle = document.getElementById('booking_modal_title');
  const modalEdit = document.getElementById('booking_modal_edit');
  const modalTravel = document.getElementById('booking_modal_travel_content');

  function sanitizeHtml(html) {
    const allowed = new Set(['DIV','B','I','U','STRONG','EM','P','BR','UL','OL','LI','H1','H2','H3','H4','H5','H6','BLOCKQUOTE','A','SPAN']);
    const parser = new DOMParser();
    const doc = parser.parseFromString('<div>' + (html || '') + '</div>', 'text/html');
    function walk(node) {
      const children = Array.from(node.childNodes);
      for (const child of children) {
        if (child.nodeType === Node.ELEMENT_NODE) {
          if (!allowed.has(child.tagName)) {
            const fragment = doc.createDocumentFragment();
            while (child.firstChild) fragment.appendChild(child.firstChild);
            child.replaceWith(fragment);
            continue;
          }
          const attrs = Array.from(child.attributes);
          for (const attr of attrs) {
            if (child.tagName === 'A' && attr.name === 'href') {
              if (!/^https?:\/\//i.test(attr.value)) child.removeAttribute(attr.name);
            } else {
              child.removeAttribute(attr.name);
            }
          }
        }
        walk(child);
      }
    }
    walk(doc.body);
    const wrapper = doc.querySelector('div');
    return wrapper ? wrapper.innerHTML : '';
  }

  function normalizeDate(value) {
    return (value ?? '').toString().substring(0, 10);
  }

  function normalizeTime(value) {
    return (value || '00:00').toString().substring(0, 5);
  }

  function openBookingModal(booking) {
    const range = normalizeDate(booking.booking_date) + (booking.end_date ? (' s/d ' + normalizeDate(booking.end_date)) : '');
    const vehicleStr = booking.vehicle ? (booking.vehicle.make + ' ' + booking.vehicle.model) : 'Tidak ada kendaraan';
    const mitraStr = booking.mitra && booking.mitra.full_name ? booking.mitra.full_name : 'No driver';
    const rows = [
      ['Nama', booking.customer_name || '-'],
      ['Tanggal', range || '-'],
      ['Waktu Jemput', normalizeTime(booking.pickup_time)],
      ['Lokasi Jemput', booking.pickup_location || '-'],
      ['Kendaraan', vehicleStr],
      ['Mitra', mitraStr],
      ['Status', booking.status || '-'],
    ];

    modalTitle.textContent = 'Detail Booking';
    modalBody.innerHTML = '';
    rows.forEach(function ([label, value]) {
      const row = document.createElement('div');
      row.className = 'modal-row';
      const kk = document.createElement('span');
      kk.className = 'label';
      kk.textContent = label;
      const vv = document.createElement('div');
      vv.className = 'value';
      vv.textContent = value;
      row.appendChild(kk);
      row.appendChild(vv);
      modalBody.appendChild(row);
    });

    if (modalTravel) {
      modalTravel.innerHTML = sanitizeHtml(booking.travel_plans || '');
    }
    if (modalEdit) {
      modalEdit.href = '/bookings/' + booking.id + '/edit';
      modalEdit.style.display = 'inline-block';
    }
    modal.style.display = 'flex';
  }

  function closeModal() {
    modal.style.display = 'none';
    if (modalEdit) modalEdit.style.display = 'none';
    if (modalTravel) modalTravel.innerHTML = '';
  }

  try {
    const res = await fetch('/api/bookings', { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Network response was not ok');
    const data = await res.json();

    const parsed = data
      .filter(function (booking) {
        const endDate = normalizeDate(booking.end_date);
        const today = new Date().toISOString().substring(0, 10);
        return !endDate || endDate >= today;
      })
      .map(function (booking) {
        return {
          id: booking.id,
          raw: booking,
          date: booking.booking_date,
          displayDate: normalizeDate(booking.booking_date),
          time: normalizeTime(booking.pickup_time),
          title: booking.customer_name ?? 'Customer',
          meta: ((booking.mitra && booking.mitra.full_name) ? booking.mitra.full_name : 'No driver') + (booking.pickup_location ? (' • ' + booking.pickup_location) : '')
        };
      })
      .sort((a, b) => (b.date + ' ' + b.time).localeCompare(a.date + ' ' + a.time))
      .slice(0, 8);

    if (!parsed.length) {
      scheduleEl.innerHTML = '<div class="empty-state">Tidak ada booking.</div>';
    } else {
      scheduleEl.innerHTML = '';
      parsed.forEach(function (item) {
        const wrap = document.createElement('div');
        wrap.className = 'schedule-item';
        wrap.dataset.id = item.id;

        const time = document.createElement('div');
        time.className = 'schedule-time';
        const timeStrong = document.createElement('strong');
        timeStrong.textContent = item.time;
        const timeSpan = document.createElement('span');
        timeSpan.textContent = item.displayDate;
        time.appendChild(timeStrong);
        time.appendChild(timeSpan);

        const info = document.createElement('div');
        const title = document.createElement('div');
        title.className = 'schedule-title';
        title.textContent = item.title;
        const meta = document.createElement('div');
        meta.className = 'schedule-meta';
        meta.textContent = item.meta;
        info.appendChild(title);
        info.appendChild(meta);

        wrap.appendChild(time);
        wrap.appendChild(info);
        wrap.addEventListener('click', async function () {
          try {
            const r = await fetch('/api/bookings/' + item.id, { headers: { 'Accept': 'application/json' } });
            if (!r.ok) throw new Error('Bad response');
            const booking = await r.json();
            openBookingModal(booking);
          } catch (_) {
            alert('Gagal memuat detail booking');
          }
        });

        scheduleEl.appendChild(wrap);
      });
    }
  } catch (e) {
    scheduleEl.innerHTML = '<div class="empty-state">Gagal memuat jadwal.</div>';
  }

  document.getElementById('booking_modal_close').addEventListener('click', closeModal);
  document.getElementById('booking_modal').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
  });
});
</script>
@endsection
