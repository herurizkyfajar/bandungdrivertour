@extends('layouts.app', ['title' => 'Manage Vehicles'])

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
  .vm-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 1.25rem;
    align-items: start;
    min-height: calc(100vh - 6rem);
    padding-left: 252px;
  }
  .vm-main {
    display: grid;
    gap: 1.05rem;
    align-content: start;
    min-width: 0;
    min-height: 0;
    padding: 0 0 1rem;
  }

  /* ===== Sidebar (sama dengan dashboard) ===== */
  .vm-shell > .dashboard-sidebar {
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
  .vm-shell > .dashboard-sidebar::after {
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
  .vm-shell .dashboard-brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .45rem;
    padding: 0 .3rem 1rem;
    margin-bottom: .75rem;
    border-bottom: 1px solid rgba(255, 255, 255, .18);
  }
  .vm-shell .dashboard-brand img {
    width: 142px;
    height: auto;
    padding: .3rem .45rem;
    border-radius: 10px;
    background: #ffffff;
    object-fit: contain;
  }
  .vm-shell .dashboard-brand h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #ffffff;
  }
  .vm-shell .dashboard-brand .subtitle {
    font-size: .82rem;
    color: rgba(255, 255, 255, .82);
  }
  .vm-shell .dashboard-section-label {
    margin: .85rem .3rem .4rem;
    padding: 0;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .6);
  }
  .vm-shell .dashboard-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: .12rem;
  }
  .vm-shell .dashboard-menu a,
  .vm-shell .dashboard-menu button {
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
  .vm-shell .dashboard-menu a:hover,
  .vm-shell .dashboard-menu button:hover {
    background: rgba(255, 255, 255, .12);
    color: #ffffff;
  }
  .vm-shell .dashboard-menu a.active {
    background: #ffffff;
    color: #0b4fd6;
    box-shadow: 0 6px 14px rgba(4, 33, 90, .22);
  }
  .vm-shell .dashboard-menu .nav-icon {
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
  .vm-shell .dashboard-menu a.active .nav-icon {
    background: #e7f0ff;
    color: #0b4fd6;
  }

  /* ===== Topbar ===== */
  .vm-topbar {
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
  .vm-topbar-left { display: flex; align-items: center; gap: .6rem; min-width: 0; }
  .vm-topbar-right { display: flex; align-items: center; gap: .45rem; flex: 0 0 auto; }
  .vm-menu-btn {
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
  .vm-page-label {
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
  .vm-page-label i { color: #1565c0; font-size: .95rem; }
  .vm-icon-btn {
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
  .vm-icon-btn:hover { background: #f4f9ff; border-color: #bcd5f5; color: #0b4fd6; }
  .vm-user-wrap { position: relative; }
  .vm-user {
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
  .vm-user:hover { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }
  .vm-user[aria-expanded="true"] { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }
  .vm-avatar {
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
  .vm-user-meta { display: flex; flex-direction: column; line-height: 1.15; min-width: 0; overflow: hidden; }
  .vm-user-meta strong { max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .82rem; font-weight: 800; color: #0b2545; }
  .vm-user-meta span { font-size: .7rem; color: #64748b; text-transform: capitalize; }
  .vm-user > i { color: #94a3b8; font-size: .75rem; transition: transform .15s ease; }
  .vm-user[aria-expanded="true"] > i { transform: rotate(180deg); }
  .vm-user-menu {
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
  .vm-user-menu[hidden] { display: none; }
  .vm-user-menu-head {
    display: flex;
    flex-direction: column;
    gap: .15rem;
    padding: .55rem .7rem .6rem;
    margin-bottom: .35rem;
    border-bottom: 1px solid #eef2f8;
  }
  .vm-user-menu-head strong { font-size: .82rem; font-weight: 800; color: #0b2545; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .vm-user-menu-head span { font-size: .72rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .vm-user-menu-item {
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
  .vm-user-menu-item i { font-size: .95rem; color: #64748b; }
  .vm-user-menu-item:hover { background: #f1f5fb; }
  .vm-user-menu-item--danger { color: #b91c1c; }
  .vm-user-menu-item--danger i { color: #b91c1c; }
  .vm-user-menu-item--danger:hover { background: #fef2f2; }

  /* ===== Hero ===== */
  .vm-hero {
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
  .vm-hero::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 5px;
    background: linear-gradient(90deg, #22c55e 0%, #84d94e 38%, #2f80ed 100%);
    opacity: .9;
  }
  .vm-hero-content { position: relative; z-index: 2; flex: 1 1 auto; min-width: 0; }
  .vm-eyebrow {
    margin-bottom: .3rem;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .14em;
    color: #1d6fe0;
  }
  .vm-hero h2 { margin: 0; font-size: 1.85rem; font-weight: 800; letter-spacing: -.03em; color: #0b2545; }
  .vm-hero-actions {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
    flex: 0 0 auto;
  }
  .vm-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .68rem 1.05rem;
    border-radius: 12px;
    border: 1px solid transparent;
    font-size: .88rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .vm-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(11, 61, 145, .22); }
  .vm-btn-primary { background: #0b4fd6; color: #ffffff; border: 1px solid transparent; }
  .vm-btn-primary:hover { background: #093fa9; }
  .vm-btn:not(.vm-btn-primary) { background: #ffffff; color: #0b4fd6; border-color: #cfe0f7; box-shadow: 0 8px 18px rgba(15, 23, 42, .1); }
  .vm-btn:not(.vm-btn-primary):hover { background: #f6faff; }
  .vm-btn--danger { background: #dc2626; color: #ffffff; border: 1px solid transparent; }
  .vm-btn--danger:hover { background: #b91c1c; }

  /* ===== Kartu statistik ===== */
  .vm-status-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .85rem; }
  .vm-status-card {
    display: flex;
    align-items: center;
    gap: .8rem;
    padding: .95rem 1rem;
    border-radius: 18px;
    border: 1px solid transparent;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .05);
  }
  .vm-status-icon {
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
  .vm-status-label { font-size: .82rem; font-weight: 700; color: #334155; }
  .vm-status-value { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; color: #0f172a; line-height: 1.15; }
  .vm-status-card--blue { background: #eff6ff; border-color: #dbeafe; }
  .vm-status-card--blue .vm-status-icon { color: #2563eb; }
  .vm-status-card--amber { background: #fffbeb; border-color: #fef3c7; }
  .vm-status-card--amber .vm-status-icon { color: #d97706; }
  .vm-status-card--green { background: #f0fdf4; border-color: #dcfce7; }
  .vm-status-card--green .vm-status-icon { color: #16a34a; }
  .vm-status-card--purple { background: #faf5ff; border-color: #ede9fe; }
  .vm-status-card--purple .vm-status-icon { color: #a855f7; }

  /* ===== Kartu konten ===== */
  .vm-card {
    background: #ffffff;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
    min-width: 0;
  }
  .vm-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: .85rem;
  }
  .vm-card-head h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0b2545;
    letter-spacing: -.01em;
  }
  .vm-card-head h3 i { color: #1565c0; font-size: 1rem; }

  /* ===== Tabel ===== */
  .vm-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border: 1px solid #e8f0fb; border-radius: 14px; }
  .vm-table { width: 100%; border-collapse: separate; border-spacing: 0; }
  .vm-table thead th {
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
  .vm-table tbody td {
    padding: .68rem .8rem;
    border-bottom: 1px solid #eef4fc;
    font-size: .85rem;
    color: #1f3a5f;
    vertical-align: middle;
    word-break: break-word;
  }
  .vm-table tbody tr:last-child td { border-bottom: none; }
  .vm-table tbody tr:hover td { background: #f9fbff; }
  .vm-badge { display: inline-block; padding: .24rem .6rem; border-radius: 999px; font-size: .72rem; font-weight: 800; white-space: nowrap; }
  .vm-empty-msg { text-align: center; padding: 2rem; color: #94a3b8; font-size: .86rem; }

  /* ===== Sort mode ===== */
  .vm-sort-badge { display: inline-flex; align-items: center; gap: .3rem; background: #e0e7ff; color: #2563eb; font-size: .75rem; font-weight: 600; padding: .2rem .6rem; border-radius: 999px; }
  .vm-mobile-card { background: #fff; border: 1px solid #e8f0fb; border-radius: 12px; padding: 1rem; margin-bottom: .75rem; display: flex; gap: .75rem; align-items: flex-start; }
  .vm-mobile-card-avatar { width: 48px; height: 48px; border-radius: 10px; object-fit: cover; border: 1px solid #e8f0fb; flex-shrink: 0; }
  .vm-mobile-card-body { flex: 1; min-width: 0; }
  .vm-mobile-card-title { font-weight: 700; color: #0f172a; font-size: .95rem; margin-bottom: .25rem; }
  .vm-mobile-card-meta { font-size: .85rem; color: #64748b; margin-bottom: .5rem; }
  .vm-mobile-card-actions { display: flex; gap: .35rem; flex-wrap: wrap; }
  .vm-drag-handle { cursor: grab; color: #64748b; font-size: 1.2rem; user-select: none; padding: 0 .25rem; }
  .vm-drag-handle:active { cursor: grabbing; }
  .vm-sortable-ghost { opacity: 0.4; background: #e0e7ff; }
  .vm-sortable-chosen { box-shadow: 0 4px 12px rgba(37,99,235,0.25); }
  .vm-sort-save-toast { position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%); background: #059669; color: #fff; padding: .6rem 1.2rem; border-radius: 10px; font-size: .85rem; font-weight: 600; z-index: 9999; opacity: 0; transition: opacity .3s; pointer-events: none; }
  .vm-sort-save-toast.show { opacity: 1; }

  /* ===== Pagination ===== */
  .vm-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-top: .9rem;
    font-size: .8rem;
    color: #64748b;
  }

  /* ===== Responsive ===== */
  @media (max-width: 1200px) {
    .vm-shell { padding-left: 0; }
    .vm-shell > .dashboard-sidebar { display: none !important; }
    .vm-menu-btn { display: inline-flex; }
    .vm-status-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    body.mobile-sidebar-open .vm-shell > .dashboard-sidebar {
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
    .vm-topbar { padding: .55rem .7rem; border-radius: 16px; }
    .vm-page-label span { display: none; }
    .vm-user { padding: .3rem; border: none; background: transparent; }
    .vm-user-meta, .vm-user > i { display: none; }
    .vm-hero { flex-direction: column; align-items: stretch; gap: .9rem; min-height: 0; padding: 1.15rem 1.15rem 1.4rem; border-radius: 18px; }
    .vm-hero h2 { font-size: 1.45rem; }
    .vm-hero-actions { width: 100%; justify-content: space-between; }
    .vm-hero-actions .vm-btn { flex: 1 1 auto; }
    .vm-status-grid { grid-template-columns: minmax(0, 1fr); }
    .vm-status-value { font-size: 1.35rem; }
    body.mobile-sidebar-open .vm-shell > .dashboard-sidebar {
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
  $totalVehicles = $vehicles->total();
  $withPhoto = $vehicles->getCollection()->filter(fn ($v) => $v->photo_path)->count();
  $withoutPhoto = $totalVehicles - $withPhoto;
  $relatedMitras = $vehicles->getCollection()->flatMap(fn ($v) => $v->mitras)->unique('id')->count();
@endphp

<div class="vm-sort-save-toast" id="vmSortToast">Urutan berhasil disimpan!</div>

<div class="vm-shell">
  @include('partials.admin-sidebar')

  <main class="vm-main">
    <div class="vm-topbar">
      <div class="vm-topbar-left">
        <button class="vm-menu-btn" id="vmMenuBtn" type="button" aria-label="Buka menu"><i class="bi bi-list"></i></button>
        <div class="vm-page-label"><i class="bi bi-car-front"></i><span>Manage Vehicles</span></div>
      </div>
      <div class="vm-topbar-right">
        <a class="vm-icon-btn" href="{{ route('dashboard') }}" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard"><i class="bi bi-house-door"></i></a>
        <a class="vm-icon-btn" href="{{ route('dashboard.calendar') }}" title="Kalender booking" aria-label="Kalender booking"><i class="bi bi-calendar3"></i></a>
        <div class="vm-user-wrap" id="vmUserWrap">
          <button class="vm-user" id="vmUserBtn" type="button" title="{{ auth()->user()->name ?? 'Pengguna' }}" aria-haspopup="true" aria-expanded="false" aria-controls="vmUserMenu">
            <div class="vm-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div class="vm-user-meta">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ str_replace('_', ' ', auth()->user()->role ?? 'user') }}</span>
            </div>
            <i class="bi bi-chevron-down"></i>
          </button>
          <div class="vm-user-menu" id="vmUserMenu" role="menu" aria-labelledby="vmUserBtn" hidden>
            <div class="vm-user-menu-head">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ auth()->user()->email ?? '' }}</span>
            </div>
            <a class="vm-user-menu-item" href="{{ route('accounts.show', auth()->id()) }}" role="menuitem"><i class="bi bi-person"></i> Profil</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="vm-user-menu-item vm-user-menu-item--danger" role="menuitem"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <section class="vm-hero">
      <div class="vm-hero-content">
        <div class="vm-eyebrow">KENDARAAN</div>
        <h2>Manage Vehicles</h2>
      </div>
      <div class="vm-hero-actions">
        <a class="vm-btn vm-btn-primary" href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Kembali ke Dashboard</a>
        <a class="vm-btn" href="{{ route('dashboard.calendar') }}"><i class="bi bi-calendar3"></i> Kalender</a>
        <a class="vm-btn vm-btn-primary" href="{{ route('vehicles.create') }}"><i class="bi bi-plus-lg"></i> Tambah Kendaraan</a>
      </div>
    </section>

    <div class="vm-status-grid">
      <div class="vm-status-card vm-status-card--blue">
        <div class="vm-status-icon"><i class="bi bi-car-front"></i></div>
        <div>
          <div class="vm-status-label">Total Kendaraan</div>
          <div class="vm-status-value">{{ $totalVehicles }} mobil</div>
        </div>
      </div>
      <div class="vm-status-card vm-status-card--green">
        <div class="vm-status-icon"><i class="bi bi-image"></i></div>
        <div>
          <div class="vm-status-label">Dengan Foto</div>
          <div class="vm-status-value">{{ $withPhoto }} mobil</div>
        </div>
      </div>
      <div class="vm-status-card vm-status-card--amber">
        <div class="vm-status-icon"><i class="bi bi-image-off"></i></div>
        <div>
          <div class="vm-status-label">Tanpa Foto</div>
          <div class="vm-status-value">{{ $withoutPhoto }} mobil</div>
        </div>
      </div>
      <div class="vm-status-card vm-status-card--purple">
        <div class="vm-status-icon"><i class="bi bi-people"></i></div>
        <div>
          <div class="vm-status-label">Mitra Terkait</div>
          <div class="vm-status-value">{{ $relatedMitras }} mitra</div>
        </div>
      </div>
    </div>

    <div class="vm-card">
      <div class="vm-card-head">
        <h3><i class="bi bi-list"></i>Daftar Kendaraan</h3>
        <div style="display:flex; gap:.5rem; align-items:center;">
          <button class="vm-btn" id="vmBtnToggleSort" onclick="toggleSortMode()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h12M3 18h6"/></svg>
            Urutkan
          </button>
          <a class="vm-btn vm-btn-primary" href="{{ route('vehicles.create') }}">Add Vehicle</a>
        </div>
      </div>

      {{-- Normal mode: paginated table --}}
      <div id="vmNormalView">
        <div class="vm-table-wrap" style="margin-top:.75rem;">
          <table class="vm-table" style="width:100%; border-collapse:separate; border-spacing:0;">
            <thead>
              <tr>
                <th style="width:40px;">#</th>
                <th>Photo</th>
                <th>Plate Number</th>
                <th>Make</th>
                <th>Model</th>
                <th>Color</th>
                <th>Owner</th>
                <th style="min-width:140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($vehicles as $i => $v)
              <tr>
                <td style="padding:.6rem .5rem;">{{ ($vehicles->currentPage()-1)*$vehicles->perPage()+$i+1 }}</td>
                <td style="padding:.6rem .5rem;">
                  @if($v->photo_path)
                    <img class="vm-avatar" src="{{ asset('storage/'.$v->photo_path) }}" alt="{{ $v->plate_number }}">
                  @else
                    -
                  @endif
                </td>
                <td style="padding:.6rem .5rem; font-weight:600;">{{ $v->plate_number }}</td>
                <td style="padding:.6rem .5rem;">{{ $v->make }}</td>
                <td style="padding:.6rem .5rem;">{{ $v->model }}</td>
                <td style="padding:.6rem .5rem;">{{ $v->color }}</td>
                <td style="padding:.6rem .5rem;">{{ $v->mitras->pluck('full_name')->implode(', ') ?: '-' }}</td>
                <td style="padding:.6rem .5rem;">
                  <div style="display:flex; gap:.35rem; flex-wrap:wrap;">
                    <a class="vm-btn" href="{{ route('vehicles.edit', $v) }}">Edit</a>
                    <form method="POST" action="{{ route('vehicles.destroy', $v) }}">
                      @csrf
                      @method('DELETE')
                      <button class="vm-btn vm-btn--danger" type="submit" onclick="return confirm('Delete this vehicle?')">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8"><div class="vm-empty-msg">No vehicles yet.</div></td>
              </tr>
              @endforelse
            </tbody>
          </table>
          <div style="margin-top:.75rem;" class="vm-pagination">
            {{ $vehicles->links() }}
          </div>
        </div>
      </div>

      {{-- Sort mode: draggable list --}}
      <div id="vmSortView" style="display:none; margin-top:.75rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem;">
          <span class="vm-sort-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h12M3 18h6"/></svg>
            Mode Pengurutan Aktif
          </span>
          <div style="display:flex; gap:.5rem;">
            <button class="vm-btn vm-btn-primary" id="vmBtnSaveSort" onclick="saveSortOrder()" disabled>Simpan Urutan</button>
            <button class="vm-btn" onclick="toggleSortMode()">Batal</button>
          </div>
        </div>
        <div id="vmSortableList" style="display:flex; flex-direction:column; gap:.5rem;"></div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
let sortMode = false;
let sortableInstance = null;
let allVehicles = [];

function toggleSortMode() {
  sortMode = !sortMode;
  document.getElementById('vmNormalView').style.display = sortMode ? 'none' : 'block';
  document.getElementById('vmSortView').style.display = sortMode ? 'block' : 'none';
  document.getElementById('vmBtnToggleSort').innerHTML = sortMode
    ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg> Tutup'
    : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h12M3 18h6"/></svg> Urutkan';

  if (sortMode) {
    loadAllVehicles();
  } else if (sortableInstance) {
    sortableInstance.destroy();
    sortableInstance = null;
  }
}

function loadAllVehicles() {
  const list = document.getElementById('vmSortableList');
  list.innerHTML = '<div style="text-align:center; padding:1.5rem; color:#64748b;">Memuat data...</div>';

  fetch('{{ route("vehicles.all") }}')
    .then(r => r.json())
    .then(vehicles => {
      allVehicles = vehicles;
      renderSortableList(vehicles);
    })
    .catch(() => {
      list.innerHTML = '<div style="text-align:center; padding:1.5rem; color:#dc2626;">Gagal memuat data.</div>';
    });
}

function renderSortableList(vehicles) {
  const list = document.getElementById('vmSortableList');
  if (!vehicles.length) {
    list.innerHTML = '<div style="text-align:center; padding:2rem; color:#64748b;">Tidak ada kendaraan.</div>';
    return;
  }

  list.innerHTML = vehicles.map((v, i) => `
    <div class="vm-mobile-card" data-id="${v.id}" style="margin-bottom:0; border:1px solid #e8f0fb; border-radius:10px;">
      <span class="vm-drag-handle" title="Drag untuk mengurutkan">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="5" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="19" r="1"/></svg>
      </span>
      <span style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:#f1f5f9; color:#64748b; font-size:.8rem; font-weight:700; flex-shrink:0;">${i + 1}</span>
      ${v.photo_path ? `<img class="vm-mobile-card-avatar" src="/storage/${v.photo_path}" alt="${v.plate_number}">` : ''}
      <div class="vm-mobile-card-body">
        <div class="vm-mobile-card-title">${v.plate_number}</div>
        <div class="vm-mobile-card-meta">${v.make || ''} ${v.model || ''} &middot; ${v.color || ''} &middot; ${v.mitras && v.mitras.length ? v.mitras.map(m => m.full_name).join(', ') : '-'}</div>
      </div>
    </div>
  `).join('');

  if (sortableInstance) sortableInstance.destroy();
  sortableInstance = new Sortable(list, {
    handle: '.vm-drag-handle',
    animation: 200,
    ghostClass: 'vm-sortable-ghost',
    chosenClass: 'vm-sortable-chosen',
    onEnd: function() {
      document.getElementById('vmBtnSaveSort').disabled = false;
      updateSortNumbers();
    }
  });

  document.getElementById('vmBtnSaveSort').disabled = true;
}

function updateSortNumbers() {
  const items = document.querySelectorAll('#vmSortableList .vm-mobile-card');
  items.forEach((item, i) => {
    const badge = item.querySelector('span:not(.vm-drag-handle):not(img)');
    if (badge && !badge.classList.contains('vm-mobile-card-avatar')) {
      badge.textContent = i + 1;
    }
  });
}

function saveSortOrder() {
  const btn = document.getElementById('vmBtnSaveSort');
  const items = document.querySelectorAll('#vmSortableList .vm-mobile-card');
  const ids = Array.from(items).map(item => parseInt(item.dataset.id));

  btn.disabled = true;
  btn.textContent = 'Menyimpan...';

  fetch('{{ route("vehicles.reorder") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ ids: ids })
  })
  .then(r => r.json())
  .then(data => {
    btn.textContent = 'Simpan Urutan';
    if (data.success) {
      const toast = document.getElementById('vmSortToast');
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 2500);
    }
  })
  .catch(() => {
    btn.disabled = false;
    btn.textContent = 'Simpan Urutan';
    alert('Gagal menyimpan urutan. Coba lagi.');
  });
}
</script>

<script>
(function () {
  const wrap = document.getElementById('vmUserWrap');
  const btn = document.getElementById('vmUserBtn');
  const menu = document.getElementById('vmUserMenu');
  if (!wrap || !btn || !menu) return;
  const setOpen = (open) => {
    menu.hidden = !open;
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  };
  btn.addEventListener('click', function (e) { e.stopPropagation(); setOpen(menu.hidden); });
  document.addEventListener('click', function (e) {
    if (!menu.hidden && !wrap.contains(e.target)) setOpen(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !menu.hidden) { setOpen(false); btn.focus(); }
  });
})();

(function () {
  const btn = document.getElementById('vmMenuBtn');
  if (!btn) return;
  btn.addEventListener('click', function () {
    document.body.classList.toggle('mobile-sidebar-open');
  });
  document.addEventListener('click', function (e) {
    if (!document.body.classList.contains('mobile-sidebar-open')) return;
    const sidebar = document.querySelector('.vm-shell > .dashboard-sidebar');
    if (sidebar && !sidebar.contains(e.target) && !btn.contains(e.target)) {
      document.body.classList.remove('mobile-sidebar-open');
    }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') document.body.classList.remove('mobile-sidebar-open');
  });
})();
</script>
@endsection
