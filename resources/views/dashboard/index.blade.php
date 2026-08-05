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

  .mobile-grid-menu,
  .mobile-more-panel {
    display: none;
    grid-template-columns: repeat(4, 1fr);
    gap: .6rem;
    padding: .75rem;
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
    grid-column: 1 / -1;
  }
  .mobile-more-panel { margin-top: .5rem; }
  .mobile-more-panel.show { display: grid; }
  .mobile-grid-menu a,
  .mobile-more-panel a {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .4rem;
    text-decoration: none;
    color: #334155;
    padding: .6rem .2rem;
    border-radius: 14px;
    transition: background .15s ease;
  }
  .mobile-grid-menu a:hover,
  .mobile-more-panel a:hover { background: #f1f5f9; }
  .mobile-grid-menu .icon-circle,
  .mobile-more-panel .icon-circle {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
  }
  .mobile-grid-menu .icon-circle svg,
  .mobile-more-panel .icon-circle svg {
    width: 26px;
    height: 26px;
    stroke-width: 1.8;
    color: #fff;
  }
  .mobile-grid-menu .icon-label,
  .mobile-more-panel .icon-label {
    font-size: .68rem;
    font-weight: 600;
    text-align: center;
    line-height: 1.2;
  }
  @media (max-width: 768px) {
    .mobile-grid-menu { display: grid; }
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
    .dashboard-sidebar,
    .dashboard-hero,
    .summary-card { display: none !important; }
    .modal-header {
      flex-direction: column;
    }

    .quick-links,
    .modal-grid {
      grid-template-columns: 1fr;
    }
    .dashboard-cards {
      grid-template-columns: repeat(2, 1fr);
      gap: .5rem;
    }
    .metric-card {
      padding: .6rem;
      border-radius: 14px;
      text-align: center;
    }
    .metric-card h4 {
      font-size: .72rem;
      margin-bottom: .15rem;
    }
    .metric-value {
      font-size: 2rem;
    }
    .metric-note { display: none; }

    .schedule-item {
      grid-template-columns: 56px minmax(0, 1fr);
    }
    .dashboard-main {
      min-height: auto;
      padding: 0;
      gap: .75rem;
      background: transparent;
      border: none;
      border-radius: 0;
      box-shadow: none;
      backdrop-filter: none;
    }
    .dashboard-shell {
      gap: .75rem;
      align-items: start;
    }
    .dashboard-shell > .mobile-grid-menu { order: 1; }
    .dashboard-shell > .mobile-more-panel { order: 2; }
    .dashboard-shell > .dashboard-main { order: 0; }
    .dashboard-shell > .dashboard-right { order: 3; }
    .dashboard-right {
      padding: 0;
      background: transparent;
      border: none;
      border-radius: 0;
      box-shadow: none;
      backdrop-filter: none;
    }
    .right-card {
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,.04);
    }
  }
</style>

<div class="dashboard-shell">
  @include('partials.admin-sidebar')

  @if($role === 'super_admin')
  <div class="mobile-grid-menu" id="mobileGridMenu">
    <a href="{{ route('dashboard') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#2563eb,#3b82f6);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      </div>
      <span class="icon-label">Dashboard</span>
    </a>
    <a href="{{ route('bookings.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#7c3aed,#a78bfa);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      </div>
      <span class="icon-label">Bookings</span>
    </a>
    <a href="{{ route('booking-data.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#0891b2,#22d3ee);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      </div>
      <span class="icon-label">Data Booking</span>
    </a>
    <a href="{{ route('laporan-keuangan.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#059669,#34d399);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <span class="icon-label">Keuangan</span>
    </a>
    <a href="{{ route('dashboard.calendar') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#ea580c,#fb923c);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <span class="icon-label">Kalender</span>
    </a>
    <a href="{{ route('vehicles.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#dc2626,#f87171);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      </div>
      <span class="icon-label">Kendaraan</span>
    </a>
    <a href="{{ route('mitras.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#d946ef,#e879f9);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <span class="icon-label">Mitra</span>
    </a>
    <a href="javascript:void(0)" onclick="document.getElementById('mobileMorePanel').classList.toggle('show')">
      <div class="icon-circle" style="background:linear-gradient(135deg,#64748b,#94a3b8);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
      </div>
      <span class="icon-label">Lainnya</span>
    </a>
  </div>
  <div class="mobile-more-panel" id="mobileMorePanel">
    <a href="{{ route('booking.create') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#4f46e5,#818cf8);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </div>
      <span class="icon-label">Tambah Booking</span>
    </a>
    <a href="{{ route('services.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#0d9488,#5eead4);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      </div>
      <span class="icon-label">Layanan</span>
    </a>
    <a href="{{ route('groups.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#ca8a04,#facc15);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <span class="icon-label">Groups</span>
    </a>
    <a href="{{ route('accounts.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#be185d,#f472b6);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <span class="icon-label">Akun</span>
    </a>
    <a href="{{ route('itineraries.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#4338ca,#6366f1);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
      </div>
      <span class="icon-label">Itinerary</span>
    </a>
    <a href="{{ route('settings.smtp') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#71717a,#a1a1aa);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      </div>
      <span class="icon-label">Pengaturan</span>
    </a>
    <a href="{{ route('email-logs.index') }}">
      <div class="icon-circle" style="background:linear-gradient(135deg,#b45309,#fbbf24);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      </div>
      <span class="icon-label">Email Log</span>
    </a>
  </div>
  @endif

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
    <div class="dashboard-cards" style="grid-template-columns: repeat(2, 1fr);">
      <div class="metric-card" style="background: linear-gradient(135deg, #0891b2 0%, #22d3ee 100%);">
        <h4>Total Booking</h4>
        <div class="metric-value">{{ $metrics['bookings'] ?? 0 }}</div>
      </div>
      <div class="metric-card" style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);">
        <h4>Pendapatan Kotor</h4>
        <div class="metric-value">Rp {{ number_format($metrics['gross_revenue'] ?? 0, 0, ',', '.') }}</div>
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
