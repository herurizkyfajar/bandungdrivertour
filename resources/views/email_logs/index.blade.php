@extends('layouts.app', ['title' => 'Log Email'])

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

  /* ===== Stats cards ===== */
  .elog-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem; }
  .stat-card {
    border: 1px solid #dbeafe;
    background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
    border-radius: 14px;
    padding: 1rem 1.1rem;
  }
  .stat-card.sent { border-color: #dcfce7; background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%); }
  .stat-card.failed { border-color: #fee2e2; background: linear-gradient(180deg, #fef2f2 0%, #ffffff 100%); }
  .stat-label { font-size: .78rem; font-weight: 800; color: #64748b; letter-spacing: .04em; text-transform: uppercase; }
  .stat-value { font-size: 1.9rem; font-weight: 800; line-height: 1.1; color: #1d4ed8; }
  .stat-card.sent .stat-value { color: #16a34a; }
  .stat-card.failed .stat-value { color: #dc2626; }

  /* ===== Table ===== */
  .table-wrap { overflow-x: auto; }
  .elog-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; }
  .elog-table th {
    padding: .95rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #334155;
    font-size: .85rem;
  }
  .elog-table td {
    padding: .95rem 1rem;
    vertical-align: top;
    line-height: 1.5;
    word-break: break-word;
  }
  .elog-table tr { background: #ffffff; }
  .time-date { font-weight: 700; }
  .time-clock { font-size: .85rem; color: #64748b; }
  .booking-ref { font-size: .85rem; color: #64748b; margin-top: .15rem; }
  .status-badge {
    display: inline-flex;
    align-items: center;
    padding: .35rem .75rem;
    border-radius: 999px;
    font-weight: 800;
    font-size: .85rem;
  }
  .status-sent { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
  .status-failed { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
  .email-log-error {
    color: #b91c1c;
    font-weight: 600;
    cursor: copy;
    max-width: 100%;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .email-log-error:hover { text-decoration: underline; }

  /* ===== Empty state ===== */
  .empty-state { text-align: center; padding: 2rem; }
  .empty-title { font-size: 1.05rem; font-weight: 700; margin-bottom: .25rem; }
  .empty-sub { font-size: .85rem; color: #64748b; }

  /* ===== Mobile ===== */
  .mobile-cards { display: none; margin-top: 1rem; }
  .mobile-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem; margin-bottom: .75rem; }
  .mobile-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; margin-bottom: .5rem; }
  .mobile-card-time { font-weight: 700; color: #0f172a; font-size: .9rem; }
  .mobile-card-recipient { font-weight: 600; color: #0f172a; font-size: .9rem; word-break: break-word; }
  .mobile-card-meta { font-size: .82rem; color: #6b7280; margin-bottom: .5rem; }
  .mobile-card-error { font-size: .82rem; color: #b91c1c; font-weight: 600; margin-top: .25rem; cursor: copy; }
  .desktop-table { display: block; }

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
    .elog-stats { grid-template-columns: 1fr !important; }
    .desktop-table { display: none !important; }
    .mobile-cards { display: block !important; }
    .card { box-shadow: none; }
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
        <div class="vm-page-label"><i class="bi bi-envelope-open"></i><span>Log Email</span></div>
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
        <div class="vm-eyebrow">LOG EMAIL</div>
        <h2>Log Email</h2>
      </div>
      <div class="vm-hero-actions">
        <a class="vm-btn vm-btn-primary" href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Kembali ke Dashboard</a>
        <a class="vm-btn" href="{{ route('dashboard.calendar') }}"><i class="bi bi-calendar3"></i> Kalender</a>
      </div>
    </section>

    <div class="vm-card">
      <div class="vm-card-head">
        <h3><i class="bi bi-envelope-open"></i>Histori Pengiriman Email</h3>
      </div>

      @if(session('success'))
        <div class="vm-flash">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div style="padding:.75rem 1rem;border-radius:10px;margin-bottom:1rem;font-weight:600;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;">{{ session('error') }}</div>
      @endif

      <!-- Stats -->
      <div class="elog-stats">
        <div class="stat-card">
          <div class="stat-label">Total Log</div>
          <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>
        <div class="stat-card sent">
          <div class="stat-label">Sent</div>
          <div class="stat-value">{{ number_format($stats['sent'] ?? 0) }}</div>
        </div>
        <div class="stat-card failed">
          <div class="stat-label">Failed</div>
          <div class="stat-value">{{ number_format($stats['failed'] ?? 0) }}</div>
        </div>
      </div>

      <!-- Filter form -->
      <form method="GET" action="{{ route('email-logs.index') }}" class="vm-card" style="margin-top:1rem; border-color:#dbeafe; background:#f8fbff;">
        <div class="card-body" style="padding:0;">
          <div class="vm-form-grid">
            <div class="vm-form-col-4">
              <div class="vm-form-field">
                <label>Search</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="Search email, subject, or error">
              </div>
            </div>
            <div class="vm-form-col-3">
              <div class="vm-form-field">
                <label>Status</label>
                <select name="status">
                  <option value="">All</option>
                  <option value="sent" {{ $status === 'sent' ? 'selected' : '' }}>Sent</option>
                  <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
              </div>
            </div>
            <div class="vm-form-col-3">
              <div class="vm-form-field">
                <label>From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}">
              </div>
            </div>
            <div class="vm-form-col-3">
              <div class="vm-form-field">
                <label>To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}">
              </div>
            </div>
            <div class="vm-form-col-3">
              <div class="vm-form-field">
                <label style="white-space:nowrap;">Per Page</label>
                <select name="per_page">
                  <option value="10" {{ (int) $perPage === 10 ? 'selected' : '' }}>10</option>
                  <option value="50" {{ (int) $perPage === 50 ? 'selected' : '' }}>50</option>
                  <option value="100" {{ (int) $perPage === 100 ? 'selected' : '' }}>100</option>
                </select>
              </div>
            </div>
          </div>

          <div class="vm-actions" style="margin-top:1rem; justify-content:space-between; flex-wrap:wrap;">
            <div class="subtitle" style="font-size:.82rem; color:#64748b;">
              Showing {{ $emailLogs->firstItem() ?? 0 }} - {{ $emailLogs->lastItem() ?? 0 }} of {{ number_format($emailLogs->total()) }} results
            </div>
            <div>
              <a class="vm-btn" href="{{ route('email-logs.index') }}" style="padding:.55rem .9rem; font-size:.82rem;">Reset</a>
              <button type="submit" class="vm-btn vm-btn-primary" style="padding:.55rem .9rem; font-size:.82rem;">Apply Filter</button>
            </div>
          </div>
        </div>
      </form>

      <!-- Desktop table -->
      <div class="desktop-table" style="margin-top:1rem;">
        <div class="table-wrap">
          <table class="elog-table">
            <thead>
              <tr>
                <th>Time</th>
                <th>Recipient</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Error</th>
              </tr>
            </thead>
            <tbody>
              @forelse($emailLogs as $log)
              <tr>
                <td>
                  <div class="time-date">{{ optional($log->created_at)->format('d M Y') }}</div>
                  <div class="time-clock">{{ optional($log->created_at)->format('H:i:s') }}</div>
                </td>
                <td>
                  <div style="font-weight:700;">{{ $log->to_email }}</div>
                  @if($log->booking_id || $log->invoice_id)
                    <div class="booking-ref">
                      @if($log->booking_id) Booking #{{ $log->booking_id }}@endif
                      @if($log->booking_id && $log->invoice_id) · @endif
                      @if($log->invoice_id) Invoice #{{ $log->invoice_id }}@endif
                    </div>
                  @endif
                </td>
                <td>
                  <div style="font-weight:700;">{{ $log->subject }}</div>
                </td>
                <td>
                  @php
                    $statusClass = $log->status === 'sent' ? 'status-sent' : 'status-failed';
                  @endphp
                  <span class="status-badge {{ $statusClass }}">{{ ucfirst($log->status) }}</span>
                </td>
                <td>
                  @if($log->error_message)
                    <div class="email-log-error" title="Click to copy full message">{{ $log->error_message }}</div>
                  @else
                    <div class="subtitle" style="font-size:.82rem; color:#64748b;">-</div>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5">
                  <div class="empty-state">
                    <div class="empty-title">No email logs yet</div>
                    <div class="empty-sub">Try changing the filter or send a test email first.</div>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Mobile cards -->
      <div class="mobile-cards" style="margin-top:1rem;">
        @forelse($emailLogs as $log)
        <div class="mobile-card">
          <div class="mobile-card-header">
            <div>
              <div class="mobile-card-time">{{ optional($log->created_at)->format('d M Y H:i:s') }}</div>
              <div class="mobile-card-recipient" style="margin-top:.25rem;">{{ $log->to_email }}</div>
              @if($log->booking_id || $log->invoice_id)
                <div class="mobile-card-meta" style="margin-top:.15rem;">
                  @if($log->booking_id) Booking #{{ $log->booking_id }}@endif
                  @if($log->booking_id && $log->invoice_id) · @endif
                  @if($log->invoice_id) Invoice #{{ $log->invoice_id }}@endif
                </div>
              @endif
            </div>
            @php
              $statusClass = $log->status === 'sent' ? 'status-sent' : 'status-failed';
            @endphp
            <span class="status-badge {{ $statusClass }}">{{ ucfirst($log->status) }}</span>
          </div>
          @if($log->error_message)
            <div class="mobile-card-error email-log-error" title="Click to copy full message">{{ $log->error_message }}</div>
          @endif
        </div>
        @empty
        <div class="empty-state">
          <div class="empty-title">No email logs yet</div>
          <div class="empty-sub">Try changing the filter or send a test email first.</div>
        </div>
        @endforelse
        @if($emailLogs->hasPages())
        <div style="margin-top:.75rem;">
          {{ $emailLogs->links() }}
        </div>
        @endif
      </div>

      <!-- Pagination -->
      <div style="margin-top:1rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <div class="subtitle" style="font-size:.82rem; color:#64748b;">
          Page {{ $emailLogs->currentPage() }} of {{ $emailLogs->lastPage() }}
        </div>
        <div>{{ $emailLogs->links() }}</div>
      </div>
    </div>
  </main>
</div>

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

<style>
  code { background: #eef2ff; padding: .1rem .35rem; border-radius: 4px; font-size: .8rem; color: #1d4ed8; }
</style>

@endsection