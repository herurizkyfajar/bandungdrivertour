@extends('layouts.app', ['title' => 'Edit SPJ Manual'])

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

  /* ===== Flash & actions ===== */
  .vm-flash { padding: .75rem 1rem; border-radius: 10px; margin-bottom: 1rem; font-weight: 600; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
  .vm-actions { display: flex; gap: .5rem; margin-top: 1rem; }

  /* ===== Form ===== */
  .vm-form-field { margin-bottom: .85rem; }
  .vm-form-field label { display: block; font-size: .82rem; font-weight: 700; color: #334155; margin-bottom: .35rem; }
  .vm-form-field input,
  .vm-form-field select,
  .vm-form-field textarea {
    width: 100%;
    padding: .55rem .8rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .85rem;
  }
  .vm-form-field input:focus,
  .vm-form-field select:focus,
  .vm-form-field textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .14);
    outline: none;
  }
  .vm-form-field textarea { min-height: 90px; resize: vertical; }
  .vm-form-hint { font-size: .75rem; color: #64748b; margin-top: .25rem; display: block; }
  .vm-form-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 1rem; }
  .vm-form-col-4 { grid-column: span 4; }
  .vm-form-col-6 { grid-column: span 6; }
  .vm-form-col-12 { grid-column: span 12; }

  /* ===== Error ===== */
  .field-error { color: #b91c1c; font-size: .78rem; font-weight: 600; margin-top: .25rem; }
  .errors-box { padding:.75rem 1rem; border-radius:10px; margin-bottom:1rem; font-weight:600; background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; }
  .errors-box ul { margin:0; padding-left:1.25rem; }

  /* ===== SPJ search-select ===== */
  .search-select { position: relative; }
  .search-select .search-input { width: 100%; padding: .75rem .9rem; border: 1px solid #cbd5e1; border-radius: 12px; background: #fff; color: #0b2545; }
  .search-select .search-dropdown { position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 10; background: #fff; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,.08); max-height: 220px; overflow-y: auto; display: none; }
  .search-select.open .search-dropdown { display: block; }
  .search-select .search-item { padding: .5rem .75rem; cursor: pointer; font-size: .9rem; }
  .search-select .search-item:hover { background: #f8fafc; }
  .search-select .search-item.selected { background: #eff6ff; color: #2563eb; font-weight: 600; }
  .search-select .search-empty { padding: .6rem .75rem; color: #64748b; font-size: .85rem; }

  /* ===== Responsive ===== */
  @media (max-width: 1200px) {
    .vm-shell { padding-left: 0; }
    .vm-shell > .dashboard-sidebar { display: none !important; }
    .vm-menu-btn { display: inline-flex; }
    .vm-form-grid { grid-template-columns: repeat(4, 1fr); }
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
    .vm-form-grid { grid-template-columns: repeat(2, 1fr); }
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
  @media (max-width: 640px) {
    .vm-form-grid { grid-template-columns: 1fr; }
    .vm-form-col-4, .vm-form-col-6, .vm-form-col-12 { grid-column: span 12; }
  }
</style>

<div class="vm-shell">
  @include('partials.admin-sidebar')

  <main class="vm-main">
    <div class="vm-topbar">
      <div class="vm-topbar-left">
        <button class="vm-menu-btn" id="vmMenuBtn" type="button" aria-label="Buka menu"><i class="bi bi-list"></i></button>
        <div class="vm-page-label"><i class="bi bi-pencil-square"></i><span>Edit SPJ Manual</span></div>
      </div>
      <div class="vm-topbar-right">
        <a class="vm-icon-btn" href="{{ route('spj-manuals.index') }}" title="Kembali ke Daftar SPJ" aria-label="Kembali ke Daftar SPJ"><i class="bi bi-house-door"></i></a>
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
        <div class="vm-eyebrow">KELOLA SPJ MANUAL</div>
        <h2>Edit SPJ Manual</h2>
      </div>
      <div class="vm-hero-actions">
        <a class="vm-btn vm-btn-primary" href="{{ route('spj-manuals.index') }}"><i class="bi bi-house-door"></i> Kembali ke Daftar SPJ</a>
        <a class="vm-btn" href="{{ route('dashboard.calendar') }}"><i class="bi bi-calendar3"></i> Kalender</a>
      </div>
    </section>

    <div class="vm-card">
      <div class="vm-card-head">
        <h3><i class="bi bi-pencil-square"></i>Form Edit SPJ Manual</h3>
      </div>

    <form method="POST" action="{{ route('spj-manuals.update', $spjManual) }}">
      @csrf
      @method('PUT')
      <div class="vm-form-grid">
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="driver_name">Nama Pengemudi</label>
            <div class="search-select" id="driver_select">
              <input id="driver_search" class="search-input" type="text" placeholder="Ketik nama pengemudi..." autocomplete="off" value="{{ old('driver_name', $spjManual->driver_name) }}">
              <input type="hidden" name="driver_name" id="driver_name" value="{{ old('driver_name', $spjManual->driver_name) }}">
              <div class="search-dropdown" id="driver_dropdown"></div>
            </div>
          </div>
        </div>
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="customer_name">Nama Pelanggan</label>
            <input id="customer_name" type="text" name="customer_name" value="{{ old('customer_name', $spjManual->customer_name) }}" required>
          </div>
        </div>
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="customer_contact">Kontak Pelanggan</label>
            <input id="customer_contact" type="text" name="customer_contact" value="{{ old('customer_contact', $spjManual->customer_contact) }}">
          </div>
        </div>
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="country_of_origin">Negara Asal</label>
            <div class="search-select" id="country_select">
              <input id="country_search" class="search-input" type="text" placeholder="Ketik nama negara..." autocomplete="off" value="{{ old('country_of_origin', $spjManual->country_of_origin) }}">
              <input type="hidden" name="country_of_origin" id="country_of_origin_hidden" value="{{ old('country_of_origin', $spjManual->country_of_origin) }}">
              <div class="search-dropdown" id="country_dropdown"></div>
            </div>
          </div>
        </div>
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="passenger_count">Jumlah Penumpang</label>
            <input id="passenger_count" type="number" name="passenger_count" value="{{ old('passenger_count', $spjManual->passenger_count) }}" min="1" required>
          </div>
        </div>
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="start_date">Tanggal Mulai</label>
            <input id="start_date" type="date" name="start_date" value="{{ old('start_date', $spjManual->start_date->format('Y-m-d')) }}" required>
          </div>
        </div>
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="pickup_time">Waktu Jemput</label>
            <input id="pickup_time" type="time" name="pickup_time" value="{{ old('pickup_time', $spjManual->pickup_time) }}" required>
          </div>
        </div>
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="service_type">Layanan</label>
            <div class="search-select" id="service_select">
              <input id="service_search" class="search-input" type="text" placeholder="Ketik nama layanan..." autocomplete="off" value="{{ old('service_type', $spjManual->service_type) }}">
              <input type="hidden" name="service_type" id="service_type_hidden" value="{{ old('service_type', $spjManual->service_type) }}">
              <div class="search-dropdown" id="service_dropdown"></div>
            </div>
          </div>
        </div>
        <div class="vm-form-col-12">
          <div class="vm-form-field">
            <label for="pickup_address">Alamat Jemput</label>
            <textarea id="pickup_address" name="pickup_address" required>{{ old('pickup_address', $spjManual->pickup_address) }}</textarea>
          </div>
        </div>
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="service_duration">Durasi Layanan</label>
            <input id="service_duration" type="text" name="service_duration" value="{{ old('service_duration', $spjManual->service_duration) }}" placeholder="Contoh: 12 jam">
          </div>
        </div>
        <div class="vm-form-col-6">
          <div class="vm-form-field">
            <label for="payment_plan">Rencana Pembayaran</label>
            <select id="payment_plan" name="payment_plan">
              <option value="">Pilih rencana pembayaran</option>
              <option value="down_payment" {{ old('payment_plan', $spjManual->payment_plan) == 'down_payment' ? 'selected' : '' }}>Down Payment</option>
              <option value="payment_full_transfer" {{ old('payment_plan', $spjManual->payment_plan) == 'payment_full_transfer' ? 'selected' : '' }}>Payment Full Transfer</option>
              <option value="payment_full_on_driver" {{ old('payment_plan', $spjManual->payment_plan) == 'payment_full_on_driver' ? 'selected' : '' }}>Payment Full On Driver</option>
            </select>
          </div>
        </div>
        <div class="vm-form-col-12">
          <div class="vm-form-field">
            <label for="trip_details">Rincian Perjalanan</label>
            <textarea id="trip_details" name="trip_details">{{ old('trip_details', $spjManual->trip_details) }}</textarea>
          </div>
        </div>
      </div>

      <div class="vm-actions" style="margin-top:1rem;">
        <button type="submit" class="vm-btn vm-btn-primary">Simpan</button>
      </div>
    </form>
  </div>
  </main>
</div>

<script>
  const MITRAS = [
    @foreach($mitras as $m)
      { id: {{ $m->id }}, name: "{{ addslashes($m->full_name) }}" }{{ $loop->last ? '' : ',' }}
    @endforeach
  ];
  const wrap = document.getElementById('driver_select');
  const searchInput = document.getElementById('driver_search');
  const hiddenInput = document.getElementById('driver_name');
  const dropdown = document.getElementById('driver_dropdown');

  function render(q) {
    const s = (q || '').toLowerCase().trim();
    const items = MITRAS.filter(m => m.name.toLowerCase().includes(s));
    dropdown.innerHTML = '';
    if (!items.length) {
      const empty = document.createElement('div');
      empty.className = 'search-empty';
      empty.textContent = 'Tidak ada hasil';
      dropdown.appendChild(empty);
      return;
    }
    items.forEach(function(item) {
      const el = document.createElement('div');
      el.className = 'search-item' + (hiddenInput.value === item.name ? ' selected' : '');
      el.textContent = item.name;
      el.addEventListener('click', function() {
        hiddenInput.value = item.name;
        searchInput.value = item.name;
        wrap.classList.remove('open');
      });
      dropdown.appendChild(el);
    });
  }

  searchInput.addEventListener('focus', function() {
    wrap.classList.add('open');
    render(searchInput.value);
  });
  searchInput.addEventListener('input', function() {
    hiddenInput.value = '';
    wrap.classList.add('open');
    render(searchInput.value);
  });
  document.addEventListener('click', function(e) {
    if (!wrap.contains(e.target)) wrap.classList.remove('open');
  });
</script>
<script>
  const COUNTRIES = [
    "Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria",
    "Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan",
    "Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cambodia",
    "Cameroon","Canada","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Congo-Brazzaville)","Costa Rica",
    "Croatia","Cuba","Cyprus","Czechia","Democratic Republic of the Congo","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador",
    "Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini","Ethiopia","Fiji","Finland","France",
    "Gabon","Gambia","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau",
    "Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland",
    "Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Kuwait","Kyrgyzstan",
    "Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Madagascar",
    "Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia",
    "Moldova","Monaco","Mongolia","Montenegro","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal",
    "Netherlands","New Zealand","Nicaragua","Niger","Nigeria","North Korea","North Macedonia","Norway","Oman","Pakistan",
    "Palau","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania",
    "Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal",
    "Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Korea",
    "South Sudan","Spain","Sri Lanka","Sudan","Suriname","Sweden","Switzerland","Syria","Taiwan","Tajikistan",
    "Tanzania","Thailand","Timor-Leste","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu",
    "Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela",
    "Vietnam","Yemen","Zambia","Zimbabwe"
  ];
  const countrySelect = document.getElementById('country_select');
  const countrySearch = document.getElementById('country_search');
  const countryHidden = document.getElementById('country_of_origin_hidden');
  const countryDropdown = document.getElementById('country_dropdown');
  function renderCountryList(items) {
    countryDropdown.innerHTML = '';
    if (!items.length) {
      const empty = document.createElement('div');
      empty.className = 'search-empty';
      empty.textContent = 'Tidak ada hasil';
      countryDropdown.appendChild(empty);
      return;
    }
    items.forEach(function(c) {
      const el = document.createElement('div');
      el.className = 'search-item' + (countryHidden.value === c ? ' selected' : '');
      el.textContent = c;
      el.addEventListener('click', function() {
        countrySearch.value = c;
        countryHidden.value = c;
        countrySelect.classList.remove('open');
      });
      countryDropdown.appendChild(el);
    });
  }
  function filterCountries(q) {
    const query = (q || '').toLowerCase().trim();
    if (!query) return COUNTRIES.slice();
    return COUNTRIES.filter(c => c.toLowerCase().includes(query));
  }
  countrySearch.addEventListener('focus', function() {
    countrySelect.classList.add('open');
    renderCountryList(filterCountries(countrySearch.value));
  });
  countrySearch.addEventListener('input', function() {
    countryHidden.value = '';
    countrySelect.classList.add('open');
    renderCountryList(filterCountries(countrySearch.value));
  });
  document.addEventListener('click', function(e) {
    if (!countrySelect.contains(e.target)) countrySelect.classList.remove('open');
  });
</script>
<script>
  const SERVICES = [
    @foreach($services as $s)
      { id: {{ $s->id }}, name: "{{ addslashes($s->name) }}" }{{ $loop->last ? '' : ',' }}
    @endforeach
  ];
  const serviceSelect = document.getElementById('service_select');
  const serviceSearch = document.getElementById('service_search');
  const serviceHidden = document.getElementById('service_type_hidden');
  const serviceDropdown = document.getElementById('service_dropdown');
  function renderServiceList(items) {
    serviceDropdown.innerHTML = '';
    if (!items.length) {
      const empty = document.createElement('div');
      empty.className = 'search-empty';
      empty.textContent = 'Tidak ada hasil';
      serviceDropdown.appendChild(empty);
      return;
    }
    items.forEach(function(item) {
      const el = document.createElement('div');
      el.className = 'search-item' + (serviceHidden.value === item.name ? ' selected' : '');
      el.textContent = item.name;
      el.addEventListener('click', function() {
        serviceSearch.value = item.name;
        serviceHidden.value = item.name;
        serviceSelect.classList.remove('open');
      });
      serviceDropdown.appendChild(el);
    });
  }
  function filterServices(q) {
    const s = (q || '').toLowerCase().trim();
    if (!s) return SERVICES.slice();
    return SERVICES.filter(m => m.name.toLowerCase().includes(s));
  }
  serviceSearch.addEventListener('focus', function() {
    serviceSelect.classList.add('open');
    renderServiceList(filterServices(serviceSearch.value));
  });
  serviceSearch.addEventListener('input', function() {
    serviceHidden.value = '';
    serviceSelect.classList.add('open');
    renderServiceList(filterServices(serviceSearch.value));
  });
  document.addEventListener('click', function(e) {
    if (!serviceSelect.contains(e.target)) serviceSelect.classList.remove('open');
  });
</script>
<script>
  document.querySelector('form').addEventListener('submit', function() {
    var driverSearch = document.getElementById('driver_search');
    var driverHidden = document.getElementById('driver_name');
    if (!driverHidden.value && driverSearch.value) driverHidden.value = driverSearch.value;

    var countrySearch = document.getElementById('country_search');
    var countryHidden = document.getElementById('country_of_origin_hidden');
    if (!countryHidden.value && countrySearch.value) countryHidden.value = countrySearch.value;

    var serviceSearch = document.getElementById('service_search');
    var serviceHidden = document.getElementById('service_type_hidden');
    if (!serviceHidden.value && serviceSearch.value) serviceHidden.value = serviceSearch.value;
  });
</script>

<style>
  code { background: #eef2ff; padding: .1rem .35rem; border-radius: 4px; font-size: .8rem; color: #1d4ed8; }
</style>

@endsection