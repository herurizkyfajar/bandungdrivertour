@extends('layouts.app', ['title' => 'Calendar'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .dashboard-wrap {
    display: grid;
    grid-template-columns: 250px minmax(0, 1fr) 320px;
    gap: 1.25rem;
    align-items: start;
  }

  .sidebar,
  .calendar-side,
  .content-card {
    background: rgba(255,255,255,.88);
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 24px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
    backdrop-filter: blur(10px);
  }

  .sidebar {
    padding: 1rem;
    height: fit-content;
    position: sticky;
    top: 76px;
  }

  .sidebar h3,
  .calendar-side h2,
  .content-card h2 {
    margin: 0;
    letter-spacing: -.02em;
  }

  .side-menu {
    list-style: none;
    padding: 0;
    margin: .9rem 0 0;
    display: grid;
    gap: .45rem;
  }

  .side-menu a {
    display: block;
    padding: .72rem .85rem;
    border-radius: 14px;
    text-decoration: none;
    color: var(--text);
    border: 1px solid #e5e7eb;
    background: #fff;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
  }

  .side-menu a:hover {
    transform: translateY(-1px);
    border-color: #c7d2fe;
    box-shadow: 0 10px 18px rgba(15, 23, 42, .06);
    background: #f8fafc;
  }

  .side-menu a.active {
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 12px 24px rgba(37, 99, 235, .25);
  }

  .content-card {
    padding: 1.15rem;
    min-width: 0;
    overflow: hidden;
  }

  .calendar-hero {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding: 1rem;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(37,99,235,.08), rgba(99,102,241,.08));
    border: 1px solid rgba(191,219,254,.8);
  }

  .calendar-hero h2 {
    font-size: 1.45rem;
  }

  .calendar-hero .subtitle {
    max-width: 56ch;
  }

  .hero-actions {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
  }

  .calendar-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .75rem;
    margin-bottom: 1rem;
  }

  .stat-card {
    border-radius: 18px;
    padding: .95rem 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
  }

  .stat-label {
    font-size: .82rem;
    color: var(--muted);
    margin-bottom: .25rem;
  }

  .stat-value {
    font-size: 1.55rem;
    font-weight: 800;
    letter-spacing: -.03em;
    line-height: 1.1;
  }

  .calendar-shell {
    background: #fff;
    border-radius: 22px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
  }

  .calendar-shell .fc {
    --fc-border-color: #e2e8f0;
    --fc-page-bg-color: #fff;
    --fc-neutral-bg-color: #f8fafc;
    --fc-list-event-hover-bg-color: #f8fafc;
    --fc-today-bg-color: rgba(59, 130, 246, .08);
  }

  .calendar-shell .fc .fc-toolbar {
    padding: 1rem 1rem .5rem;
    margin-bottom: 0;
  }

  .calendar-shell .fc .fc-toolbar-title {
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.02em;
  }

  .calendar-shell .fc .fc-button {
    border-radius: 12px !important;
    border: 1px solid #dbeafe !important;
    background: #fff !important;
    color: #0f172a !important;
    box-shadow: none !important;
    text-transform: capitalize;
  }

  .calendar-shell .fc .fc-button-primary:not(:disabled).fc-button-active,
  .calendar-shell .fc .fc-button-primary:not(:disabled):active {
    background: #2563eb !important;
    color: #fff !important;
    border-color: #2563eb !important;
  }

  .calendar-shell .fc .fc-daygrid-day-number,
  .calendar-shell .fc .fc-col-header-cell-cushion {
    color: #334155;
    text-decoration: none;
    font-weight: 600;
  }

  .calendar-shell .fc .fc-day-today {
    background: rgba(59,130,246,.08) !important;
  }

  .calendar-shell .fc .fc-event {
    border: 0;
    border-radius: 8px;
    padding: .15rem .3rem;
    box-shadow: 0 4px 8px rgba(15, 23, 42, .1);
    cursor: pointer;
  }

  .calendar-shell .fc-event-main {
    font-weight: 700;
    overflow: hidden;
  }

  .calendar-shell .fc .fc-daygrid-event {
    white-space: normal;
  }

  .calendar-side {
    padding: 1rem;
    position: sticky;
    top: 76px;
    height: fit-content;
  }

  .agenda-list {
    display: grid;
    gap: .75rem;
    margin-top: 1rem;
  }

  .agenda-item {
    display: grid;
    grid-template-columns: 64px minmax(0, 1fr);
    gap: .75rem;
    padding: .85rem;
    border-radius: 18px;
    border: 1px solid #e2e8f0;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
  }

  .agenda-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 24px rgba(15, 23, 42, .08);
  }

  .agenda-time {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 16px;
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    color: #fff;
    min-height: 66px;
    text-align: center;
    padding: .35rem;
  }

  .agenda-time strong {
    font-size: 1rem;
    line-height: 1.1;
  }

  .agenda-time span {
    font-size: .78rem;
    opacity: .9;
  }

  .agenda-title {
    font-weight: 800;
    color: #0f172a;
    margin-bottom: .2rem;
    letter-spacing: -.01em;
  }

  .agenda-meta {
    color: var(--muted);
    font-size: .9rem;
    line-height: 1.45;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .65rem;
    border-radius: 999px;
    font-size: .82rem;
    font-weight: 700;
    margin-top: .45rem;
  }

  .status-pill--masuk { background: #eff6ff; color: #1d4ed8; }
  .status-pill--proses { background: #fef3c7; color: #92400e; }
  .status-pill--cancel { background: #fee2e2; color: #b91c1c; }
  .status-pill--selesai { background: #dcfce7; color: #166534; }

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
  }

  @media (max-width: 1200px) {
    .dashboard-wrap { grid-template-columns: 1fr; }
    .sidebar { position: static; overflow: hidden; min-width: 0; }
    .calendar-side { display: none !important; }
  }

  @media (max-width: 768px) {
    .card { box-shadow: none; }
    .content-card { overflow: hidden; padding: .75rem; }
    .calendar-hero { flex-direction: column; padding: .75rem; }
    .calendar-hero h2 { font-size: 1.15rem; }
    .hero-actions { width: 100%; }
    .hero-actions .btn { flex: 1; text-align: center; }
    .calendar-stats { grid-template-columns: 1fr; gap: .5rem; }
    .stat-card { padding: .7rem .85rem; }
    .stat-value { font-size: 1.25rem; }
    .modal-header { flex-direction: column; }
    .modal-grid { grid-template-columns: 1fr; }

    .calendar-shell .fc .fc-toolbar {
      flex-direction: column;
      gap: .5rem;
      padding: .75rem;
    }
    .calendar-shell .fc .fc-toolbar-chunk {
      display: flex;
      justify-content: center;
    }
    .calendar-shell .fc .fc-toolbar-title {
      font-size: 1rem;
    }
    .calendar-shell .fc .fc-button {
      padding: .35rem .65rem !important;
      font-size: .78rem !important;
    }
    .calendar-shell .fc .fc-col-header-cell {
      padding: .3rem 0;
    }
    .calendar-shell .fc .fc-col-header-cell-cushion {
      font-size: .7rem;
      padding: .2rem 0;
    }
    .calendar-shell .fc .fc-daygrid-day {
      padding: .15rem;
    }
    .calendar-shell .fc .fc-daygrid-day-number {
      font-size: .72rem;
      padding: .2rem .3rem;
    }
    .calendar-shell .fc .fc-event {
      border-radius: 6px;
      padding: .1rem .2rem;
      margin-bottom: 1px;
    }
    .calendar-shell .fc-event-main {
      font-size: .65rem;
      line-height: 1.25;
    }
    .calendar-shell .fc .fc-daygrid-more-link {
      font-size: .65rem;
      padding: .1rem .25rem;
    }
  }
</style>

@php
  $sidebarActiveCalendar = request()->routeIs('dashboard.calendar');
@endphp

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <main>
    <div class="content-card">
      <div class="calendar-hero">
        <div>
          <h2>Booking Calendar</h2>
          <div class="subtitle" style="margin-top:.35rem;">
            Track bookings in a cleaner, faster, and easier-to-read calendar view.
          </div>
        </div>
        <div class="hero-actions">
          <a class="btn btn-primary" href="{{ route('bookings.index') }}">Manage Bookings</a>
          <a class="btn" href="{{ route('booking.create') }}">Add Booking</a>
        </div>
      </div>

      <div class="calendar-stats">
        <div class="stat-card">
          <div class="stat-label">Total Bookings</div>
          <div class="stat-value" id="statTotal">-</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Today's Bookings</div>
          <div class="stat-value" id="statToday">-</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Upcoming Bookings</div>
          <div class="stat-value" id="statUpcoming">-</div>
        </div>
      </div>

      <div class="calendar-shell">
        <div id="calendar"></div>
      </div>
    </div>
  </main>

  <aside class="calendar-side">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem;">
      <div>
        <h2>Recent Agenda</h2>
        <div class="subtitle" style="margin-top:.25rem;">Latest incoming bookings.</div>
      </div>
      <button type="button" class="btn" id="refreshAgendaBtn" style="padding:.55rem .8rem;">Refresh</button>
    </div>
    <div class="agenda-list" id="schedule_list"></div>
  </aside>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const modal = document.getElementById('calendar_booking_modal');
  const modalBody = document.getElementById('calendar_booking_modal_body');
  const modalTitle = document.getElementById('calendar_booking_modal_title');
  const modalEdit = document.getElementById('calendar_booking_modal_edit');
  const modalTravel = document.getElementById('calendar_booking_modal_travel_content');
  const scheduleEl = document.getElementById('schedule_list');
  const statTotal = document.getElementById('statTotal');
  const statToday = document.getElementById('statToday');
  const statUpcoming = document.getElementById('statUpcoming');
  const refreshAgendaBtn = document.getElementById('refreshAgendaBtn');

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

  function statusBadgeClass(status) {
    const value = (status || '').toLowerCase();
    if (['cancelled', 'cancel', 'batal'].includes(value)) return 'status-pill status-pill--cancel';
    if (['selesai_pelayanan', 'selesai_administrasi_fee', 'completed'].includes(value)) return 'status-pill status-pill--selesai';
    if (['konfirmasi', 'dijadwalkan'].includes(value)) return 'status-pill status-pill--proses';
    return 'status-pill status-pill--masuk';
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

  function renderAgenda(items) {
    if (!items.length) {
      scheduleEl.innerHTML = '<div class="subtitle" style="padding:.5rem 0;">No bookings.</div>';
      return;
    }

    scheduleEl.innerHTML = '';
    items.forEach(function (item) {
      const colors = colorForStatus(item.status);
      const card = document.createElement('div');
      card.className = 'agenda-item';
      card.style.borderColor = colors.border;

      const timeBox = document.createElement('div');
      timeBox.className = 'agenda-time';
      timeBox.style.background = `linear-gradient(135deg, ${colors.text} 0%, ${colors.background} 140%)`;
      timeBox.style.color = colors.text === '#92400e' ? '#92400e' : '#fff';

      const timeStrong = document.createElement('strong');
      timeStrong.textContent = item.time;
      const timeSpan = document.createElement('span');
      timeSpan.textContent = item.displayDate;
      timeBox.appendChild(timeStrong);
      timeBox.appendChild(timeSpan);

      const info = document.createElement('div');
      const title = document.createElement('div');
      title.className = 'agenda-title';
      title.textContent = item.title;
      const meta = document.createElement('div');
      meta.className = 'agenda-meta';
      meta.textContent = (item.meta || '-') + (item.vehicle ? (' · ' + item.vehicle) : '');
      const status = document.createElement('span');
      status.className = statusBadgeClass(item.status);
      status.textContent = statusLabel(item.status);

      info.appendChild(title);
      info.appendChild(meta);
      info.appendChild(status);

      card.appendChild(timeBox);
      card.appendChild(info);
      card.addEventListener('click', function () {
        openBookingModal(item.raw);
      });
      scheduleEl.appendChild(card);
    });
  }

  function updateStats(items) {
    const today = new Date();
    const todayStr = today.toISOString().substring(0, 10);
    const upcomingCount = items.filter(function (item) {
      return item.date && item.date >= todayStr;
    }).length;
    const todayCount = items.filter(function (item) {
      return item.date === todayStr;
    }).length;

    statTotal.textContent = items.length.toString();
    statToday.textContent = todayCount.toString();
    statUpcoming.textContent = upcomingCount.toString();
  }

  async function loadBookings() {
    const res = await fetch('/api/bookings', { headers: { 'Accept': 'application/json' } });
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
        const items = await loadBookings();
        updateStats(items);
        const todayStr = new Date().toISOString().substring(0, 10);
        renderAgenda(items.filter(function (item) {
          return !item.endDate || item.endDate >= todayStr;
        }).slice(0, 10));
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
        mitraBadge.style.background = 'rgba(255,255,255,.25)';
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
  refreshAgendaBtn?.addEventListener('click', function () {
    calendar.refetchEvents();
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

    <div id="calendar_booking_modal_body" class="modal-grid"></div>

    <div class="modal-section">
      <div class="subtitle" style="margin-bottom:.35rem; font-weight:700;">Travel Plans</div>
      <div id="calendar_booking_modal_travel" class="wysiwyg-view">
        <div id="calendar_booking_modal_travel_content" class="w-content"></div>
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
