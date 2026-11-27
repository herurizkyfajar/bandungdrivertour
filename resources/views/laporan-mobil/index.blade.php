@extends('layouts.app', ['title' => 'Laporan Mobil'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body { background: #eef5ff; }
  body::before {
    content: "";
    position: fixed;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    background:
      radial-gradient(1100px 460px at 88% -8%, #d4e9ff 0%, rgba(212, 233, 255, 0) 72%),
      linear-gradient(180deg, #e3f0fd 0%, #eef5ff 34%, #f7fbff 68%, #e9f7ee 100%);
  }
  .container { max-width: 100% !important; width: 100%; margin: 0 auto; padding: 1rem 1.25rem .5rem; }

  /* ===== Shell ===== */
  .lm-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 1.25rem;
    align-items: start;
    min-height: calc(100vh - 6rem);
    padding-left: 252px;
  }

  .lm-main {
    display: grid;
    gap: 1.05rem;
    align-content: start;
    min-width: 0;
    min-height: 0;
    padding: 0 0 1rem;
  }

  /* ===== Sidebar (sama dengan dashboard) ===== */
  .lm-shell > .dashboard-sidebar {
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
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
  }

  .lm-shell > .dashboard-sidebar::after {
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

  .lm-shell .dashboard-brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .45rem;
    padding: 0 .3rem 1rem;
    margin-bottom: .75rem;
    border-bottom: 1px solid rgba(255, 255, 255, .18);
  }

  .lm-shell .dashboard-brand img {
    width: 142px;
    height: auto;
    padding: .3rem .45rem;
    border-radius: 10px;
    background: #ffffff;
    object-fit: contain;
  }

  .lm-shell .dashboard-brand h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #ffffff;
  }

  .lm-shell .dashboard-brand .subtitle {
    font-size: .82rem;
    color: rgba(255, 255, 255, .82);
  }

  .lm-shell .dashboard-section-label {
    margin: .85rem .3rem .4rem;
    padding: 0;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .6);
  }

  .lm-shell .dashboard-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: .12rem;
  }

  .lm-shell .dashboard-menu a,
  .lm-shell .dashboard-menu button {
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

  .lm-shell .dashboard-menu a:hover,
  .lm-shell .dashboard-menu button:hover {
    background: rgba(255, 255, 255, .12);
    color: #ffffff;
  }

  .lm-shell .dashboard-menu a.active {
    background: #ffffff;
    color: #0b4fd6;
    box-shadow: 0 6px 14px rgba(4, 33, 90, .22);
  }

  .lm-shell .dashboard-menu .nav-icon {
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

  .lm-shell .dashboard-menu a.active .nav-icon {
    background: #e7f0ff;
    color: #0b4fd6;
  }


  /* ===== Topbar ===== */
  .lm-topbar {
    position: sticky;
    top: .5rem;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .85rem;
    padding: .6rem .8rem;
    border: 1px solid #e8f0fb;
    border-radius: 18px;
    background: rgba(255, 255, 255, .94);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    box-shadow: 0 8px 22px rgba(13, 43, 82, .05);
  }

  .lm-topbar-left { display: flex; align-items: center; gap: .6rem; min-width: 0; }
  .lm-topbar-right { display: flex; align-items: center; gap: .45rem; flex: 0 0 auto; }

  .lm-menu-btn {
    display: none;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: 1px solid #e6eef9;
    border-radius: 12px;
    background: #f6faff;
    color: #0b4fd6;
    font-size: 1.05rem;
    cursor: pointer;
    flex: 0 0 auto;
  }

  .lm-page-label {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .45rem .8rem;
    border-radius: 12px;
    background: #f4f9ff;
    border: 1px solid #e8f0fb;
    color: #0b2545;
    font-size: .86rem;
    font-weight: 800;
    letter-spacing: -.01em;
    white-space: nowrap;
  }

  .lm-page-label i { color: #1565c0; font-size: .95rem; }

  .lm-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 12px;
    border: 1px solid #e8f0fb;
    background: #ffffff;
    color: #1565c0;
    font-size: .95rem;
    text-decoration: none;
    cursor: pointer;
    transition: background .15s ease, border-color .15s ease;
  }

  .lm-icon-btn:hover { background: #f4f9ff; border-color: #bcd5f5; color: #0b4fd6; }

  .lm-user-wrap { position: relative; }

  .lm-user {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .3rem .7rem .3rem .35rem;
    border: 1px solid #e8eef7;
    border-radius: 999px;
    background: #ffffff;
    font: inherit;
    text-align: left;
    cursor: pointer;
    transition: border-color .15s ease, box-shadow .15s ease;
  }

  .lm-user:hover { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }
  .lm-user[aria-expanded="true"] { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }

  .lm-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #2f80ed, #0b4fd6);
    color: #ffffff;
    font-size: .88rem;
    font-weight: 800;
    flex-shrink: 0;
  }

  .lm-user-meta { display: flex; flex-direction: column; line-height: 1.15; min-width: 0; overflow: hidden; }
  .lm-user-meta strong { max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .82rem; font-weight: 800; color: #0b2545; }
  .lm-user-meta span { font-size: .7rem; color: #64748b; text-transform: capitalize; }

  .lm-user > i { color: #94a3b8; font-size: .75rem; transition: transform .15s ease; }
  .lm-user[aria-expanded="true"] > i { transform: rotate(180deg); }

  .lm-user-menu {
    position: absolute;
    top: calc(100% + .5rem);
    right: 0;
    z-index: 1200;
    min-width: 220px;
    padding: .4rem;
    border: 1px solid #e8eef7;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 18px 40px rgba(15, 23, 42, .14);
  }

  .lm-user-menu[hidden] { display: none; }

  .lm-user-menu-head {
    display: flex;
    flex-direction: column;
    gap: .15rem;
    padding: .55rem .7rem .6rem;
    margin-bottom: .35rem;
    border-bottom: 1px solid #eef2f8;
  }

  .lm-user-menu-head strong { font-size: .82rem; font-weight: 800; color: #0b2545; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .lm-user-menu-head span { font-size: .72rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  .lm-user-menu-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    width: 100%;
    padding: .55rem .7rem;
    border: 0;
    border-radius: 10px;
    background: transparent;
    color: #0b2545;
    font-family: inherit;
    font-size: .83rem;
    font-weight: 600;
    text-align: left;
    text-decoration: none;
    cursor: pointer;
  }

  .lm-user-menu-item i { font-size: .95rem; color: #64748b; }
  .lm-user-menu-item:hover { background: #f1f5fb; }
  .lm-user-menu-item--danger { color: #b91c1c; }
  .lm-user-menu-item--danger i { color: #b91c1c; }
  .lm-user-menu-item--danger:hover { background: #fef2f2; }

  /* ===== Hero ===== */
  .lm-hero {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    min-height: 150px;
    padding: 1.5rem 1.65rem;
    border-radius: 22px;
    border: 1px solid #dbeafe;
    overflow: hidden;
    background:
      linear-gradient(90deg, rgba(238, 245, 255, .97) 0%, rgba(238, 245, 255, .92) 34%, rgba(238, 245, 255, .38) 60%, rgba(238, 245, 255, 0) 88%),
      url('{{ asset('assets/img/gedung-sate-drive.png') }}') right 42% / cover no-repeat;
    box-shadow: 0 14px 30px rgba(13, 43, 82, .08);
  }

  .lm-hero::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 5px;
    background: linear-gradient(90deg, #22c55e 0%, #84d94e 38%, #2f80ed 100%);
    opacity: .9;
  }

  .lm-hero-content { position: relative; z-index: 2; flex: 1 1 auto; min-width: 0; }

  .lm-eyebrow {
    margin-bottom: .3rem;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .14em;
    color: #1d6fe0;
  }

  .lm-hero h2 { margin: 0; font-size: 1.85rem; font-weight: 800; letter-spacing: -.03em; color: #0b2545; }

  .lm-hero-actions {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
    flex: 0 0 auto;
  }

  .lm-hero-actions .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .68rem 1.05rem;
    border-radius: 12px;
    font-size: .88rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }

  .lm-hero-actions .btn-primary { background: #0b4fd6; color: #ffffff; border: 1px solid transparent; }
  .lm-hero-actions .btn-primary:hover { background: #093fa9; }
  .lm-hero-actions .btn:not(.btn-primary) { background: #ffffff; color: #0b4fd6; border-color: #cfe0f7; box-shadow: 0 8px 18px rgba(15, 23, 42, .1); }
  .lm-hero-actions .btn:not(.btn-primary):hover { background: #f6faff; }


  /* ===== Kartu statistik ===== */
  .lm-status-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .85rem; }

  .lm-status-card {
    display: flex;
    align-items: center;
    gap: .8rem;
    padding: .95rem 1rem;
    border-radius: 18px;
    border: 1px solid transparent;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .05);
  }

  .lm-status-icon {
    flex: 0 0 auto;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    background: #ffffff;
    box-shadow: 0 4px 10px rgba(15, 23, 42, .08);
  }

  .lm-status-label { font-size: .82rem; font-weight: 700; color: #334155; }
  .lm-status-value { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; color: #0f172a; line-height: 1.15; }

  .lm-status-card--blue { background: #eff6ff; border-color: #dbeafe; }
  .lm-status-card--blue .lm-status-icon { color: #2563eb; }
  .lm-status-card--amber { background: #fffbeb; border-color: #fef3c7; }
  .lm-status-card--amber .lm-status-icon { color: #d97706; }
  .lm-status-card--green { background: #f0fdf4; border-color: #dcfce7; }
  .lm-status-card--green .lm-status-icon { color: #16a34a; }
  .lm-status-card--purple { background: #faf5ff; border-color: #ede9fe; }
  .lm-status-card--purple .lm-status-icon { color: #a855f7; }


  /* ===== Kartu konten ===== */
  .lm-card {
    background: #ffffff;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
    min-width: 0;
  }

  .lm-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: .85rem;
  }

  .lm-card-head h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0b2545;
    letter-spacing: -.01em;
  }

  .lm-card-head h3 i { color: #1565c0; font-size: 1rem; }

  /* ===== Toolbar (filter periode & status) ===== */
  .lm-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: .55rem;
    align-items: center;
    padding: .7rem;
    margin-bottom: .85rem;
    background: #f7faff;
    border: 1px solid #e8f0fb;
    border-radius: 14px;
  }

  .lm-field { display: flex; align-items: center; gap: .35rem; }
  .lm-toolbar label { margin: 0; font-size: .74rem; font-weight: 700; color: #64748b; white-space: nowrap; }

  .lm-toolbar select,
  .lm-toolbar input {
    padding: .45rem .6rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .8rem;
    outline: none;
  }

  .lm-toolbar select:focus,
  .lm-toolbar input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .14); }

  .lm-toolbar input[type="text"] { min-width: 180px; flex: 1; }

  .lm-toolbar button {
    padding: .45rem 1rem;
    border-radius: 10px;
    font-size: .8rem;
    font-weight: 700;
  }

  .lm-toolbar button { border: none; background: #0b4fd6; color: #ffffff; cursor: pointer; }
  .lm-toolbar button:hover { filter: brightness(1.05); }

  .lm-toolbar a.btn-primary { text-decoration: none; }

  .period-btn { padding: .45rem .9rem; font-size: .85rem; background: #fff; color: #334155; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; font-weight: 600; }
  .period-btn:hover { background: #f8fafc; }
  .period-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }

  /* ===== Tabel ===== */
  .lm-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border: 1px solid #e8f0fb; border-radius: 14px; }
  .lm-table { width: 100%; border-collapse: separate; border-spacing: 0; }

  .lm-table thead th {
    text-align: left;
    padding: .68rem .8rem;
    background: #f7faff;
    border-bottom: 1px solid #e8f0fb;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: #64748b;
    white-space: nowrap;
  }

  .lm-table tbody td {
    padding: .68rem .8rem;
    border-bottom: 1px solid #eef4fc;
    font-size: .85rem;
    color: #1f3a5f;
    vertical-align: middle;
    word-break: break-word;
  }

  .lm-table tbody tr:last-child td { border-bottom: none; }
  .lm-table tbody tr:hover td { background: #f9fbff; }

  .badge { display: inline-block; padding: .24rem .6rem; border-radius: 999px; font-size: .72rem; font-weight: 800; white-space: nowrap; }

  .empty-msg { text-align: center; padding: 2rem; color: #94a3b8; font-size: .86rem; }

  .lm-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-top: .9rem;
    font-size: .8rem;
    color: #64748b;
  }

  .lm-pagination button {
    padding: .42rem .8rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .78rem;
    font-weight: 700;
    cursor: pointer;
  }

  .lm-pagination button:hover:not(:disabled) { background: #f2f7ff; border-color: #bcd5f5; }
  .lm-pagination button:disabled { opacity: .45; cursor: not-allowed; }

  /* ===== Chart ===== */
  .lm-chart-box { position: relative; height: 280px; }
  .lm-chart-card {
    background: #ffffff;
    border: 1px solid #e8f0fb;
    border-radius: 14px;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
    min-width: 0;
  }

  .lm-chart-card h3 {
    margin: 0 0 .75rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0b2545;
    display: flex;
    align-items: center;
    gap: .45rem;
  }

  .lm-chart-card h3 i { color: #1565c0; font-size: 1rem; }

  /* ===== Usage bar ===== */
  .usage-bar { background: #e2e8f0; border-radius: 999px; height: 8px; overflow: hidden; min-width: 80px; }
  .usage-bar > span { display: block; height: 100%; background: #2563eb; border-radius: 999px; }


  /* ===== Responsive ===== */
  @media (max-width: 1200px) {
    .lm-shell { padding-left: 0; }
    .lm-shell > .dashboard-sidebar { display: none !important; }
    .lm-menu-btn { display: inline-flex; }
    .lm-status-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    body.mobile-sidebar-open .lm-shell > .dashboard-sidebar {
      display: flex !important;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: 270px;
      max-height: none;
      z-index: 120;
      overflow-y: auto;
      border-radius: 0;
    }
    body.mobile-sidebar-open::after {
      content: "";
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .5);
      z-index: 110;
    }
  }

  @media (max-width: 768px) {
    .lm-topbar { padding: .55rem .7rem; border-radius: 16px; }
    .lm-page-label span { display: none; }
    .lm-user { padding: .3rem; border: none; background: transparent; }
    .lm-user-meta, .lm-user > i { display: none; }
    .lm-hero { flex-direction: column; align-items: stretch; gap: .9rem; min-height: 0; padding: 1.15rem 1.15rem 1.4rem; border-radius: 18px; }
    .lm-hero h2 { font-size: 1.45rem; }
    .lm-hero-actions { width: 100%; justify-content: space-between; }
    .lm-hero-actions .btn { flex: 1 1 auto; }
    .lm-status-grid { grid-template-columns: minmax(0, 1fr); }
    .lm-status-value { font-size: 1.35rem; }
    .lm-chart-box { height: 220px; }
    .lm-toolbar { flex-direction: column; align-items: stretch; }
    .lm-field { width: 100%; }
    .lm-toolbar input[type="text"] { min-width: 0; }
    body.mobile-sidebar-open .lm-shell > .dashboard-sidebar {
      display: flex !important;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: 270px;
      max-height: none;
      z-index: 120;
      overflow-y: auto;
      border-radius: 0;
    }
    body.mobile-sidebar-open::after {
      content: "";
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .5);
      z-index: 110;
    }
  }
</style>


@php
  $period = request('period', 'all');
  $rangeStart = $rangeStart ?? null;
  $rangeEnd = $rangeEnd ?? null;
@endphp

<div class="lm-shell">
  @include('partials.admin-sidebar')

  <main class="lm-main">
    <div class="lm-topbar">
      <div class="lm-topbar-left">
        <button class="lm-menu-btn" id="lmMenuBtn" type="button" aria-label="Buka menu">
          <i class="bi bi-list"></i>
        </button>
        <div class="lm-page-label"><i class="bi bi-car-front"></i><span>Laporan Mobil</span></div>
      </div>
      <div class="lm-topbar-right">
        <a class="lm-icon-btn" href="{{ route('dashboard') }}" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
          <i class="bi bi-house-door"></i>
        </a>
        <a class="lm-icon-btn" href="{{ route('dashboard.calendar') }}" title="Kalender booking" aria-label="Kalender booking">
          <i class="bi bi-calendar3"></i>
        </a>
        <div class="lm-user-wrap" id="lmUserWrap">
          <button class="lm-user" id="lmUserBtn" type="button" title="{{ auth()->user()->name ?? 'Pengguna' }}" aria-haspopup="true" aria-expanded="false" aria-controls="lmUserMenu">
            <div class="lm-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div class="lm-user-meta">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ str_replace('_', ' ', auth()->user()->role ?? 'user') }}</span>
            </div>
            <i class="bi bi-chevron-down"></i>
          </button>
          <div class="lm-user-menu" id="lmUserMenu" role="menu" aria-labelledby="lmUserBtn" hidden>
            <div class="lm-user-menu-head">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ auth()->user()->email ?? '' }}</span>
            </div>
            <a class="lm-user-menu-item" href="{{ route('accounts.show', auth()->id()) }}" role="menuitem">
              <i class="bi bi-person"></i> Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="lm-user-menu-item lm-user-menu-item--danger" role="menuitem">
                <i class="bi bi-box-arrow-right"></i> Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <section class="lm-hero">
      <div class="lm-hero-content">
        <div class="lm-eyebrow">LAPORAN MOBIL</div>
        <h2>Laporan Mobil</h2>
      </div>
      <div class="lm-hero-actions">
        <a class="lm-btn lm-btn-primary" href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Kembali ke Dashboard</a>
        <a class="lm-btn" href="{{ route('dashboard.calendar') }}"><i class="bi bi-calendar3"></i> Kalender</a>
      </div>
    </section>

    <div class="lm-status-grid">
      <div class="lm-status-card lm-status-card--blue">
        <div class="lm-status-icon"><i class="bi bi-car-front"></i></div>
        <div>
          <div class="lm-status-label">Total Penggunaan</div>
          <div class="lm-status-value">{{ $totalBookings }} booking</div>
        </div>
      </div>
      <div class="lm-status-card lm-status-card--green">
        <div class="lm-status-icon"><i class="bi bi-calendar-week"></i></div>
        <div>
          <div class="lm-status-label">Total Hari Pakai</div>
          <div class="lm-status-value">{{ $totalDays }} hari</div>
        </div>
      </div>
      <div class="lm-status-card lm-status-card--amber">
        <div class="lm-status-icon"><i class="bi bi-people"></i></div>
        <div>
          <div class="lm-status-label">Mobil Digunakan</div>
          <div class="lm-status-value">{{ $vehiclesUsed }} / {{ $totalVehicles }} mobil</div>
        </div>
      </div>
      <div class="lm-status-card lm-status-card--purple">
        <div class="lm-status-icon"><i class="bi bi-bar-chart"></i></div>
        <div>
          <div class="lm-status-label">Rata-rata / Mobil</div>
          <div class="lm-status-value">{{ $vehiclesUsed > 0 ? round($totalBookings / $vehiclesUsed, 1) : 0 }} booking</div>
        </div>
      </div>
    </div>

    <div class="lm-card">
      <div class="lm-card-head">
        <h3><i class="bi bi-funnel"></i>Filter</h3>
      </div>
      <form method="GET" action="{{ route('laporan-mobil.index') }}" id="filterForm" class="lm-toolbar">
        <div class="lm-field">
          <label>Periode</label>
          <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
            <button type="submit" name="period" value="all" class="period-btn {{ $period === 'all' ? 'active' : '' }}">Semua</button>
            <button type="submit" name="period" value="day" class="period-btn {{ $period === 'day' ? 'active' : '' }}">Hari</button>
            <button type="submit" name="period" value="week" class="period-btn {{ $period === 'week' ? 'active' : '' }}">Minggu</button>
            <button type="submit" name="period" value="month" class="period-btn {{ $period === 'month' ? 'active' : '' }}">Bulan</button>
            <button type="submit" name="period" value="year" class="period-btn {{ $period === 'year' ? 'active' : '' }}">Tahun</button>
          </div>
        </div>
        <div class="lm-field">
          <label for="start_date">Dari Tanggal</label>
          <input id="start_date" name="start_date" type="date" value="{{ request('start_date', $rangeStart?->format('Y-m-d')) }}" {{ $period !== 'custom' ? 'disabled' : '' }}>
        </div>
        <div class="lm-field">
          <label for="end_date">Sampai Tanggal</label>
          <input id="end_date" name="end_date" type="date" value="{{ request('end_date', $rangeEnd?->format('Y-m-d')) }}" {{ $period !== 'custom' ? 'disabled' : '' }}>
        </div>
        <button type="submit" name="period" value="custom" class="period-btn {{ $period === 'custom' ? 'active' : '' }}">Rentang Kustom</button>
        <div class="lm-field">
          <label for="status_filter">Status</label>
          <select id="status_filter" name="status_filter">
            <option value="" {{ $statusFilter === '' ? 'selected' : '' }}>Semua (tanpa cancel)</option>
            @foreach($statuses as $value => $label)
              <option value="{{ $value }}" {{ $statusFilter === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" name="period" value="{{ $period }}">Terapkan</button>
        @if(request()->hasAny(['period', 'start_date', 'end_date', 'status_filter']))
          <a href="{{ route('laporan-mobil.index') }}">Reset</a>
        @endif
      </form>

      <div class="lm-card-head" style="margin-top:1rem; justify-content:space-between;">
        <h3 style="margin:0;"><i class="bi bi-car-front"></i>Rentang: <strong style="color:#0f172a;">{{ $rangeLabel }}</strong>@if($rangeDays) &middot; {{ $rangeDays }} hari@endif &middot; booking belum dihapus &middot; status Booking Cancel tidak dihitung@if($statusFilter !== '') &middot; difilter: {{ $statuses[$statusFilter] ?? $statusFilter }}@endif</h3>
      </div>


      <div class="lm-table-wrap">
        <table class="lm-table">
          <thead>
            <tr>
              <th style="width:40px;">No</th>
              <th>Nama Mobil</th>
              <th style="text-align:right;">Jumlah Booking</th>
              <th style="text-align:right;">Total Hari Pakai</th>
              <th style="min-width:150px;">Utilisasi</th>
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

    </div>

    @if($chartData->isNotEmpty())
    <div class="lm-chart-card">
      <div class="lm-card-head">
        <h3><i class="bi bi-bar-chart"></i>10 Mobil Paling Banyak Digunakan</h3>
      </div>
      <div class="lm-chart-box">
        <canvas id="vehicleUsageChart"></canvas>
      </div>
    </div>
    @endif
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
  const wrap = document.getElementById('lmUserWrap');
  const btn = document.getElementById('lmUserBtn');
  const menu = document.getElementById('lmUserMenu');
  if (wrap && btn && menu) {
    const setOpen = (open) => {
      menu.hidden = !open;
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    btn.addEventListener('click', (e) => { e.stopPropagation(); setOpen(menu.hidden); });
    document.addEventListener('click', (e) => { if (!menu.hidden && !wrap.contains(e.target)) setOpen(false); });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !menu.hidden) { setOpen(false); btn.focus(); }
    });
  }

  const menuBtn = document.getElementById('lmMenuBtn');
  if (menuBtn) {
    menuBtn.addEventListener('click', () => document.body.classList.toggle('mobile-sidebar-open'));
    document.addEventListener('click', (e) => {
      if (!document.body.classList.contains('mobile-sidebar-open')) return;
      const sidebar = document.querySelector('.lm-shell > .dashboard-sidebar');
      if (sidebar && !sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
        document.body.classList.remove('mobile-sidebar-open');
      }
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') document.body.classList.remove('mobile-sidebar-open');
    });
  }
})();

(function(){
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
