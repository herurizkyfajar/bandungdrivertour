@extends('layouts.app', ['title' => 'Booking Data'])

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
  .booking-data-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 1.25rem;
    align-items: start;
    min-height: calc(100vh - 6rem);
    padding-left: 252px;
  }

  .booking-data-main {
    display: grid;
    gap: 1.05rem;
    align-content: start;
    min-width: 0;
    min-height: 0;
    padding: 0 0 1rem;
  }

  /* ===== Sidebar (sama seperti dashboard) ===== */
  .booking-data-shell > .dashboard-sidebar {
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

  .booking-data-shell > .dashboard-sidebar::after {
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

  .booking-data-shell .dashboard-brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .45rem;
    padding: 0 .3rem 1rem;
    margin-bottom: .75rem;
    border-bottom: 1px solid rgba(255, 255, 255, .18);
  }

  .booking-data-shell .dashboard-brand img {
    width: 142px;
    height: auto;
    padding: .3rem .45rem;
    border-radius: 10px;
    background: #ffffff;
    object-fit: contain;
  }

  .booking-data-shell .dashboard-brand h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #ffffff;
  }

  .booking-data-shell .dashboard-brand .subtitle {
    font-size: .82rem;
    color: rgba(255, 255, 255, .82);
  }

  .booking-data-shell .dashboard-section-label {
    margin: .85rem .3rem .4rem;
    padding: 0;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .6);
  }

  .booking-data-shell .dashboard-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: .12rem;
  }

  .booking-data-shell .dashboard-menu a,
  .booking-data-shell .dashboard-menu button {
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

  .booking-data-shell .dashboard-menu a:hover,
  .booking-data-shell .dashboard-menu button:hover {
    background: rgba(255, 255, 255, .12);
    color: #ffffff;
  }

  .booking-data-shell .dashboard-menu a.active {
    background: #ffffff;
    color: #0b4fd6;
    box-shadow: 0 6px 14px rgba(4, 33, 90, .22);
  }

  .booking-data-shell .dashboard-menu .nav-icon {
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

  .booking-data-shell .dashboard-menu a.active .nav-icon {
    background: #e7f0ff;
    color: #0b4fd6;
  }

  /* ===== Topbar ===== */
  .bd-topbar {
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

  .bd-topbar-left { display: flex; align-items: center; gap: .6rem; min-width: 0; }
  .bd-topbar-right { display: flex; align-items: center; gap: .45rem; flex: 0 0 auto; }

  .bd-menu-btn {
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

  .bd-page-label {
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

  .bd-page-label i { color: #1565c0; font-size: .95rem; }

  .bd-icon-btn {
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

  .bd-icon-btn:hover { background: #f4f9ff; border-color: #cfe0f7; color: #0b4fd6; }

  .bd-user-wrap { position: relative; }

  .bd-user {
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

  .bd-user:hover { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }
  .bd-user[aria-expanded="true"] { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }

  .bd-avatar {
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

  .bd-user-meta { display: flex; flex-direction: column; line-height: 1.15; min-width: 0; overflow: hidden; }
  .bd-user-meta strong { max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .82rem; font-weight: 800; color: #0b2545; }
  .bd-user-meta span { font-size: .7rem; color: #64748b; text-transform: capitalize; }

  .bd-user > i { color: #94a3b8; font-size: .75rem; transition: transform .15s ease; }
  .bd-user[aria-expanded="true"] > i { transform: rotate(180deg); }

  .bd-user-menu {
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

  .bd-user-menu[hidden] { display: none; }

  .bd-user-menu-head {
    display: flex;
    flex-direction: column;
    gap: .15rem;
    padding: .55rem .7rem .6rem;
    margin-bottom: .35rem;
    border-bottom: 1px solid #eef2f8;
  }

  .bd-user-menu-head strong { font-size: .82rem; font-weight: 800; color: #0b2545; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .bd-user-menu-head span { font-size: .72rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  .bd-user-menu-item {
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

  .bd-user-menu-item i { font-size: .95rem; color: #64748b; }
  .bd-user-menu-item:hover { background: #f1f5fb; }
  .bd-user-menu-item--danger { color: #b91c1c; }
  .bd-user-menu-item--danger i { color: #b91c1c; }
  .bd-user-menu-item--danger:hover { background: #fef2f2; }

  /* ===== Hero ===== */
  .bd-hero {
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

  .bd-hero::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 5px;
    background: linear-gradient(90deg, #22c55e 0%, #84d94e 38%, #2f80ed 100%);
    opacity: .9;
  }

  .bd-hero-content { position: relative; z-index: 2; flex: 1 1 auto; min-width: 0; }

  .bd-eyebrow {
    margin-bottom: .3rem;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .14em;
    color: #1d6fe0;
  }

  .bd-hero h2 { margin: 0; font-size: 1.85rem; font-weight: 800; letter-spacing: -.03em; color: #0b2545; }

  .bd-hero-filter {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .55rem .7rem;
    border-radius: 14px;
    border: 1px solid #cfe0f7;
    background: rgba(255, 255, 255, .94);
    box-shadow: 0 10px 22px rgba(11, 61, 145, .12);
    flex: 0 0 auto;
  }

  .bd-hero-filter label { margin: 0; font-size: .8rem; font-weight: 800; color: #0b2545; white-space: nowrap; }

  .bd-hero-filter select {
    padding: .48rem .7rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .84rem;
    font-weight: 600;
    outline: none;
    min-width: 190px;
  }

  .bd-hero-filter select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .14); }

  /* ===== Kartu statistik ===== */
  .bd-status-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .85rem; }

  .bd-status-card {
    display: flex;
    align-items: center;
    gap: .8rem;
    padding: .95rem 1rem;
    border-radius: 18px;
    border: 1px solid transparent;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .05);
  }

  .bd-status-icon {
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

  .bd-status-label { font-size: .82rem; font-weight: 700; color: #334155; }
  .bd-status-value { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; color: #0f172a; line-height: 1.15; }

  .bd-status-card--blue { background: #eff6ff; border-color: #dbeafe; }
  .bd-status-card--blue .bd-status-icon { color: #2563eb; }
  .bd-status-card--amber { background: #fffbeb; border-color: #fef3c7; }
  .bd-status-card--amber .bd-status-icon { color: #d97706; }
  .bd-status-card--green { background: #f0fdf4; border-color: #dcfce7; }
  .bd-status-card--green .bd-status-icon { color: #16a34a; }
  .bd-status-card--red { background: #fef2f2; border-color: #fee2e2; }
  .bd-status-card--red .bd-status-icon { color: #dc2626; }

  /* ===== Kartu pendapatan ===== */
  .bd-revenue-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    min-height: 104px;
    padding: 1.1rem 1.25rem;
    border-radius: 18px;
    overflow: hidden;
    color: #ffffff;
    background: linear-gradient(135deg, #1e63e9 0%, #0b4fd6 100%);
    box-shadow: 0 10px 22px rgba(13, 43, 82, .12);
  }

  .bd-revenue-icon {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 13px;
    background: rgba(255, 255, 255, .18);
    font-size: 1.2rem;
  }

  .bd-revenue-body { position: relative; z-index: 2; min-width: 0; }
  .bd-revenue-body .label { font-size: .82rem; font-weight: 700; color: rgba(255, 255, 255, .82); margin-bottom: .15rem; }
  .bd-revenue-body .value { font-size: 1.75rem; font-weight: 800; letter-spacing: -.03em; line-height: 1.15; }

  .bd-revenue-bars {
    margin-left: auto;
    display: flex;
    align-items: flex-end;
    gap: 5px;
    height: 54px;
    opacity: .55;
  }

  .bd-revenue-bars span { width: 9px; border-radius: 4px; background: #ffffff; }

  /* ===== Kartu umum ===== */
  .bd-card {
    background: #ffffff;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
    min-width: 0;
  }

  .bd-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: .85rem;
  }

  .bd-card-head h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0b2545;
    letter-spacing: -.01em;
  }

  .bd-card-head h3 i { color: #1565c0; font-size: 1rem; }

  .bd-range { display: flex; align-items: center; gap: .35rem; flex-wrap: wrap; }
  .bd-range label { margin: 0; font-size: .72rem; font-weight: 700; color: #64748b; white-space: nowrap; }

  .bd-range input {
    padding: .4rem .55rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .78rem;
    outline: none;
  }

  .bd-range input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .14); }

  .bd-chart-box { position: relative; height: 260px; }
  .bd-chart-row { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 1.05rem; }
  .bd-donut { position: relative; max-width: 230px; margin: 0 auto; }

  /* ===== Toolbar ===== */
  .bd-toolbar {
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

  .bd-field { display: flex; align-items: center; gap: .35rem; }
  .bd-toolbar label { margin: 0; font-size: .74rem; font-weight: 700; color: #64748b; white-space: nowrap; }

  .bd-toolbar select,
  .bd-toolbar input {
    padding: .45rem .6rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .8rem;
    outline: none;
  }

  .bd-toolbar select:focus,
  .bd-toolbar input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .14); }

  .bd-toolbar input[type="text"] { min-width: 180px; flex: 1; }

  /* ===== Tabel ===== */
  .bd-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border: 1px solid #e8f0fb; border-radius: 14px; }
  .bd-table { width: 100%; border-collapse: separate; border-spacing: 0; }

  .bd-table thead th {
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

  .bd-table tbody td {
    padding: .68rem .8rem;
    border-bottom: 1px solid #eef4fc;
    font-size: .85rem;
    color: #1f3a5f;
    vertical-align: middle;
    word-break: break-word;
  }

  .bd-table tbody tr:last-child td { border-bottom: none; }
  .bd-table tbody tr:hover td { background: #f9fbff; }

  .badge { display: inline-block; padding: .24rem .6rem; border-radius: 999px; font-size: .72rem; font-weight: 800; white-space: nowrap; }
  .badge-new { background: #eff6ff; color: #1d4ed8; }
  .badge-process { background: #fef3c7; color: #92400e; }
  .badge-done { background: #dcfce7; color: #166534; }
  .badge-cancel { background: #fee2e2; color: #991b1b; }

  .invoice-link { color: #1565c0; font-weight: 700; cursor: pointer; text-decoration: underline; text-underline-offset: 2px; }
  .invoice-link:hover { color: #0b4fd6; }

  .empty-msg { text-align: center; padding: 2rem; color: #94a3b8; font-size: .86rem; }

  .bd-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-top: .9rem;
    font-size: .8rem;
    color: #64748b;
  }

  .bd-pagination button {
    padding: .42rem .8rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .78rem;
    font-weight: 700;
    cursor: pointer;
  }

  .bd-pagination button:hover:not(:disabled) { background: #f2f7ff; border-color: #bcd5f5; }
  .bd-pagination button:disabled { opacity: .45; cursor: not-allowed; }

  /* ===== Kartu mobile ===== */
  .mobile-cards { display: none; }

  .mobile-card {
    background: #ffffff;
    border: 1px solid #e8f0fb;
    border-radius: 16px;
    padding: 1rem;
    margin-bottom: .7rem;
    box-shadow: 0 8px 20px rgba(13, 43, 82, .05);
  }

  .mobile-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; margin-bottom: .35rem; }
  .mobile-card-title { font-weight: 800; color: #0b2545; font-size: .9rem; }
  .mobile-card-meta { font-size: .8rem; color: #64748b; line-height: 1.5; }

  /* ===== Modal detail ===== */
  .detail-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9998;
    background: rgba(9, 27, 51, .5);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
  }

  .detail-backdrop.open { display: flex; align-items: center; justify-content: center; padding: 1rem; }

  .detail-modal {
    width: min(900px, 96%);
    max-height: 90vh;
    overflow: auto;
    background: #ffffff;
    border-radius: 22px;
    border: 1px solid #e8f0fb;
    box-shadow: 0 28px 70px rgba(9, 42, 90, .35);
  }

  .detail-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .75rem;
    padding: 1.25rem 1.5rem .85rem;
    border-bottom: 1px solid #eef4fc;
  }

  .detail-head h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #0b2545; letter-spacing: -.01em; }
  .detail-head .sub { font-size: .82rem; color: #64748b; margin-top: .15rem; }

  .detail-close {
    border: 1px solid #dbe7f6;
    background: #ffffff;
    color: #0b2545;
    border-radius: 999px;
    width: 36px;
    height: 36px;
    font-size: 1rem;
    cursor: pointer;
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .detail-close:hover { background: #f4f9ff; }

  .detail-body { padding: 1.1rem 1.5rem 1.5rem; }
  .detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: .75rem; margin-bottom: 1rem; }

  .detail-box { background: #f7faff; border: 1px solid #e8f0fb; border-radius: 12px; padding: .65rem .8rem; }
  .detail-label { font-size: .7rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .2rem; display: block; }
  .detail-value { font-size: .87rem; font-weight: 700; color: #0b2545; }

  .detail-section { margin-top: 1rem; }
  .detail-section h4 { margin: 0 0 .6rem; font-size: .9rem; font-weight: 800; color: #0b2545; }
  .detail-actions { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .75rem; }

  .action-link {
    display: inline-block;
    padding: .45rem .85rem;
    border-radius: 10px;
    font-size: .8rem;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid #dbe7f6;
    color: #0b2545;
    background: #ffffff;
  }

  .action-link:hover { background: #f4f9ff; border-color: #bcd5f5; }
  .action-link--primary { background: #0b4fd6; color: #ffffff; border-color: #0b4fd6; }
  .action-link--primary:hover { background: #093fa9; border-color: #093fa9; }

  /* ===== Responsive ===== */
  @media (max-width: 1200px) {
    .booking-data-shell { padding-left: 0; }
    .booking-data-shell > .dashboard-sidebar { display: none !important; }
    .bd-menu-btn { display: inline-flex; }
    .bd-status-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .bd-chart-row { grid-template-columns: minmax(0, 1fr); }
    body.mobile-sidebar-open .booking-data-shell > .dashboard-sidebar {
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
    .bd-topbar { padding: .55rem .7rem; border-radius: 16px; }
    .bd-page-label span { display: none; }
    .bd-user { padding: .3rem; border: none; background: transparent; }
    .bd-user-meta, .bd-user > i { display: none; }
    .bd-hero { flex-direction: column; align-items: stretch; gap: .9rem; min-height: 0; padding: 1.15rem 1.15rem 1.4rem; border-radius: 18px; }
    .bd-hero h2 { font-size: 1.45rem; }
    .bd-hero-filter { width: 100%; }
    .bd-hero-filter select { flex: 1; min-width: 0; }
    .bd-status-grid { grid-template-columns: minmax(0, 1fr); }
    .bd-status-value { font-size: 1.35rem; }
    .bd-revenue-body .value { font-size: 1.45rem; }
    .bd-revenue-bars { display: none; }
    .bd-chart-box { height: 220px; }
    .bd-toolbar { flex-direction: column; align-items: stretch; }
    .bd-field { width: 100%; }
    .bd-toolbar input[type="text"] { min-width: 0; }
    .desktop-table { display: none !important; }
    .mobile-cards { display: block !important; }
    .detail-backdrop.open { padding: 0; }
    .detail-modal { width: 100%; max-height: 100vh; height: 100%; border-radius: 0; }
    .detail-grid { grid-template-columns: 1fr 1fr; }
  }
</style>

@php
  $allRows = $rawBookings->values()->toArray();
@endphp

<div class="booking-data-shell">
  @include('partials.admin-sidebar')

  <main class="booking-data-main">
    <div class="bd-topbar">
      <div class="bd-topbar-left">
        <button class="bd-menu-btn" id="bdMenuBtn" type="button" aria-label="Buka menu">
          <i class="bi bi-list"></i>
        </button>
        <div class="bd-page-label"><i class="bi bi-database"></i><span>Booking Data</span></div>
      </div>
      <div class="bd-topbar-right">
        <a class="bd-icon-btn" href="{{ route('dashboard') }}" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
          <i class="bi bi-house-door"></i>
        </a>
        <a class="bd-icon-btn" href="{{ route('dashboard.calendar') }}" title="Kalender booking" aria-label="Kalender booking">
          <i class="bi bi-calendar3"></i>
        </a>
        <div class="bd-user-wrap" id="bdUserWrap">
          <button class="bd-user" id="bdUserBtn" type="button" title="{{ auth()->user()->name ?? 'Pengguna' }}" aria-haspopup="true" aria-expanded="false" aria-controls="bdUserMenu">
            <div class="bd-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div class="bd-user-meta">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ str_replace('_', ' ', auth()->user()->role ?? 'user') }}</span>
            </div>
            <i class="bi bi-chevron-down"></i>
          </button>
          <div class="bd-user-menu" id="bdUserMenu" role="menu" aria-labelledby="bdUserBtn" hidden>
            <div class="bd-user-menu-head">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ auth()->user()->email ?? '' }}</span>
            </div>
            <a class="bd-user-menu-item" href="{{ route('accounts.show', auth()->id()) }}" role="menuitem">
              <i class="bi bi-person"></i> Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="bd-user-menu-item bd-user-menu-item--danger" role="menuitem">
                <i class="bi bi-box-arrow-right"></i> Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <section class="bd-hero">
      <div class="bd-hero-content">
        <div class="bd-eyebrow">BOOKING DATA</div>
        <h2>Booking Data</h2>
      </div>
      <div class="bd-hero-filter">
        <label for="filterGroup">Group:</label>
        <select id="filterGroup">
          <option value="">All Groups</option>
          @foreach($groups as $g)
            <option value="{{ $g->name }}">{{ $g->name }}</option>
          @endforeach
        </select>
      </div>
    </section>

    <div class="bd-status-grid">
      <div class="bd-status-card bd-status-card--blue">
        <div class="bd-status-icon"><i class="bi bi-calendar3"></i></div>
        <div>
          <div class="bd-status-label">Total Bookings</div>
          <div class="bd-status-value" id="statTotal">0</div>
        </div>
      </div>
      <div class="bd-status-card bd-status-card--amber">
        <div class="bd-status-icon"><i class="bi bi-clock-history"></i></div>
        <div>
          <div class="bd-status-label">Processing</div>
          <div class="bd-status-value" id="statProcessing">0</div>
        </div>
      </div>
      <div class="bd-status-card bd-status-card--green">
        <div class="bd-status-icon"><i class="bi bi-check2-circle"></i></div>
        <div>
          <div class="bd-status-label">Completed</div>
          <div class="bd-status-value" id="statCompleted">0</div>
        </div>
      </div>
      <div class="bd-status-card bd-status-card--red">
        <div class="bd-status-icon"><i class="bi bi-x-circle"></i></div>
        <div>
          <div class="bd-status-label">Canceled</div>
          <div class="bd-status-value" id="statCancelled">0</div>
        </div>
      </div>
    </div>

    <div class="bd-revenue-card">
      <div class="bd-revenue-icon"><i class="bi bi-cash-coin"></i></div>
      <div class="bd-revenue-body">
        <div class="label">Total Revenue</div>
        <div class="value" id="statRevenue">Rp 0</div>
      </div>
      <div class="bd-revenue-bars" aria-hidden="true">
        <span style="height: 34%"></span>
        <span style="height: 52%"></span>
        <span style="height: 42%"></span>
        <span style="height: 68%"></span>
        <span style="height: 58%"></span>
        <span style="height: 88%"></span>
      </div>
    </div>

    <div class="bd-card">
      <div class="bd-card-head">
        <h3><i class="bi bi-graph-up-arrow"></i>Revenue History</h3>
        <div class="bd-range">
          <label for="revFrom">From:</label>
          <input type="month" id="revFrom">
          <label for="revTo">To:</label>
          <input type="month" id="revTo">
        </div>
      </div>
      <div class="bd-chart-box">
        <canvas id="revenueHistoryChart"></canvas>
      </div>
    </div>

    <div class="bd-card">
      <div class="bd-card-head">
        <h3><i class="bi bi-graph-up"></i>Revenue History (Completed)</h3>
        <div class="bd-range">
          <label for="revCompFrom">From:</label>
          <input type="month" id="revCompFrom">
          <label for="revCompTo">To:</label>
          <input type="month" id="revCompTo">
        </div>
      </div>
      <div class="bd-chart-box">
        <canvas id="revenueHistoryCompletedChart"></canvas>
      </div>
    </div>

    <div class="bd-chart-row">
      <div class="bd-card">
        <div class="bd-card-head">
          <h3><i class="bi bi-credit-card"></i>Payment Methods</h3>
        </div>
        <div class="bd-donut"><canvas id="paymentChart"></canvas></div>
      </div>
      <div class="bd-card">
        <div class="bd-card-head">
          <h3><i class="bi bi-megaphone"></i>Info Source</h3>
        </div>
        <div class="bd-donut"><canvas id="infoSourceChart"></canvas></div>
      </div>
    </div>

    <div class="bd-card">
      <div class="bd-card-head">
        <h3><i class="bi bi-table"></i>All Bookings</h3>
      </div>

      <div class="bd-toolbar" id="toolbar">
        <div class="bd-field">
          <label for="perPage">Rows:</label>
          <select id="perPage">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
        <div class="bd-field">
          <label for="searchInput">Search:</label>
          <input type="text" id="searchInput" placeholder="Invoice, customer, vehicle...">
        </div>
        <div class="bd-field">
          <label for="filterStatus">Filter:</label>
          <select id="filterStatus">
            <option value="">All Status</option>
            <option value="masuk">Booking Masuk</option>
            <option value="proses">Booking Proses</option>
            <option value="cancel">Booking Cancel</option>
            <option value="selesai">Booking Selesai</option>
          </select>
        </div>
        <div class="bd-field">
          <label for="sortField">Sort:</label>
          <select id="sortField">
            <option value="invoice">Invoice A-Z</option>
            <option value="invoice_desc">Invoice Z-A</option>
            <option value="amount">Amount 1-9</option>
            <option value="amount_desc">Amount 9-1</option>
            <option value="customer">Customer A-Z</option>
            <option value="customer_desc">Customer Z-A</option>
          </select>
        </div>
      </div>

      <div class="desktop-table">
        <div class="bd-table-wrap">
          <table class="bd-table">
            <thead>
              <tr>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Group</th>
                <th>Amount</th>
                <th>Vehicle</th>
                <th>Mitra</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="desktopBody"></tbody>
          </table>
        </div>
      </div>

      <div class="mobile-cards" id="mobileCards"></div>

      <div class="bd-pagination">
        <span id="pageInfo"></span>
        <div style="display:flex; gap:.35rem;">
          <button id="btnPrev" disabled>&laquo; Prev</button>
          <button id="btnNext" disabled>Next &raquo;</button>
        </div>
      </div>
    </div>
  </main>
</div>

<div class="detail-backdrop" id="detailBackdrop">
  <div class="detail-modal">
    <div class="detail-head">
      <div>
        <h3>Detail Booking</h3>
        <div class="sub">Booking information and quick actions.</div>
      </div>
      <button type="button" class="detail-close" id="detailClose">x</button>
    </div>
    <div class="detail-body">
      <div class="detail-grid">
        <div class="detail-box"><span class="detail-label">Customer</span><div class="detail-value" id="dCustomer">-</div></div>
        <div class="detail-box"><span class="detail-label">Status</span><div class="detail-value" id="dStatus">-</div></div>
        <div class="detail-box"><span class="detail-label">Start Date</span><div class="detail-value" id="dStartDate">-</div></div>
        <div class="detail-box"><span class="detail-label">End Date</span><div class="detail-value" id="dEndDate">-</div></div>
        <div class="detail-box"><span class="detail-label">Vehicle</span><div class="detail-value" id="dVehicle">-</div></div>
        <div class="detail-box"><span class="detail-label">Price</span><div class="detail-value" id="dPrice">-</div></div>
        <div class="detail-box"><span class="detail-label">Group</span><div class="detail-value" id="dGroup">-</div></div>
      </div>

      <div class="detail-section">
        <h4>Full Information</h4>
        <div class="detail-grid">
          <div class="detail-box"><span class="detail-label">Phone</span><div class="detail-value" id="dContact">-</div></div>
          <div class="detail-box"><span class="detail-label">Passengers</span><div class="detail-value" id="dPassengers">-</div></div>
          <div class="detail-box"><span class="detail-label">Country</span><div class="detail-value" id="dCountry">-</div></div>
          <div class="detail-box"><span class="detail-label">Pickup</span><div class="detail-value" id="dPickup">-</div></div>
          <div class="detail-box"><span class="detail-label">Pickup Address</span><div class="detail-value" id="dPickupAddr">-</div></div>
          <div class="detail-box"><span class="detail-label">Service</span><div class="detail-value" id="dService">-</div></div>
          <div class="detail-box"><span class="detail-label">Payment</span><div class="detail-value" id="dPayment">-</div></div>
          <div class="detail-box"><span class="detail-label">Down Payment</span><div class="detail-value" id="dDP">-</div></div>
          <div class="detail-box"><span class="detail-label">Invoice</span><div class="detail-value" id="dInvoice">-</div></div>
        </div>
      </div>

      <div class="detail-section">
        <h4>Travel Plans</h4>
        <div class="detail-value" id="dTravel" style="white-space:normal;">-</div>
      </div>

      <div class="detail-section">
        <h4>Quick Actions</h4>
        <div class="detail-actions" id="dActions"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  Chart.register(ChartDataLabels);

  const allRows = @json($allRows);

  const colorSets = [
    { bg: '#dbeafe', border: '#3b82f6' },
    { bg: '#dcfce7', border: '#22c55e' },
    { bg: '#fef3c7', border: '#f59e0b' },
    { bg: '#fee2e2', border: '#ef4444' },
    { bg: '#f3e8ff', border: '#a855f7' },
    { bg: '#e0f2fe', border: '#0ea5e9' },
  ];

  const badgeClass = (s) => {
    if (['baru_masuk','',null].includes(s)) return 'badge-new';
    if (['konfirmasi','dijadwalkan'].includes(s)) return 'badge-process';
    if (['selesai_pelayanan','selesai_administrasi_fee','completed'].includes(s)) return 'badge-done';
    if (['cancelled','cancel','batal'].includes(s)) return 'badge-cancel';
    return 'badge-new';
  };
  const badgeLabel = (s) => {
    if (['baru_masuk','',null].includes(s)) return 'Pending';
    if (['konfirmasi','dijadwalkan'].includes(s)) return 'Proses';
    if (['selesai_pelayanan','selesai_administrasi_fee','completed'].includes(s)) return 'Selesai';
    if (['cancelled','cancel','batal'].includes(s)) return 'Canceled';
    return s;
  };
  const phaseLabel = (s) => {
    if (['baru_masuk','',null].includes(s)) return 'Booking Masuk';
    if (['konfirmasi','dijadwalkan'].includes(s)) return 'Booking Proses';
    if (['selesai_pelayanan','selesai_administrasi_fee','completed'].includes(s)) return 'Booking Selesai';
    if (['cancelled','cancel','batal'].includes(s)) return 'Booking Cancel';
    return s;
  };
  const paymentLabel = (s) => ({
    down_payment: 'Down Payment',
    payment_full_transfer: 'Full Transfer',
    payment_full_on_driver: 'Cash to Driver',
  }[s] || s || '-');
  const fmt = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
  const isCanceled = (s) => ['cancelled','cancel','batal'].includes(s);
  const isCompleted = (s) => ['selesai_pelayanan','selesai_administrasi_fee'].includes(s);
  const isProcessing = (s) => ['baru_masuk','konfirmasi','dijadwalkan'].includes(s);

  // Chart instances
  let revChart = null, revCompChart = null, paymentChartInst = null, infoSourceChartInst = null;

  function getFilteredGroup() {
    return document.getElementById('filterGroup').value;
  }

  function getGroupRows() {
    const g = getFilteredGroup();
    return g ? allRows.filter(r => r.group_name === g) : allRows;
  }

  function updateStats() {
    const rows = getGroupRows();
    document.getElementById('statTotal').textContent = rows.length;
    document.getElementById('statProcessing').textContent = rows.filter(r => isProcessing(r.status)).length;
    document.getElementById('statCompleted').textContent = rows.filter(r => isCompleted(r.status)).length;
    document.getElementById('statCancelled').textContent = rows.filter(r => isCanceled(r.status)).length;
    document.getElementById('statRevenue').textContent = 'Rp ' + rows
      .filter(r => !isCanceled(r.status))
      .reduce((sum, r) => sum + (Number(r.amount) || 0), 0)
      .toLocaleString('id-ID');
  }

  function buildRevenueHistory(rows) {
    return rows
      .filter(r => r.booking_date_iso && (Number(r.amount) || 0) > 0 && !isCanceled(r.status))
      .reduce((acc, r) => {
        const key = r.booking_date_iso.substring(0, 7);
        const existing = acc.find(a => a.month === key);
        if (existing) { existing.total += Number(r.amount) || 0; }
        else { acc.push({ month: key, label: key, total: Number(r.amount) || 0 }); }
        return acc;
      }, [])
      .sort((a, b) => a.month.localeCompare(b.month))
      .map(a => { a.label = a.month; return a; });
  }

  function buildRevenueHistoryCompleted(rows) {
    return rows
      .filter(r => isCompleted(r.status) && r.booking_date_iso && (Number(r.amount) || 0) > 0)
      .reduce((acc, r) => {
        const key = r.booking_date_iso.substring(0, 7);
        const existing = acc.find(a => a.month === key);
        if (existing) { existing.total += Number(r.amount) || 0; }
        else { acc.push({ month: key, label: key, total: Number(r.amount) || 0 }); }
        return acc;
      }, [])
      .sort((a, b) => a.month.localeCompare(b.month))
      .map(a => { a.label = a.month; return a; });
  }

  function buildPaymentMethods(rows) {
    const counts = {};
    rows.forEach(r => { const k = r.payment_plan || 'unknown'; counts[k] = (counts[k] || 0) + 1; });
    return Object.entries(counts).map(([k, v]) => ({
      label: ({down_payment:'Down Payment',payment_full_transfer:'Full Transfer',payment_full_on_driver:'Cash to Driver'}[k]) || k,
      value: v,
    }));
  }

  function buildInfoSources(rows) {
    const counts = {};
    rows.forEach(r => { const k = r.info_source || 'unknown'; counts[k] = (counts[k] || 0) + 1; });
    return Object.entries(counts).map(([k, v]) => ({
      label: ({instagram:'Instagram',tiktok:'TikTok',google:'Google',whatsapp:'WhatsApp',referral:'Referral',website:'Website',ai:'AI',other:'Other'}[k]) || k,
      value: v,
    }));
  }

  function renderRevenueChart(canvasId, data, chartObj) {
    if (chartObj) chartObj.destroy();
    if (!data.length) return null;
    const fromEl = document.getElementById(canvasId === 'revenueHistoryChart' ? 'revFrom' : 'revCompFrom');
    const toEl = document.getElementById(canvasId === 'revenueHistoryChart' ? 'revTo' : 'revCompTo');
    if (!fromEl.value && data.length) { fromEl.value = data[0].month; toEl.value = data[data.length - 1].month; }
    const from = fromEl.value;
    const to = toEl.value;
    const filtered = data.filter(d => {
      if (from && d.month < from) return false;
      if (to && d.month > to) return false;
      return true;
    });
    const c = new Chart(document.getElementById(canvasId), {
      type: 'line',
      data: {
        labels: filtered.map(d => d.month),
        datasets: [{
          label: 'Revenue',
          data: filtered.map(d => d.total),
          borderColor: canvasId === 'revenueHistoryChart' ? '#2563eb' : '#16a34a',
          backgroundColor: canvasId === 'revenueHistoryChart' ? 'rgba(37,99,235,.08)' : 'rgba(22,163,74,.08)',
          borderWidth: 2.5,
          pointBackgroundColor: canvasId === 'revenueHistoryChart' ? '#2563eb' : '#16a34a',
          pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7,
          fill: true, tension: 0.3,
        }],
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, datalabels: { display: false },
          tooltip: { callbacks: { label: (ctx) => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') } },
        },
        scales: {
          x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#64748b' } },
          y: { beginAtZero: true, grid: { color: '#f1f5f9' },
            ticks: { font: { size: 11 }, color: '#64748b', callback: (v) => 'Rp ' + (v >= 1000000 ? (v / 1000000).toFixed(0) + 'JT' : v >= 1000 ? (v / 1000).toFixed(0) + 'RB' : v) },
          },
        },
      },
    });
    return c;
  }

  function renderDoughnutChart(canvasId, data, chartObj) {
    if (chartObj) chartObj.destroy();
    if (!data.length) return null;
    return new Chart(document.getElementById(canvasId), {
      type: 'doughnut',
      data: {
        labels: data.map(d => d.label),
        datasets: [{
          data: data.map(d => d.value),
          backgroundColor: data.map((_, i) => colorSets[i % colorSets.length].bg),
          borderColor: data.map((_, i) => colorSets[i % colorSets.length].border),
          borderWidth: 2, hoverOffset: 6,
        }],
      },
      options: {
        responsive: true, maintainAspectRatio: true, cutout: '55%',
        plugins: {
          legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyleWidth: 10, font: { size: 11, weight: '600' } } },
          datalabels: {
            color: (ctx) => ['#1d4ed8','#166534','#92400e','#991b1b','#7c3aed','#0369a1'][ctx.dataIndex % 6],
            font: { weight: '800', size: 14 }, textShadowColor: '#fff', textShadowBlur: 6,
            formatter: (v) => v,
          },
        },
      },
    });
  }

  function updateAllCharts() {
    const rows = getGroupRows();
    updateStats();
    revChart = renderRevenueChart('revenueHistoryChart', buildRevenueHistory(rows), revChart);
    revCompChart = renderRevenueChart('revenueHistoryCompletedChart', buildRevenueHistoryCompleted(rows), revCompChart);
    paymentChartInst = renderDoughnutChart('paymentChart', buildPaymentMethods(rows), paymentChartInst);
    infoSourceChartInst = renderDoughnutChart('infoSourceChart', buildInfoSources(rows), infoSourceChartInst);
  }

  // Revenue chart date filters
  document.getElementById('revFrom').addEventListener('change', () => { updateAllCharts(); });
  document.getElementById('revTo').addEventListener('change', () => { updateAllCharts(); });
  document.getElementById('revCompFrom').addEventListener('change', () => { updateAllCharts(); });
  document.getElementById('revCompTo').addEventListener('change', () => { updateAllCharts(); });

  // Detail modal
  const backdrop = document.getElementById('detailBackdrop');
  const detailClose = document.getElementById('detailClose');

  function openDetail(r) {
    document.getElementById('dCustomer').textContent = r.customer_name;
    document.getElementById('dStatus').textContent = phaseLabel(r.status);
    document.getElementById('dStartDate').textContent = r.booking_date;
    document.getElementById('dEndDate').textContent = r.end_date;
    document.getElementById('dVehicle').textContent = r.vehicle;
    document.getElementById('dPrice').textContent = 'Rp ' + r.price;
    document.getElementById('dGroup').textContent = r.group_name || '-';
    document.getElementById('dContact').textContent = r.contact_number;
    document.getElementById('dPassengers').textContent = r.number_of_passengers;
    document.getElementById('dCountry').textContent = r.country_of_origin;
    document.getElementById('dPickup').textContent = r.pickup_location;
    document.getElementById('dPickupAddr').textContent = r.pickup_address_en;
    document.getElementById('dService').textContent = r.service_name;
    document.getElementById('dPayment').textContent = paymentLabel(r.payment_plan);
    document.getElementById('dDP').textContent = r.down_payment_amount;
    document.getElementById('dInvoice').textContent = r.invoice_number;
    document.getElementById('dTravel').innerHTML = r.travel_plans || '<span style="color:#64748b;">No travel plans.</span>';

    const btns = [];
    if (r.invoice_show_url) {
      btns.push('<a class="action-link action-link--primary" href="' + r.invoice_show_url + '">View Invoice</a>');
      btns.push('<a class="action-link" href="' + r.invoice_download_url + '">Download Invoice</a>');
    }
    if (r.whatsapp_url) {
      btns.push('<a class="action-link" href="' + r.whatsapp_url + '" target="_blank" rel="noopener">Chat WA</a>');
    }
    if (r.edit_url) {
      btns.push('<a class="action-link action-link--primary" href="' + r.edit_url + '">Edit Booking</a>');
    }
    document.getElementById('dActions').innerHTML = btns.length ? btns.join('') : '<span style="color:#64748b;">No actions available.</span>';
    backdrop.classList.add('open');
  }

  function closeDetail() { backdrop.classList.remove('open'); }
  detailClose.addEventListener('click', closeDetail);
  backdrop.addEventListener('click', (e) => { if (e.target === backdrop) closeDetail(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDetail(); });

  // Table
  let currentPage = 1;
  let perPage = 10;

  function getFilteredTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const fs = document.getElementById('filterStatus').value;
    const sort = document.getElementById('sortField').value;
    let data = getGroupRows().filter(r => {
      if (fs) {
        const grouped = { masuk: ['baru_masuk','',null], proses: ['konfirmasi','dijadwalkan'], cancel: ['cancelled','cancel','batal'], selesai: ['selesai_pelayanan','selesai_administrasi_fee'] };
        if (!grouped[fs]?.includes(r.status)) return false;
      }
      if (q) {
        const hay = (r.invoice_number + ' ' + r.customer_name + ' ' + r.vehicle + ' ' + r.mitra + ' ' + (r.group_name || '')).toLowerCase();
        if (!hay.includes(q)) return false;
      }
      return true;
    });
    data.sort((a, b) => {
      switch (sort) {
        case 'invoice': return a.invoice_number.localeCompare(b.invoice_number);
        case 'invoice_desc': return b.invoice_number.localeCompare(a.invoice_number);
        case 'amount': return a.amount - b.amount;
        case 'amount_desc': return b.amount - a.amount;
        case 'customer': return a.customer_name.localeCompare(b.customer_name);
        case 'customer_desc': return b.customer_name.localeCompare(a.customer_name);
        default: return 0;
      }
    });
    return data;
  }

  function renderTable() {
    const data = getFilteredTable();
    const totalPages = Math.max(1, Math.ceil(data.length / perPage));
    if (currentPage > totalPages) currentPage = totalPages;
    const start = (currentPage - 1) * perPage;
    const page = data.slice(start, start + perPage);
    const desktop = document.getElementById('desktopBody');
    const mobile = document.getElementById('mobileCards');

    if (!page.length) {
      desktop.innerHTML = '<tr><td colspan="7" class="empty-msg">No bookings found.</td></tr>';
      mobile.innerHTML = '<div class="empty-msg">No bookings found.</div>';
    } else {
      desktop.innerHTML = page.map((r, i) => `
        <tr>
          <td><span class="invoice-link" data-idx="${start + i}">${r.invoice_number}</span></td>
          <td>${r.customer_name}</td>
          <td>${r.group_name || '-'}</td>
          <td style="font-weight:600;">${fmt(r.amount)}</td>
          <td>${r.vehicle}</td>
          <td>${r.mitra}</td>
          <td><span class="badge ${badgeClass(r.status)}">${badgeLabel(r.status)}</span></td>
        </tr>
      `).join('');

      mobile.innerHTML = page.map((r, i) => `
        <div class="mobile-card">
          <div class="mobile-card-header">
            <div>
              <div class="mobile-card-title"><span class="invoice-link" data-idx="${start + i}">${r.invoice_number}</span></div>
              <div class="mobile-card-meta">${r.customer_name}</div>
            </div>
            <span class="badge ${badgeClass(r.status)}">${badgeLabel(r.status)}</span>
          </div>
          <div class="mobile-card-meta">${r.group_name ? r.group_name + ' &middot; ' : ''}${fmt(r.amount)} &middot; ${r.vehicle} &middot; ${r.mitra}</div>
        </div>
      `).join('');

      document.querySelectorAll('.invoice-link').forEach(el => {
        el.addEventListener('click', () => {
          const idx = parseInt(el.dataset.idx);
          openDetail(data[idx]);
        });
      });
    }

    document.getElementById('pageInfo').textContent = `Showing ${page.length} of ${data.length} bookings (page ${currentPage}/${totalPages})`;
    document.getElementById('btnPrev').disabled = currentPage <= 1;
    document.getElementById('btnNext').disabled = currentPage >= totalPages;
  }

  // Event listeners
  document.getElementById('filterGroup').addEventListener('change', () => { currentPage = 1; updateAllCharts(); renderTable(); });
  document.getElementById('perPage').addEventListener('change', e => { perPage = parseInt(e.target.value); currentPage = 1; renderTable(); });
  document.getElementById('searchInput').addEventListener('input', () => { currentPage = 1; renderTable(); });
  document.getElementById('filterStatus').addEventListener('change', () => { currentPage = 1; renderTable(); });
  document.getElementById('sortField').addEventListener('change', () => { currentPage = 1; renderTable(); });
  document.getElementById('btnPrev').addEventListener('click', () => { if (currentPage > 1) { currentPage--; renderTable(); } });
  document.getElementById('btnNext').addEventListener('click', () => { currentPage++; renderTable(); });

  // Initial render
  updateAllCharts();
  renderTable();
});
</script>

<script>
(function () {
  const wrap = document.getElementById('bdUserWrap');
  const btn = document.getElementById('bdUserBtn');
  const menu = document.getElementById('bdUserMenu');
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

  const menuBtn = document.getElementById('bdMenuBtn');
  if (menuBtn) {
    menuBtn.addEventListener('click', () => document.body.classList.toggle('mobile-sidebar-open'));
    document.addEventListener('click', (e) => {
      if (!document.body.classList.contains('mobile-sidebar-open')) return;
      const sidebar = document.querySelector('.booking-data-shell > .dashboard-sidebar');
      if (sidebar && !sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
        document.body.classList.remove('mobile-sidebar-open');
      }
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') document.body.classList.remove('mobile-sidebar-open');
    });
  }
})();
</script>
@endsection
