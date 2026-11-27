@extends('layouts.app', ['title' => 'Kalender Booking'])

@section('content')
<style>
  body { background: #eef2f7; }
  .dashboard-shell { display: block; padding-left: 252px; min-height: calc(100vh - 4rem); }
  .dashboard-sidebar {
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 252px;
    height: 100vh;
    max-height: none;
    margin: 0;
    border: none;
    border-radius: 0;
    padding: 1.1rem .9rem 1.25rem;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 60;
    color: #fff;
    background: linear-gradient(170deg, #1f63e8 0%, #0d4cd0 42%, #093fa9 100%);
    box-shadow: 6px 0 24px rgba(9, 63, 169, .16);
  }
  .dashboard-sidebar::after {
    content: "";
    display: block;
    flex: 0 0 auto;
    margin-top: 1.25rem;
    height: 168px;
    border-radius: 14px;
    background:
      linear-gradient(180deg, rgba(31, 99, 232, 0) 0%, rgba(9, 63, 169, .35) 100%),
      url('{{ asset('assets/img/gedung-sate-drive.png') }}') 14% 82% / 200% auto no-repeat;
  }
  .dashboard-brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .45rem;
    padding: 0 .3rem 1rem;
    margin-bottom: .75rem;
    border-bottom: 1px solid rgba(255, 255, 255, .18);
  }
  .dashboard-brand img {
    width: 142px;
    height: auto;
    padding: .3rem .45rem;
    border-radius: 10px;
    background: #ffffff;
    object-fit: contain;
  }
  .dashboard-brand h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #ffffff;
  }
  .dashboard-brand .subtitle {
    font-size: .82rem;
    color: rgba(255, 255, 255, .82);
  }
  .dashboard-section-label {
    margin: .85rem .3rem .4rem;
    padding: 0;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .6);
  }
  .dashboard-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: .12rem;
  }
  .dashboard-menu a,
  .dashboard-menu button {
    display: flex;
    align-items: center;
    gap: .6rem;
    width: 100%;
    padding: .48rem .55rem;
    border: none;
    border-radius: 11px;
    background: transparent;
    color: #e9f1ff;
    font-family: inherit;
    font-size: .87rem;
    font-weight: 600;
    text-align: left;
    text-decoration: none;
    cursor: pointer;
    transition: background .15s ease, color .15s ease;
  }
  .dashboard-menu a:hover,
  .dashboard-menu button:hover {
    background: rgba(255, 255, 255, .12);
    color: #ffffff;
  }
  .dashboard-menu a.active {
    background: #ffffff;
    color: #0b4fd6;
    box-shadow: 0 6px 14px rgba(4, 33, 90, .22);
  }
  .dashboard-menu .nav-icon {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(255, 255, 255, .14);
    color: #ffffff;
    font-size: .9rem;
  }
  .dashboard-menu a.active .nav-icon {
    background: #e7f0ff;
    color: #0b4fd6;
  }
  .dashboard-main { padding: 1rem 1.25rem 4.5rem; min-width: 0; }
  .dashboard-topbar {
    position: sticky; top: 0; z-index: 40; display: flex; align-items: center; gap: .75rem;
    background: rgba(255,255,255,.92); backdrop-filter: blur(10px);
    border: 1px solid #e2e8f0; border-radius: 18px; padding: .6rem .7rem;
    box-shadow: 0 8px 24px rgba(15,23,42,.06); margin-bottom: 1rem;
  }
  .topbar-search { position: relative; flex: 1 1 auto; max-width: 460px; }
  .topbar-search input {
    width: 100%; padding: .62rem .9rem .62rem 2.2rem; border-radius: 12px;
    border: 1px solid #dbe3ef; background: #f8fafc; font-size: .9rem; color: #0f172a;
  }
  .topbar-search input:focus { outline: none; border-color: #93c5fd; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
  .topbar-search svg { position: absolute; left: .7rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8; }
  .topbar-search kbd {
    position: absolute; right: .5rem; top: 50%; transform: translateY(-50%);
    font-size: .68rem; color: #94a3b8; border: 1px solid #e2e8f0; background: #fff;
    border-radius: 6px; padding: .1rem .35rem;
  }
  .topbar-actions { display: flex; align-items: center; gap: .45rem; margin-left: auto; }
  .topbar-icon {
    display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px;
    border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; color: #475569; cursor: pointer;
    transition: all .15s ease; text-decoration: none;
  }
  .topbar-icon:hover { background: #f1f5f9; color: #1d4ed8; border-color: #c7d2fe; }
  .topbar-icon svg { width: 18px; height: 18px; }
  .topbar-icon.active-pill { width: auto; padding: 0 .8rem; gap: .4rem; background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; font-weight: 700; font-size: .82rem; }
  .topbar-user {
    display: inline-flex; align-items: center; gap: .5rem; padding: .3rem .7rem .3rem .35rem;
    border-radius: 999px; border: 1px solid #e2e8f0; background: #fff;
  }
  .topbar-user .avatar {
    width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #2563eb, #4f46e5); color: #fff; font-weight: 800; font-size: .78rem;
  }
  .topbar-user .user-meta { line-height: 1.1; }
  .topbar-user .user-meta strong { display: block; font-size: .78rem; color: #0f172a; }
  .topbar-user .user-meta span { font-size: .68rem; color: #94a3b8; }
  .dashboard-hero {
    position: relative; overflow: hidden; border-radius: 22px; padding: 1.5rem 1.6rem; color: #fff;
    background: linear-gradient(120deg, #1e40af 0%, #2563eb 55%, #3b82f6 100%);
    box-shadow: 0 18px 38px rgba(30, 64, 175, .25); margin-bottom: 1rem;
  }
  .dashboard-hero::after {
    content: ""; position: absolute; right: -10%; top: 0; bottom: 0; width: 60%;
    background: url('{{ asset('assets/img/gedung-sate-drive.png') }}') no-repeat right center / contain;
    opacity: .38; pointer-events: none;
  }
  .dashboard-hero h1 { position: relative; margin: 0 0 .35rem; font-size: 1.6rem; font-weight: 800; letter-spacing: -.02em; }
  .dashboard-hero p { position: relative; margin: 0; color: #dbeafe; font-size: .9rem; max-width: 520px; }
  .status-cards { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .85rem; margin-bottom: 1rem; }
  .status-card {
    background: #fff; border: 1px solid #e6ecf5; border-radius: 18px; padding: .95rem 1.05rem;
    box-shadow: 0 10px 24px rgba(15,23,42,.05); display: flex; align-items: center; gap: .85rem;
  }
  .status-card .icon {
    width: 44px; height: 44px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center;
    background: #eff6ff; color: #1d4ed8; flex: 0 0 auto;
  }
  .status-card .icon svg { width: 21px; height: 21px; }
  .status-card .meta span { display: block; font-size: .76rem; color: #64748b; font-weight: 600; }
  .status-card .meta strong { display: block; font-size: 1.35rem; font-weight: 800; color: #0f172a; letter-spacing: -.02em; }
  .calendar-panel { background: #fff; border: 1px solid #e6ecf5; border-radius: 22px; padding: 1rem 1.1rem 1.2rem; box-shadow: 0 14px 30px rgba(15,23,42,.06); }
  .calendar-panel-head { display: flex; align-items: center; justify-content: space-between; gap: .75rem; flex-wrap: wrap; margin-bottom: .85rem; }
  .calendar-panel-head h2 { margin: 0; font-size: 1.02rem; font-weight: 800; color: #0f172a; }
  .calendar-panel-head p { margin: .15rem 0 0; font-size: .8rem; color: #64748b; }
  .calendar-legend { display: flex; gap: .85rem; flex-wrap: wrap; font-size: .76rem; color: #475569; font-weight: 600; }
  .calendar-legend i { display: inline-block; width: 9px; height: 9px; border-radius: 50%; margin-right: .3rem; }
  #calendar { min-height: 620px; }
  .fc .fc-toolbar-title { font-size: 1.05rem; font-weight: 800; color: #0f172a; }
  .fc .fc-button { background: #eff6ff; border: 1px solid #dbeafe; color: #1d4ed8; font-weight: 700; font-size: .8rem; box-shadow: none; padding: .35rem .7rem; }
  .fc .fc-button:hover, .fc .fc-button:focus { background: #dbeafe; border-color: #bfdbfe; color: #1e40af; }
  .fc .fc-button-primary:not(:disabled).fc-button-active,
  .fc .fc-button-primary:not(:disabled):active { background: #2563eb; border-color: #2563eb; color: #fff; }
  .fc-theme-standard th { background: #f8fafc; color: #64748b; font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; padding: .5rem 0; }
  .fc-theme-standard td, .fc-theme-standard th { border-color: #eef2f7; }
  .fc .fc-daygrid-day-number { font-size: .78rem; color: #475569; font-weight: 600; }
  .fc .fc-day-today { background: #f5f9ff !important; }
  .fc-event { border-radius: 8px; border: none; padding: 2px 6px; font-size: .72rem; font-weight: 600; cursor: pointer; }
  .site-footer { position: fixed; left: 252px; right: 0; bottom: 0; z-index: 45; margin: 0; background: #fff; border-top: 1px solid #e2e8f0; }
  body.admin-shell .site-footer-inner { padding: 0 1.25rem; }
  .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.45); z-index: 110; }
  body.sidebar-open .sidebar-backdrop { display: block; }
  #sidebarToggle { display: none; }
  @media (max-width: 1200px) {
    .dashboard-shell { padding-left: 0; }
    #sidebarToggle { display: inline-flex; }
    .dashboard-sidebar { display: none; }
    body.sidebar-open .dashboard-sidebar {
      display: flex;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: 270px;
      height: 100vh;
      max-height: none;
      overflow-y: auto;
      z-index: 120;
      border-radius: 0;
      box-shadow: 0 0 40px rgba(0, 0, 0, .35);
    }
    .site-footer { left: 0; }
  }
  @media (max-width: 768px) {
    .status-cards { grid-template-columns: 1fr; }
    .dashboard-hero h1 { font-size: 1.3rem; }
    #calendar { min-height: 520px; }
    .topbar-user .user-meta { display: none; }
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
    max-height: calc(100vh - 2rem);
    display: flex;
    flex-direction: column;
    padding: 1.2rem;
    border-radius: 24px;
    background: rgba(255,255,255,.96);
    border: 1px solid rgba(226,232,240,.95);
    box-shadow: 0 30px 60px rgba(15, 23, 42, .25);
  }

  .modal-scroll {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overscroll-behavior: contain;
    padding-right: .3rem;
  }

  .modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: .9rem;
    flex-shrink: 0;
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

  .modal-section {
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

  .modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: .5rem;
    margin-top: 1rem;
    flex-shrink: 0;
  }

  @media (max-width: 768px) {
    .modal-header { flex-direction: column; }
    .modal-grid { grid-template-columns: 1fr; }
  }
</style>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<div class="dashboard-shell">
  @include('partials.admin-sidebar')

  <div class="dashboard-main">
    <header class="dashboard-topbar">
      <button type="button" class="topbar-icon" id="sidebarToggle" aria-label="Buka menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
      </button>
      <form class="topbar-search" method="GET" action="{{ route('bookings.index') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5" stroke-linecap="round"/></svg>
        <input type="search" name="q" id="globalSearch" placeholder="Cari booking, pelanggan, negara..." value="{{ request('q') }}" autocomplete="off">
        <kbd>Ctrl K</kbd>
      </form>
      <div class="topbar-actions">
        <a class="topbar-icon" href="{{ route('bookings.index') }}" title="Data booking">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h12" stroke-linecap="round"/></svg>
        </a>
        <a class="topbar-icon" href="{{ route('dashboard') }}" title="Dashboard">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 13h6V4H4v9Zm10 7h6v-9h-6v9ZM4 20h6v-4H4v4Zm10-11h6V4h-6v5Z"/></svg>
        </a>
        <span class="topbar-icon active-pill">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 11h18"/></svg>
          Kalender
        </span>
        <div class="topbar-user">
          <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
          <span class="user-meta">
            <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
            <span>{{ ucfirst(auth()->user()->role ?? 'admin') }}</span>
          </span>
        </div>
      </div>
    </header>

    <section class="dashboard-hero">
      <h1>Kalender Booking</h1>
      <p>Lihat jadwal perjalanan per tanggal, klik event untuk membuka detail booking.</p>
    </section>

    <section class="status-cards">
      <article class="status-card">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 11h18"/></svg></span>
        <div class="meta"><span>Total Booking</span><strong id="statTotal">-</strong></div>
      </article>
      <article class="status-card">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 2" stroke-linecap="round"/></svg></span>
        <div class="meta"><span>Hari Ini</span><strong id="statToday">-</strong></div>
      </article>
      <article class="status-card">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 18h16M6 18V9m6 9V5m6 13v-6" stroke-linecap="round"/></svg></span>
        <div class="meta"><span>Akan Datang</span><strong id="statUpcoming">-</strong></div>
      </article>
    </section>

    <section class="calendar-panel">
      <div class="calendar-panel-head">
        <div>
          <h2>Jadwal Perjalanan</h2>
          <p>Klik pada event untuk melihat detail booking.</p>
        </div>
        <div class="calendar-legend">
          <span><i style="background:#22c55e"></i>Confirmed</span>
          <span><i style="background:#f59e0b"></i>Pending</span>
          <span><i style="background:#ef4444"></i>Cancelled</span>
          <span><i style="background:#64748b"></i>Lainnya</span>
        </div>
      </div>
      <div id="calendar"></div>
    </section>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const modal = document.getElementById('calendar_booking_modal');
  const modalBody = document.getElementById('calendar_booking_modal_body');
  const modalTitle = document.getElementById('calendar_booking_modal_title');
  const modalEdit = document.getElementById('calendar_booking_modal_edit');
  const modalTravel = document.getElementById('calendar_booking_modal_travel_content');
  const statTotal = document.getElementById('statTotal');
  const statToday = document.getElementById('statToday');
  const statUpcoming = document.getElementById('statUpcoming');

  let bookingCache = [];

  function safeText(value) {
    return (value ?? '-').toString();
  }

  function normalizeDate(value) {
    if (!value) return '';
    return value.toString().substring(0, 10);
  }

  function normalizeTime(value) {
    if (!value) return '-';
    return value.toString().substring(0, 5);
  }

  function statusLabel(status) {
    const value = (status || '').toLowerCase();
    if (value === 'baru_masuk') return 'New';
    if (value === 'konfirmasi') return 'Confirmed';
    if (value === 'dijadwalkan') return 'Scheduled';
    if (['cancelled', 'cancel', 'batal'].includes(value)) return 'Cancelled';
    if (value === 'selesai_pelayanan' || value === 'completed') return 'Completed';
    if (value === 'selesai_administrasi_fee') return 'Completed';
    return value ? value.replace(/_/g, ' ') : '-';
  }

  function colorForStatus(status) {
    const value = (status || '').toLowerCase();
    if (['cancelled', 'cancel', 'batal'].includes(value)) return { background: '#fee2e2', border: '#fca5a5', text: '#991b1b' };
    if (['selesai_pelayanan', 'selesai_administrasi_fee', 'completed'].includes(value)) return { background: '#dcfce7', border: '#86efac', text: '#166534' };
    if (['konfirmasi', 'dijadwalkan'].includes(value)) return { background: '#fef3c7', border: '#fcd34d', text: '#92400e' };
    return { background: '#dbeafe', border: '#93c5fd', text: '#1d4ed8' };
  }

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

  function buildRange(booking) {
    const start = normalizeDate(booking.booking_date);
    const end = normalizeDate(booking.end_date);
    if (!start) return '-';
    return end && end !== start ? `${start} to ${end}` : start;
  }

  function openBookingModal(booking) {
    const vehicleStr = booking.vehicle ? `${booking.vehicle.make || ''} ${booking.vehicle.model || ''}`.trim() : 'No vehicle';
    const mitraStr = booking.mitra && booking.mitra.full_name ? booking.mitra.full_name : 'No mitra';
    const serviceStr = booking.service && booking.service.name ? booking.service.name : 'No service';
    const details = [
      ['Customer Name', booking.customer_name || '-'],
      ['Booking Date', buildRange(booking)],
      ['Pickup Time', normalizeTime(booking.pickup_time)],
      ['Pickup Location', booking.pickup_location || '-'],
      ['Vehicle', vehicleStr],
      ['Service', serviceStr],
      ['Mitra', mitraStr],
      ['Status', statusLabel(booking.status)],
      ['Payment', booking.payment_plan_label || booking.payment_plan || '-'],
      ['Price', booking.price ? 'IDR ' + Number(booking.price).toLocaleString('id-ID') : '-'],
    ];

    modalTitle.textContent = 'Booking Details';
    modalBody.innerHTML = '';
    details.forEach(function (pair) {
      const row = document.createElement('div');
      row.className = 'modal-row';
      const label = document.createElement('span');
      label.className = 'label';
      label.textContent = pair[0];
      const value = document.createElement('div');
      value.className = 'value';
      value.textContent = pair[1];
      row.appendChild(label);
      row.appendChild(value);
      modalBody.appendChild(row);
    });

    if (modalTravel) {
      modalTravel.innerHTML = sanitizeHtml(booking.travel_plans || '<span style="color:#64748b;">No travel plans.</span>');
    }

    if (modalEdit) {
      modalEdit.href = '/bookings/' + booking.id + '/edit';
      modalEdit.style.display = 'inline-block';
    }

    modal.style.display = 'flex';
  }

  function closeBookingModal() {
    modal.style.display = 'none';
    if (modalEdit) modalEdit.style.display = 'none';
    if (modalTravel) modalTravel.innerHTML = '';
  }

  async function loadStats() {
    const res = await fetch('/api/bookings/stats', { headers: { 'Accept': 'application/json' } });
    if (!res.ok) return;
    const stats = await res.json();
    statTotal.textContent = Number(stats.total || 0).toString();
    statToday.textContent = Number(stats.today || 0).toString();
    statUpcoming.textContent = Number(stats.upcoming || 0).toString();
  }

  loadStats();

  async function loadBookings(range) {
    const params = new URLSearchParams();
    if (range && range.startStr && range.endStr) {
      params.set('start', range.startStr.substring(0, 10));
      params.set('end', range.endStr.substring(0, 10));
    }
    const query = params.toString() ? ('?' + params.toString()) : '';
    const res = await fetch('/api/bookings' + query, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Failed to load bookings');
    const data = await res.json();
    bookingCache = data.map(function (booking) {
      const date = normalizeDate(booking.booking_date);
      const endDate = normalizeDate(booking.end_date);
      const time = normalizeTime(booking.pickup_time);
      const colors = colorForStatus(booking.status);
      const vehicle = booking.vehicle ? `${booking.vehicle.make || ''} ${booking.vehicle.model || ''}`.trim() : '';
      const meta = booking.pickup_location || booking.contact_number || '';
      return {
        id: booking.id,
        raw: booking,
        date: date,
        endDate: endDate,
        time: time,
        displayDate: date || '-',
        title: booking.customer_name || 'Booking',
        meta: meta,
        vehicle: vehicle,
        status: booking.status,
        start: date || null,
        end: endDate ? (function() {
          const parts = endDate.split('-');
          const d = new Date(Date.UTC(+parts[0], +parts[1] - 1, +parts[2] + 1));
          return d.toISOString().substring(0, 10);
        })() : null,
        backgroundColor: colors.background,
        borderColor: colors.border,
        textColor: colors.text,
      };
    }).sort(function (a, b) {
      return (b.date + ' ' + b.time).localeCompare(a.date + ' ' + a.time);
    });
    return bookingCache;
  }

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 'auto',
    firstDay: 1,
    dayMaxEvents: true,
    nowIndicator: true,
    navLinks: true,
    locale: 'en',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,listMonth',
    },
    buttonText: {
      today: 'Today',
      month: 'Month',
      week: 'Week',
      list: 'List',
    },
    events: async function (fetchInfo, success, failure) {
      try {
        const items = await loadBookings(fetchInfo);
        success(items.map(function (item) {
          return {
            id: item.id,
            title: item.title,
            start: item.start,
            end: item.end,
            allDay: true,
            backgroundColor: item.backgroundColor,
            borderColor: item.borderColor,
            textColor: item.textColor,
            extendedProps: {
              status: item.status,
              raw: item.raw,
              meta: item.meta,
              vehicle: item.vehicle,
            },
          };
        }));
      } catch (error) {
        failure(error);
      }
    },
    eventContent: function (arg) {
      const wrap = document.createElement('div');
      wrap.style.display = 'grid';
      wrap.style.gap = '.15rem';
      wrap.style.overflow = 'hidden';
      const title = document.createElement('div');
      title.style.fontWeight = '800';
      title.style.fontSize = '.78rem';
      title.style.lineHeight = '1.2';
      title.style.overflow = 'hidden';
      title.style.textOverflow = 'ellipsis';
      title.style.whiteSpace = 'nowrap';
      title.textContent = arg.event.title;
      const meta = document.createElement('div');
      meta.style.fontSize = '.65rem';
      meta.style.opacity = '.85';
      meta.style.overflow = 'hidden';
      meta.style.textOverflow = 'ellipsis';
      meta.style.whiteSpace = 'nowrap';
      const raw = arg.event.extendedProps.raw || {};
      const time = normalizeTime(raw.pickup_time);
      meta.textContent = `${time} · ${statusLabel(arg.event.extendedProps.status)}`;
      wrap.appendChild(title);
      wrap.appendChild(meta);
      const mitraName = (raw.mitra && raw.mitra.full_name) ? raw.mitra.full_name : '';
      if (mitraName) {
        const mitraBadge = document.createElement('div');
        mitraBadge.style.fontSize = '.6rem';
        mitraBadge.style.fontWeight = '600';
        mitraBadge.style.padding = '.1rem .35rem';
        mitraBadge.style.borderRadius = '.25rem';
        mitraBadge.style.background = 'rgba(0,0,0,.15)';
        mitraBadge.style.color = 'rgba(0,0,0,.7)';
        mitraBadge.style.display = 'inline-block';
        mitraBadge.style.width = 'fit-content';
        mitraBadge.style.overflow = 'hidden';
        mitraBadge.style.textOverflow = 'ellipsis';
        mitraBadge.style.whiteSpace = 'nowrap';
        mitraBadge.textContent = mitraName;
        wrap.appendChild(mitraBadge);
      }
      return { domNodes: [wrap] };
    },
    eventClick: async function (info) {
      try {
        const raw = info.event.extendedProps.raw;
        if (raw) {
          openBookingModal(raw);
          return;
        }
        const response = await fetch('/api/bookings/' + info.event.id, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Bad response');
        const booking = await response.json();
        openBookingModal(booking);
      } catch (_) {
        alert('Failed to load booking details');
      }
    },
  });

  calendar.render();

  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarBackdrop = document.getElementById('sidebarBackdrop');
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function () {
      document.body.classList.toggle('sidebar-open');
    });
  }
  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', function () {
      document.body.classList.remove('sidebar-open');
    });
  }
  const globalSearch = document.getElementById('globalSearch');
  document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      if (globalSearch) globalSearch.focus();
    }
    if (e.key === 'Escape') {
      document.body.classList.remove('sidebar-open');
    }
  });

  document.getElementById('calendar_booking_modal_close').addEventListener('click', closeBookingModal);
  document.getElementById('calendar_booking_modal').addEventListener('click', function (e) {
    if (e.target === this) closeBookingModal();
  });
});
</script>

<div id="calendar_booking_modal" class="modal-backdrop">
  <div class="modal-card">
    <div class="modal-header">
      <div>
        <div id="calendar_booking_modal_title" class="modal-title">Booking Details</div>
        <div class="subtitle" style="margin-top:.25rem;">Complete booking information for quick review.</div>
      </div>
      <button id="calendar_booking_modal_close" class="modal-close">Close</button>
    </div>

    <div class="modal-scroll">
      <div id="calendar_booking_modal_body" class="modal-grid"></div>

      <div class="modal-section">
        <div class="subtitle" style="margin-bottom:.35rem; font-weight:700;">Travel Plans</div>
        <div id="calendar_booking_modal_travel" class="wysiwyg-view">
          <div id="calendar_booking_modal_travel_content" class="w-content"></div>
        </div>
      </div>
    </div>

    @if(auth()->user()?->role === 'super_admin')
    <div class="modal-actions">
      <a id="calendar_booking_modal_edit" href="#" class="btn btn-primary" style="display:none;">Edit Booking</a>
    </div>
    @endif
  </div>
</div>
@endsection
