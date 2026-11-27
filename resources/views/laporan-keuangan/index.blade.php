@extends('layouts.app', ['title' => 'Laporan Keuangan'])

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
  .lkeuangan-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 1.25rem;
    align-items: start;
    min-height: calc(100vh - 6rem);
    padding-left: 252px;
  }

  .lkeuangan-main {
    display: grid;
    gap: 1.05rem;
    align-content: start;
    min-width: 0;
    min-height: 0;
    padding: 0 0 1rem;
  }

  /* ===== Sidebar (sama dengan dashboard) ===== */
  .lkeuangan-shell > .dashboard-sidebar {
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

  .lkeuangan-shell > .dashboard-sidebar::after {
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

  .lkeuangan-shell .dashboard-brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .45rem;
    padding: 0 .3rem 1rem;
    margin-bottom: .75rem;
    border-bottom: 1px solid rgba(255, 255, 255, .18);
  }

  .lkeuangan-shell .dashboard-brand img {
    width: 142px;
    height: auto;
    padding: .3rem .45rem;
    border-radius: 10px;
    background: #ffffff;
    object-fit: contain;
  }

  .lkeuangan-shell .dashboard-brand h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #ffffff;
  }

  .lkeuangan-shell .dashboard-brand .subtitle {
    font-size: .82rem;
    color: rgba(255, 255, 255, .82);
  }

  .lkeuangan-shell .dashboard-section-label {
    margin: .85rem .3rem .4rem;
    padding: 0;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .6);
  }

  .lkeuangan-shell .dashboard-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: .12rem;
  }

  .lkeuangan-shell .dashboard-menu a,
  .lkeuangan-shell .dashboard-menu button {
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

  .lkeuangan-shell .dashboard-menu a:hover,
  .lkeuangan-shell .dashboard-menu button:hover {
    background: rgba(255, 255, 255, .12);
    color: #ffffff;
  }

  .lkeuangan-shell .dashboard-menu a.active {
    background: #ffffff;
    color: #0b4fd6;
    box-shadow: 0 6px 14px rgba(4, 33, 90, .22);
  }

  .lkeuangan-shell .dashboard-menu .nav-icon {
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

  .lkeuangan-shell .dashboard-menu a.active .nav-icon {
    background: #e7f0ff;
    color: #0b4fd6;
  }


  /* ===== Topbar ===== */
  .lkeuangan-topbar {
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

  .lkeuangan-topbar-left { display: flex; align-items: center; gap: .6rem; min-width: 0; }
  .lkeuangan-topbar-right { display: flex; align-items: center; gap: .45rem; flex: 0 0 auto; }

  .lkeuangan-menu-btn {
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

  .lkeuangan-page-label {
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

  .lkeuangan-page-label i { color: #1565c0; font-size: .95rem; }

  .lkeuangan-icon-btn {
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

  .lkeuangan-icon-btn:hover { background: #f4f9ff; border-color: #bcd5f5; color: #0b4fd6; }

  .lkeuangan-user-wrap { position: relative; }

  .lkeuangan-user {
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

  .lkeuangan-user:hover { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }
  .lkeuangan-user[aria-expanded="true"] { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }

  .lkeuangan-avatar {
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

  .lkeuangan-user-meta { display: flex; flex-direction: column; line-height: 1.15; min-width: 0; overflow: hidden; }
  .lkeuangan-user-meta strong { max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .82rem; font-weight: 800; color: #0b2545; }
  .lkeuangan-user-meta span { font-size: .7rem; color: #64748b; text-transform: capitalize; }

  .lkeuangan-user > i { color: #94a3b8; font-size: .75rem; transition: transform .15s ease; }
  .lkeuangan-user[aria-expanded="true"] > i { transform: rotate(180deg); }

  .lkeuangan-user-menu {
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

  .lkeuangan-user-menu[hidden] { display: none; }

  .lkeuangan-user-menu-head {
    display: flex;
    flex-direction: column;
    gap: .15rem;
    padding: .55rem .7rem .6rem;
    margin-bottom: .35rem;
    border-bottom: 1px solid #eef2f8;
  }

  .lkeuangan-user-menu-head strong { font-size: .82rem; font-weight: 800; color: #0b2545; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .lkeuangan-user-menu-head span { font-size: .72rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  .lkeuangan-user-menu-item {
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

  .lkeuangan-user-menu-item i { font-size: .95rem; color: #64748b; }
  .lkeuangan-user-menu-item:hover { background: #f1f5fb; }
  .lkeuangan-user-menu-item--danger { color: #b91c1c; }
  .lkeuangan-user-menu-item--danger i { color: #b91c1c; }
  .lkeuangan-user-menu-item--danger:hover { background: #fef2f2; }

  /* ===== Hero ===== */
  .lkeuangan-hero {
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

  .lkeuangan-hero::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 5px;
    background: linear-gradient(90deg, #22c55e 0%, #84d94e 38%, #2f80ed 100%);
    opacity: .9;
  }

  .lkeuangan-hero-content { position: relative; z-index: 2; flex: 1 1 auto; min-width: 0; }

  .lkeuangan-eyebrow {
    margin-bottom: .3rem;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .14em;
    color: #1d6fe0;
  }

  .lkeuangan-hero h2 { margin: 0; font-size: 1.85rem; font-weight: 800; letter-spacing: -.03em; color: #0b2545; }

  .lkeuangan-hero-actions {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
    flex: 0 0 auto;
  }

  .lkeuangan-hero-actions .btn {
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

  .lkeuangan-hero-actions .btn-primary { background: #0b4fd6; color: #ffffff; border: 1px solid transparent; }
  .lkeuangan-hero-actions .btn-primary:hover { background: #093fa9; }
  .lkeuangan-hero-actions .btn:not(.btn-primary) { background: #ffffff; color: #0b4fd6; border-color: #cfe0f7; box-shadow: 0 8px 18px rgba(15, 23, 42, .1); }
  .lkeuangan-hero-actions .btn:not(.btn-primary):hover { background: #f6faff; }


  /* ===== Kartu statistik ===== */
  .lkeuangan-status-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .85rem; }

  .lkeuangan-status-card {
    display: flex;
    align-items: center;
    gap: .8rem;
    padding: .95rem 1rem;
    border-radius: 18px;
    border: 1px solid transparent;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .05);
  }

  .lkeuangan-status-icon {
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

  .lkeuangan-status-label { font-size: .82rem; font-weight: 700; color: #334155; }
  .lkeuangan-status-value { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; color: #0f172a; line-height: 1.15; }

  .lkeuangan-status-card--blue { background: #eff6ff; border-color: #dbeafe; }
  .lkeuangan-status-card--blue .lkeuangan-status-icon { color: #2563eb; }
  .lkeuangan-status-card--amber { background: #fffbeb; border-color: #fef3c7; }
  .lkeuangan-status-card--amber .lkeuangan-status-icon { color: #d97706; }
  .lkeuangan-status-card--green { background: #f0fdf4; border-color: #dcfce7; }
  .lkeuangan-status-card--green .lkeuangan-status-icon { color: #16a34a; }
  .lkeuangan-status-card--red { background: #fef2f2; border-color: #fee2e2; }
  .lkeuangan-status-card--red .lkeuangan-status-icon { color: #dc2626; }

  /* ===== Kartu pendapatan ===== */
  .lkeuangan-revenue-card {
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

  .lkeuangan-revenue-icon {
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

  .lkeuangan-revenue-body { position: relative; z-index: 2; min-width: 0; }
  .lkeuangan-revenue-body .label { font-size: .82rem; font-weight: 700; color: rgba(255, 255, 255, .82); margin-bottom: .15rem; }
  .lkeuangan-revenue-body .value { font-size: 1.75rem; font-weight: 800; letter-spacing: -.03em; line-height: 1.15; }

  .lkeuangan-revenue-bars {
    margin-left: auto;
    display: flex;
    align-items: flex-end;
    gap: 5px;
    height: 54px;
    opacity: .55;
  }

  .lkeuangan-revenue-bars span { width: 9px; border-radius: 4px; background: #ffffff; }


  /* ===== Kartu konten ===== */
  .lkeuangan-card {
    background: #ffffff;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
    min-width: 0;
  }

  .lkeuangan-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: .85rem;
  }

  .lkeuangan-card-head h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0b2545;
    letter-spacing: -.01em;
  }

  .lkeuangan-card-head h3 i { color: #1565c0; font-size: 1rem; }

  /* ===== Toolbar (filter) ===== */
  .lkeuangan-toolbar {
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

  .lkeuangan-field { display: flex; align-items: center; gap: .35rem; }
  .lkeuangan-toolbar label { margin: 0; font-size: .74rem; font-weight: 700; color: #64748b; white-space: nowrap; }

  .lkeuangan-toolbar select,
  .lkeuangan-toolbar input {
    padding: .45rem .6rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .8rem;
    outline: none;
  }

  .lkeuangan-toolbar select:focus,
  .lkeuangan-toolbar input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .14); }

  .lkeuangan-toolbar input[type="text"] { min-width: 180px; flex: 1; }

  .lkeuangan-toolbar button,
  .lkeuangan-toolbar a.btn-primary {
    padding: .45rem 1rem;
    border-radius: 10px;
    font-size: .8rem;
    font-weight: 700;
  }

  .lkeuangan-toolbar button { border: none; background: #0b4fd6; color: #ffffff; cursor: pointer; }
  .lkeuangan-toolbar button:hover { filter: brightness(1.05); }

  .lkeuangan-toolbar a.btn-primary { text-decoration: none; }

  /* ===== Tabel ===== */
  .lkeuangan-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border: 1px solid #e8f0fb; border-radius: 14px; }
  .lkeuangan-table { width: 100%; border-collapse: separate; border-spacing: 0; }

  .lkeuangan-table thead th {
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

  .lkeuangan-table tbody td {
    padding: .68rem .8rem;
    border-bottom: 1px solid #eef4fc;
    font-size: .85rem;
    color: #1f3a5f;
    vertical-align: middle;
    word-break: break-word;
  }

  .lkeuangan-table tbody tr:last-child td { border-bottom: none; }
  .lkeuangan-table tbody tr:hover td { background: #f9fbff; }

  .badge { display: inline-block; padding: .24rem .6rem; border-radius: 999px; font-size: .72rem; font-weight: 800; white-space: nowrap; }
  .badge-new { background: #eff6ff; color: #1d4ed8; }
  .badge-process { background: #fef3c7; color: #92400e; }
  .badge-done { background: #dcfce7; color: #166534; }
  .badge-cancel { background: #fee2e2; color: #991b1b; }

  .invoice-link { color: #1565c0; font-weight: 700; cursor: pointer; text-decoration: underline; text-underline-offset: 2px; }
  .invoice-link:hover { color: #0b4fd6; }

  .empty-msg { text-align: center; padding: 2rem; color: #94a3b8; font-size: .86rem; }

  .lkeuangan-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-top: .9rem;
    font-size: .8rem;
    color: #64748b;
  }

  .lkeuangan-pagination button {
    padding: .42rem .8rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .78rem;
    font-weight: 700;
    cursor: pointer;
  }

  .lkeuangan-pagination button:hover:not(:disabled) { background: #f2f7ff; border-color: #bcd5f5; }
  .lkeuangan-pagination button:disabled { opacity: .45; cursor: not-allowed; }

  /* ===== Modal ===== */
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
    .lkeuangan-shell { padding-left: 0; }
    .lkeuangan-shell > .dashboard-sidebar { display: none !important; }
    .lkeuangan-menu-btn { display: inline-flex; }
    .lkeuangan-status-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    body.mobile-sidebar-open .lkeuangan-shell > .dashboard-sidebar {
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
    .lkeuangan-topbar { padding: .55rem .7rem; border-radius: 16px; }
    .lkeuangan-page-label span { display: none; }
    .lkeuangan-user { padding: .3rem; border: none; background: transparent; }
    .lkeuangan-user-meta, .lkeuangan-user > i { display: none; }
    .lkeuangan-hero { flex-direction: column; align-items: stretch; gap: .9rem; min-height: 0; padding: 1.15rem 1.15rem 1.4rem; border-radius: 18px; }
    .lkeuangan-hero h2 { font-size: 1.45rem; }
    .lkeuangan-hero-actions { width: 100%; justify-content: space-between; }
    .lkeuangan-hero-actions .btn { flex: 1 1 auto; }
    .lkeuangan-status-grid { grid-template-columns: minmax(0, 1fr); }
    .lkeuangan-status-value { font-size: 1.35rem; }
    .lkeuangan-revenue-body .value { font-size: 1.45rem; }
    .lkeuangan-revenue-bars { display: none; }
    .lkeuangan-toolbar { flex-direction: column; align-items: stretch; }
    .lkeuangan-field { width: 100%; }
    .lkeuangan-toolbar input[type="text"] { min-width: 0; }
    .detail-backdrop.open { padding: 0; }
    .detail-modal { width: 100%; max-height: 100vh; height: 100%; border-radius: 0; }
    .detail-grid { grid-template-columns: 1fr 1fr; }
  }


</style>

@php
  $currentSort = request('sort', 'created_at');
  $currentDir = request('dir', 'desc');
  $sortParams = ['q' => request('q'), 'filter_pendapatan' => request('filter_pendapatan'), 'filter_status' => request('filter_status'), 'filter_group' => request('filter_group'), 'filter_country' => request('filter_country')];
@endphp

<div class="lkeuangan-shell">
  @include('partials.admin-sidebar')

  <main class="lkeuangan-main">
    <div class="lkeuangan-topbar">
      <div class="lkeuangan-topbar-left">
        <button class="lkeuangan-menu-btn" id="lkeuanganMenuBtn" type="button" aria-label="Buka menu">
          <i class="bi bi-list"></i>
        </button>
        <div class="lkeuangan-page-label"><i class="bi bi-file-earmark-bar-graph"></i><span>Laporan Keuangan</span></div>
      </div>
      <div class="lkeuangan-topbar-right">
        <a class="lkeuangan-icon-btn" href="{{ route('dashboard') }}" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard">
          <i class="bi bi-house-door"></i>
        </a>
        <div class="lkeuangan-user-wrap" id="lkeuanganUserWrap">
          <button class="lkeuangan-user" id="lkeuanganUserBtn" type="button" title="{{ auth()->user()->name ?? 'Pengguna' }}" aria-haspopup="true" aria-expanded="false" aria-controls="lkeuanganUserMenu">
            <div class="lkeuangan-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div class="lkeuangan-user-meta">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ str_replace('_', ' ', auth()->user()->role ?? 'user') }}</span>
            </div>
            <i class="bi bi-chevron-down"></i>
          </button>
          <div class="lkeuangan-user-menu" id="lkeuanganUserMenu" role="menu" aria-labelledby="lkeuanganUserBtn" hidden>
            <div class="lkeuangan-user-menu-head">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ auth()->user()->email ?? '' }}</span>
            </div>
            <a class="lkeuangan-user-menu-item" href="{{ route('accounts.show', auth()->id()) }}" role="menuitem">
              <i class="bi bi-person"></i> Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="lkeuangan-user-menu-item lkeuangan-user-menu-item--danger" role="menuitem">
                <i class="bi bi-box-arrow-right"></i> Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <section class="lkeuangan-hero">
      <div class="lkeuangan-hero-content">
        <div class="lkeuangan-eyebrow">LAPORAN KEUANGAN</div>
        <h2>Laporan Keuangan</h2>
      </div>
      <div class="lkeuangan-hero-actions">
        <form method="POST" action="{{ route('laporan-keuangan.sync') }}" onsubmit="return confirm('Sync semua data ke Google Spreadsheet?')">
          @csrf
          <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-arrow-up"></i> Sync ke Spreadsheet</button>
        </form>
        <button type="button" id="btnAturPajak" class="btn"><i class="bi bi-calculator"></i> Atur Pajak ({{ (int) $pajakRate }}%)</button>
      </div>
    </section>

    <div class="lkeuangan-status-grid">
      <div class="lkeuangan-status-card lkeuangan-status-card--blue">
        <div class="lkeuangan-status-icon"><i class="bi bi-cash-coin"></i></div>
        <div>
          <div class="lkeuangan-status-label">Pendapatan Kotor</div>
          <div class="lkeuangan-status-value">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
        </div>
      </div>
      <div class="lkeuangan-status-card lkeuangan-status-card--green">
        <div class="lkeuangan-status-icon"><i class="bi bi-check2-circle"></i></div>
        <div>
          <div class="lkeuangan-status-label">Total Pendapatan</div>
          <div class="lkeuangan-status-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
      </div>
      <div class="lkeuangan-status-card lkeuangan-status-card--amber">
        <div class="lkeuangan-status-icon"><i class="bi bi-receipt-cutoff"></i></div>
        <div>
          <div class="lkeuangan-status-label">Total Pajak ({{ (int) $pajakRate }}%)</div>
          <div class="lkeuangan-status-value">Rp {{ number_format($totalPajak, 0, ',', '.') }}</div>
        </div>
      </div>
      <div class="lkeuangan-status-card lkeuangan-status-card--red">
        <div class="lkeuangan-status-icon"><i class="bi bi-dash-circle"></i></div>
        <div>
          <div class="lkeuangan-status-label">Belum Diisi</div>
          <div class="lkeuangan-status-value">{{ $belumDiisi }} booking</div>
        </div>
      </div>
    </div>

    <div class="lkeuangan-revenue-card">
      <div class="lkeuangan-revenue-icon"><i class="bi bi-cash-coin"></i></div>
      <div class="lkeuangan-revenue-body">
        <div class="label">Total Revenue</div>
        <div class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
      </div>
      <div class="lkeuangan-revenue-bars" aria-hidden="true">
        <span style="height: 34%"></span>
        <span style="height: 52%"></span>
        <span style="height: 42%"></span>
        <span style="height: 68%"></span>
        <span style="height: 58%"></span>
        <span style="height: 88%"></span>
      </div>
    </div>


    <div class="lkeuangan-card">
      <div class="lkeuangan-card-head">
        <h3><i class="bi bi-funnel"></i>Filter</h3>
      </div>
      <form method="GET" action="{{ route('laporan-keuangan.index') }}" class="lkeuangan-toolbar">
        <div class="lkeuangan-field">
          <label for="q">Pencarian</label>
          <input id="q" name="q" type="text" value="{{ request('q') }}" placeholder="Nama, no. HP, atau invoice">
        </div>
        <div class="lkeuangan-field">
          <label for="start_date">Dari Tanggal</label>
          <input id="start_date" name="start_date" type="date" value="{{ request('start_date') }}">
        </div>
        <div class="lkeuangan-field">
          <label for="end_date">Sampai Tanggal</label>
          <input id="end_date" name="end_date" type="date" value="{{ request('end_date') }}">
        </div>
        <div class="lkeuangan-field">
          <label for="filter_pendapatan">Pendapatan</label>
          <select id="filter_pendapatan" name="filter_pendapatan">
            <option value="">Semua</option>
            <option value="kosong" {{ request('filter_pendapatan') === 'kosong' ? 'selected' : '' }}>Belum Diisi</option>
          </select>
        </div>
        <div class="lkeuangan-field">
          <label for="filter_status">Status</label>
          <select id="filter_status" name="filter_status">
            <option value="">Semua</option>
            @foreach(\App\Models\Booking::KANBAN_PHASES as $phase => $def)
              @foreach($def['statuses'] as $s)
                <option value="{{ $s }}" {{ request('filter_status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
              @endforeach
            @endforeach
          </select>
        </div>
        <div class="lkeuangan-field">
          <label for="filter_group">Group</label>
          <select id="filter_group" name="filter_group">
            <option value="">Semua</option>
            @foreach($groups as $g)
              <option value="{{ $g->id }}" {{ request('filter_group') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="lkeuangan-field">
          <label for="filter_country">Asal Negara</label>
          <select id="filter_country" name="filter_country">
            <option value="">Semua</option>
            @foreach($countries as $c)
              <option value="{{ $c }}" {{ request('filter_country') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit">Filter</button>
        @if(request()->hasAny(['filter_pendapatan', 'filter_status', 'filter_group', 'filter_country', 'start_date', 'end_date']))
          <a href="{{ route('laporan-keuangan.index') }}">Reset</a>
        @endif
      </form>

      <div class="lkeuangan-card-head" style="margin-top:1rem; justify-content:space-between;">
        <h3 style="margin:0;"><i class="bi bi-table"></i>All Bookings</h3>
      </div>

      <div class="lkeuangan-table-wrap">
        <table class="lkeuangan-table">
          <thead>
            <tr>
              @php
                $columns = [
                  'invoice_number' => 'No. Invoice',
                  'group' => 'Group',
                  'customer_name' => 'Customer',
                  'country_of_origin' => 'Asal Negara',
                  'status' => 'Status',
                  'booking_date' => 'Tanggal Mulai',
                  'end_date' => 'Tanggal Akhir',
                  'price' => 'Biaya',
                  'pendapatan' => 'Pendapatan',
                  'pajak' => 'Pajak',
                ];
              @endphp
              @foreach($columns as $key => $label)
                @if($key === 'pajak')
                  <th style="padding:.6rem .5rem; vertical-align:top; line-height:1.3;">Pajak<br><span style="font-weight:400; font-size:.75rem; color:#94a3b8;">({{ (int) $pajakRate }}%)</span></th>
                @elseif($key === 'end_date')
                  <th style="padding:.6rem .5rem; vertical-align:top; line-height:1.3;">{{ $label }}</th>
                @else
                  @php
                    $isCurrentSort = $currentSort === $key;
                    $nextDir = ($isCurrentSort && $currentDir === 'asc') ? 'desc' : 'asc';
                    $arrow = $isCurrentSort ? ($currentDir === 'asc' ? ' ▲' : ' ▼') : '';
                  @endphp
                  <th style="padding:.6rem .5rem; vertical-align:top; {{ in_array($key, ['price','pendapatan']) ? 'text-align:right;' : '' }}">
                    <a href="{{ route('laporan-keuangan.index', array_merge($sortParams, ['sort' => $key, 'dir' => $nextDir])) }}" style="color:inherit; text-decoration:none; display:inline-flex; align-items:center; gap:.2rem; font-weight:600;">
                      {{ $label }}{{ $arrow }}
                    </a>
                  </th>
                @endif
              @endforeach
              <th style="padding:.6rem .5rem; text-align:center; vertical-align:top;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bookings as $b)
            <tr style="border-bottom:1px solid var(--border);">
              <td style="padding:.6rem .5rem; font-weight:600;">
                @if($b->invoice)
                  <button type="button" class="btn-invoice-detail"
                    data-invoice_number="{{ $b->invoice->invoice_number }}"
                    data-customer_name="{{ $b->customer_name }}"
                    data-contact_number="{{ $b->contact_number }}"
                    data-price="{{ number_format($b->price ?? 0, 0, ',', '.') }}"
                    data-down_payment="{{ number_format($b->invoice->down_payment ?? 0, 0, ',', '.') }}"
                    data-remaining="{{ number_format(($b->price ?? 0) - ($b->invoice->down_payment ?? 0), 0, ',', '.') }}"
                    data-status="{{ $b->statusLabel() }}"
                    data-created_at="{{ $b->invoice->created_at ? $b->invoice->created_at->format('d M Y H:i') : '-' }}"
                    style="color:#3b82f6; text-decoration:underline; cursor:pointer; background:none; border:none; font-weight:600; font-size:.88rem; padding:0;">
                    {{ $b->invoice->invoice_number }}
                  </button>
                @else
                  -
                @endif
              </td>
              <td style="padding:.6rem .5rem;">{{ $b->group->name ?? '-' }}</td>
              <td style="padding:.6rem .5rem;">{{ $b->customer_name }}</td>
              <td style="padding:.6rem .5rem;">{{ $b->country_of_origin ?? '-' }}</td>
              <td style="padding:.6rem .5rem;">
                <span style="display:inline-block; padding:.15rem .5rem; border-radius:6px; font-size:.78rem;
                  @if(in_array($b->status, ['baru_masuk'])) background:#e0e7ff; color:#3730a3;
                  @elseif(in_array($b->status, ['konfirmasi','dijadwalkan'])) background:#fef3c7; color:#92400e;
                  @elseif(in_array($b->status, ['cancelled','cancel','batal'])) background:#fee2e2; color:#991b1b;
                  @elseif(in_array($b->status, ['selesai_pelayanan','selesai_administrasi_fee'])) background:#d1fae5; color:#065f46;
                  @else background:#f1f5f9; color:#475569;
                  @endif
                ">{{ $b->statusLabel() }}</span>
              </td>
              <td style="padding:.6rem .5rem;">{{ $b->booking_date ? $b->booking_date->format('d M Y') : '-' }}</td>
              <td style="padding:.6rem .5rem;">{{ $b->end_date ? $b->end_date->format('d M Y') : '-' }}</td>
              <td style="padding:.6rem .5rem; text-align:right;">{{ $b->price ? 'Rp ' . number_format($b->price, 0, ',', '.') : '-' }}</td>
              <td style="padding:.6rem .5rem; text-align:right; font-weight:600; color:{{ $b->pendapatan ? '#15803d' : '#94a3b8' }};">
                {{ $b->pendapatan ? 'Rp ' . number_format($b->pendapatan, 0, ',', '.') : '-' }}
              </td>
              <td style="padding:.6rem .5rem; text-align:right; font-weight:600; color:{{ ($b->pendapatan ?? 0) > 0 ? '#b45309' : '#94a3b8' }};">
                @php $pajak = ($b->pendapatan ?? 0) * $pajakRate / 100; @endphp
                {{ $pajak > 0 ? 'Rp ' . number_format($pajak, 0, ',', '.') : '-' }}
              </td>
              <td style="padding:.6rem .5rem; text-align:center;">
                <button type="button" class="btn-edit-pendapatan" data-id="{{ $b->id }}" data-value="{{ $b->pendapatan ?? '' }}" style="padding:.3rem .6rem; font-size:.8rem; background:#3b82f6; color:#fff; border:none; border-radius:6px; cursor:pointer;">Edit</button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="11" style="padding:2rem; text-align:center; color:#94a3b8;">Tidak ada data ditemukan.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="lkeuangan-pagination" style="margin-top:1.5rem;">
        {{ $bookings->links() }}
      </div>
    </div>
  </main>
</div>


<!-- Modal Atur Pajak -->
<div id="pajakModal" class="detail-backdrop">
  <div class="detail-modal">
    <div class="detail-head">
      <div>
        <h3>Pengaturan Pajak</h3>
        <div class="sub">Persentase pajak yang dikenakan pada seluruh pendapatan.</div>
      </div>
      <button type="button" class="detail-close" id="closePajakModal">x</button>
    </div>
    <div class="detail-body">
      <form id="pajakForm" method="POST" action="{{ route('laporan-keuangan.pajak.update') }}">
        @csrf
        @method('PUT')
        <div class="field" style="margin:0;">
          <label for="pajak_rate_input">Pajak Pendapatan (%)</label>
          <input id="pajak_rate_input" type="number" name="pajak_rate" min="0" max="100" step="0.5" value="{{ $pajakRate }}" placeholder="Contoh: 11" required style="width:100%; padding:.5rem; border:1px solid #dbe7f6; border-radius:10px; font-size:.9rem;">
        </div>
        <div style="display:flex; gap:.5rem; justify-content:flex-end; margin-top:.5rem;">
          <button type="button" id="closePajakBtn" class="action-link">Batal</button>
          <button type="submit" class="action-link action-link--primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Pendapatan -->
<div id="editPendapatanModal" class="detail-backdrop">
  <div class="detail-modal">
    <div class="detail-head">
      <div>
        <h3>Edit Pendapatan</h3>
        <div class="sub">Ubah nominal pendapatan booking.</div>
      </div>
      <button type="button" class="detail-close" id="closeModalBtn">x</button>
    </div>
    <div class="detail-body">
      <form id="editPendapatanForm" method="POST">
        @csrf
        @method('PUT')
        <div class="field" style="margin:0;">
          <label for="pendapatan_input">Pendapatan (IDR)</label>
          <input id="pendapatan_input" type="text" inputmode="numeric" name="pendapatan" placeholder="Masukkan nominal" required style="width:100%; padding:.5rem; border:1px solid #dbe7f6; border-radius:10px; font-size:.9rem;">
        </div>
        <div style="display:flex; gap:.5rem; justify-content:flex-end; margin-top:.5rem;">
          <button type="button" id="closeEditModalBtn" class="action-link">Batal</button>
          <button type="submit" class="action-link action-link--primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Invoice -->
<div id="invoiceDetailModal" class="detail-backdrop">
  <div class="detail-modal">
    <div class="detail-head">
      <div>
        <h3>Detail Invoice</h3>
        <div class="sub">Invoice information and quick actions.</div>
      </div>
      <button type="button" class="detail-close" id="closeInvoiceModal">x</button>
    </div>
    <div class="detail-body">
      <div style="display:flex; flex-direction:column; gap:.75rem; font-size:.9rem;">
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eef4fc; padding-bottom:.5rem;">
          <span style="color:#64748b;">No. Invoice</span>
          <span style="font-weight:600;" id="inv_number">-</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eef4fc; padding-bottom:.5rem;">
          <span style="color:#64748b;">Customer</span>
          <span style="font-weight:600;" id="inv_customer">-</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eef4fc; padding-bottom:.5rem;">
          <span style="color:#64748b;">Kontak</span>
          <span style="font-weight:600;" id="inv_contact">-</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eef4fc; padding-bottom:.5rem;">
          <span style="color:#64748b;">Status</span>
          <span id="inv_status">-</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eef4fc; padding-bottom:.5rem;">
          <span style="color:#64748b;">Total Biaya</span>
          <span style="font-weight:600;" id="inv_price">-</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid #eef4fc; padding-bottom:.5rem;">
          <span style="color:#64748b;">Down Payment</span>
          <span style="font-weight:600; color:#0369a1;" id="inv_dp">-</span>
        </div>
        <div style="display:flex; justify-content:space-between; padding-bottom:.5rem;">
          <span style="color:#64748b;">Sisa Bayar</span>
          <span style="font-weight:600; color:#b45309;" id="inv_remaining">-</span>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Dibuat</span>
          <span style="font-size:.85rem; color:#94a3b8;" id="inv_created">-</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const wrap = document.getElementById('lkeuanganUserWrap');
  const btn = document.getElementById('lkeuanganUserBtn');
  const menu = document.getElementById('lkeuanganUserMenu');
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

  const menuBtn = document.getElementById('lkeuanganMenuBtn');
  if (menuBtn) {
    menuBtn.addEventListener('click', () => document.body.classList.toggle('mobile-sidebar-open'));
    document.addEventListener('click', (e) => {
      if (!document.body.classList.contains('mobile-sidebar-open')) return;
      const sidebar = document.querySelector('.lkeuangan-shell > .dashboard-sidebar');
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
  var pajakModal = document.getElementById('pajakModal');
  var btnAturPajak = document.getElementById('btnAturPajak');
  var closePajakModal = document.getElementById('closePajakModal');
  var closePajakBtn = document.getElementById('closePajakBtn');

  btnAturPajak.addEventListener('click', function(){ pajakModal.classList.add('open'); });
  closePajakModal.addEventListener('click', function(){ pajakModal.classList.remove('open'); });
  closePajakBtn.addEventListener('click', function(){ pajakModal.classList.remove('open'); });
  pajakModal.addEventListener('click', function(e){ if(e.target === pajakModal) pajakModal.classList.remove('open'); });
})();

(function(){
  var modal = document.getElementById('editPendapatanModal');
  var form = document.getElementById('editPendapatanForm');
  var input = document.getElementById('pendapatan_input');

  document.querySelectorAll('.btn-edit-pendapatan').forEach(function(btn){
    btn.addEventListener('click', function(){
      var id = this.dataset.id;
      var val = this.dataset.value;
      form.action = '/bookings/' + id + '/pendapatan';
      input.value = val ? new Intl.NumberFormat('id-ID').format(val) : '';
      modal.classList.add('open');
    });
  });

  document.getElementById('closePajakModal').addEventListener('click', function(){ modal.classList.remove('open'); });
  document.getElementById('closeEditModalBtn').addEventListener('click', function(){ modal.classList.remove('open'); });
  modal.addEventListener('click', function(e){ if(e.target === modal) modal.classList.remove('open'); });

  input.addEventListener('input', function(){
    var v = this.value.replace(/\D/g,'');
    this.value = v ? new Intl.NumberFormat('id-ID').format(v) : '';
  });

  form.addEventListener('submit', function(){
    input.value = input.value.replace(/\D/g,'');
  });
})();

(function(){
  var invModal = document.getElementById('invoiceDetailModal');
  var closeInv = document.getElementById('closeInvoiceModal');

  document.querySelectorAll('.btn-invoice-detail').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.getElementById('inv_number').textContent = this.dataset.invoice_number || '-';
      document.getElementById('inv_customer').textContent = this.dataset.customer_name || '-';
      document.getElementById('inv_contact').textContent = this.dataset.contact_number || '-';
      document.getElementById('inv_status').textContent = this.dataset.status || '-';
      document.getElementById('inv_price').textContent = 'Rp ' + (this.dataset.price || '0');
      document.getElementById('inv_dp').textContent = 'Rp ' + (this.dataset.down_payment || '0');
      document.getElementById('inv_remaining').textContent = 'Rp ' + (this.dataset.remaining || '0');
      document.getElementById('inv_created').textContent = this.dataset.created_at || '-';
      invModal.classList.add('open');
    });
  });

  closeInv.addEventListener('click', function(){ invModal.classList.remove('open'); });
  invModal.addEventListener('click', function(e){ if(e.target === invModal) invModal.classList.remove('open'); });
})();
</script>
@endsection
