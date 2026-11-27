@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
@php
    // Format persentase pertumbuhan: 12.0 -> "+12%", -3.4 -> "-3,4%"
    $fmtGrowth = function ($value) {
        if ($value === null) {
            return null;
        }
        $decimals = fmod($value, 1) == 0 ? 0 : 1;

        return ($value > 0 ? '+' : '') . number_format($value, $decimals, ',', '') . '%';
    };
@endphp
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
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 1.25rem;
    align-items: start;
    min-height: calc(100vh - 7rem);
  }

  .dashboard-sidebar {
    background: linear-gradient(135deg, #0b3d91 0%, #1565c0 100%);
    color: #fff;
    padding: 1.5rem 1rem;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 260px;
    overflow-y: auto;
    z-index: 40;
    box-shadow: 4px 0 20px rgba(0,0,0,.08);
  }

  .dashboard-main,
  .dashboard-right {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    box-shadow: 0 8px 20px rgba(0,0,0,.06);
  }

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
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255,255,255,.15);
    margin-bottom: 1rem;
  }

  .dashboard-brand img {
    width: 36px;
    height: 36px;
    object-fit: contain;
  }

  .dashboard-brand h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    letter-spacing: -.02em;
    color: #fff;
  }

  .dashboard-brand .subtitle {
    font-size: .8rem;
    color: rgba(255,255,255,.85);
  }

  .dashboard-section-label {
    margin-top: 1rem;
    margin-bottom: .6rem;
    font-size: .75rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: rgba(255,255,255,.65);
    padding: 0 1rem;
  }

  .dashboard-menu {
    list-style: none;
    margin: 0;
    padding: 0 1rem;
    display: grid;
    gap: .2rem;
  }

  .dashboard-menu a,
  .dashboard-menu button {
    display: flex;
    align-items: center;
    gap: .7rem;
    width: 100%;
    padding: .65rem .9rem;
    border-radius: 10px;
    text-decoration: none;
    color: rgba(255,255,255,.9);
    background: transparent;
    font-weight: 600;
    font-size: .9rem;
    border: none;
    text-align: left;
    cursor: pointer;
    transition: background .15s ease;
  }

  .dashboard-menu a:hover,
  .dashboard-menu button:hover {
    background: rgba(255,255,255,.08);
    color: #fff;
  }

  .dashboard-menu a.active {
    background: #ffffff;
    color: #0b3d91;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
  }

  .dashboard-main {
    padding: 1.5rem 2rem 1.5rem calc(260px + 1rem);
    min-height: calc(100vh - 7rem);
    display: grid;
    gap: 1.5rem;
    align-content: start;
    background: transparent;
    border: none;
    box-shadow: none;
  }

  .dashboard-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: .9rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
    background: #ffffff;
    position: sticky;
    top: 0;
    z-index: 30;
    border-radius: 24px 24px 0 0;
  }

  .topbar-left {
    display: flex;
    align-items: center;
    gap: .9rem;
    min-width: 0;
    flex: 1 1 auto;
    overflow: hidden;
  }

  .topbar-menu-btn {
    display: none;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #f8fafc;
    color: #0b3d91;
    cursor: pointer;
  }

  .topbar-search {
    position: relative;
    flex: 1 1 auto;
    min-width: 0;
    max-width: 420px;
  }

  .topbar-search i {
    position: absolute;
    left: .85rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .95rem;
  }

  .topbar-search input {
    width: 100%;
    padding: .68rem 1rem .68rem 2.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    background: #f8fafc;
    font-size: .92rem;
    color: #0d2b52;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
  }

  .topbar-search input:focus {
    background: #fff;
    border-color: #1565c0;
    box-shadow: 0 0 0 4px rgba(21, 101, 192, .12);
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: .6rem;
    min-width: 0;
    flex: 0 1 auto;
  }

  .topbar-icon-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    background: #fff;
    color: #475569;
    font-size: 1.05rem;
    cursor: pointer;
    text-decoration: none;
    transition: background .15s ease, border-color .15s ease, color .15s ease;
  }

  .topbar-icon-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #0b3d91;
  }

  .topbar-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 999px;
    background: #dc2626;
    color: #fff;
    font-size: .65rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
  }

  .topbar-user {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .35rem .85rem .35rem .35rem;
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    background: #fff;
    cursor: pointer;
    transition: border-color .15s ease, box-shadow .15s ease;
  }

  .topbar-user:hover {
    border-color: #cbd5e1;
    box-shadow: 0 6px 16px rgba(15, 23, 42, .07);
  }

  .topbar-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0b3d91, #1976d2);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: .9rem;
    letter-spacing: .02em;
    flex-shrink: 0;
  }

  .topbar-user-meta {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
    min-width: 0;
    overflow: hidden;
  }

  .topbar-user-meta strong {
    font-size: .85rem;
    font-weight: 800;
    color: #0d2b52;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 140px;
  }

  .topbar-user-meta span {
    font-size: .72rem;
    color: #64748b;
    text-transform: capitalize;
  }

  .topbar-user > i {
    color: #94a3b8;
    font-size: .8rem;
  }

  .dashboard-hero {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem 2rem;
    border-radius: 24px;
    background: linear-gradient(115deg, #f3f8ff 0%, #e9f2ff 45%, #f9fcff 100%);
    border: 1px solid #dcebfb;
    box-shadow: 0 12px 28px rgba(13, 43, 82, .06);
  }

  .dashboard-hero .hero-photo {
    flex: 0 0 auto;
    width: 38%;
    min-width: 260px;
    height: 210px;
    border-radius: 18px;
    overflow: hidden;
    background: url('{{ asset('assets/img/gedung-sate-drive.png') }}') 60% 40% / cover no-repeat;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    margin-left: auto;
  }

  .dashboard-hero .hero-content {
    flex: 1 1 auto;
    min-width: 0;
  }

  .dashboard-hero .hero-eyebrow {
    font-size: .76rem;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #1565c0;
    margin-bottom: .4rem;
  }

  .dashboard-hero h2 {
    margin: 0;
    font-size: 2.05rem;
    letter-spacing: -.03em;
    color: #0b2545;
    font-weight: 800;
  }

  .dashboard-hero .subtitle {
    margin: 0 0 1.25rem;
    max-width: 48ch;
    color: #475569;
    font-size: .98rem;
    line-height: 1.5;
  }

  .hero-actions {
    display: flex;
    flex-direction: column;
    gap: .75rem;
    flex: 0 0 auto;
  }

  .dashboard-hero .btn {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .68rem 1.1rem;
    border-radius: 14px;
    font-size: .9rem;
    font-weight: 700;
    border: 1px solid transparent;
    white-space: nowrap;
    box-shadow: 0 8px 18px rgba(11, 61, 145, .16);
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    width: 100%;
    justify-content: center;
  }

  .dashboard-hero .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(11, 61, 145, .22);
  }

  .dashboard-hero .btn-primary {
    background: #1565c0;
    color: #ffffff;
  }

  .dashboard-hero .btn-primary:hover {
    background: #0f52a3;
  }

  .dashboard-hero .btn:not(.btn-primary) {
    background: #ffffff;
    color: #0b3d91;
    border-color: #cbd5e1;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
  }

  .dashboard-hero .btn:not(.btn-primary):hover {
    background: #f8fafc;
  }

  /* Topbar shortcut badge */
  .topbar-shortcut {
    font-size: .72rem;
    font-weight: 700;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: .12rem .5rem;
    margin-left: .5rem;
    white-space: nowrap;
  }

  /* Kartu metrik utama (Total Booking / Pendapatan Kotor) */
  .metrics-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.35fr);
    gap: 1rem;
  }

  .metric-card.metric-wide {
    flex-direction: row;
    align-items: center;
    gap: 1.1rem;
    padding: 1.35rem 1.5rem;
    min-height: 120px;
  }

  .metric-icon {
    flex: 0 0 auto;
    width: 54px;
    height: 54px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: rgba(255,255,255,.22);
    color: #ffffff;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.28);
  }

  .metric-card.metric-card--light {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
    color: #0f172a;
  }

  .metric-card--light .metric-icon {
    background: #e7f7ee;
    color: #059669;
    box-shadow: none;
  }

  .metric-body {
    min-width: 0;
    flex: 1 1 auto;
  }

  .metric-body h4 {
    margin: 0 0 .3rem;
    font-size: .88rem;
    font-weight: 700;
    opacity: .92;
  }

  .metric-row {
    display: flex;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
  }

  .metric-badge {
    display: inline-flex;
    align-items: center;
    gap: .2rem;
    padding: .18rem .5rem;
    border-radius: 999px;
    font-size: .74rem;
    font-weight: 800;
    background: #dcfce7;
    color: #15803d;
    line-height: 1.4;
  }

  .metric-badge.down {
    background: #fee2e2;
    color: #b91c1c;
  }

  .metric-badge.neutral {
    background: #e2e8f0;
    color: #475569;
  }

  .metric-card--light .metric-value {
    color: #0f172a;
  }

  .metric-watermark {
    position: absolute;
    right: 1rem;
    bottom: -.6rem;
    font-size: 6.5rem;
    line-height: 1;
    color: rgba(255,255,255,.14);
    pointer-events: none;
    z-index: 0;
  }

  .metric-bars {
    display: flex;
    align-items: flex-end;
    gap: .4rem;
    height: 62px;
    flex: 0 0 auto;
    z-index: 0;
  }

  .metric-bars span {
    width: 13px;
    border-radius: 6px 6px 3px 3px;
    background: linear-gradient(180deg, #60a5fa 0%, #2563eb 100%);
    display: block;
  }

  /* Kartu status (Masuk / Proses / Selesai / Cancel) */
  .status-cards {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: .9rem;
  }

  .status-card {
    display: flex;
    align-items: center;
    gap: .8rem;
    padding: .95rem 1rem;
    border-radius: 18px;
    border: 1px solid transparent;
  }

  .status-icon {
    flex: 0 0 auto;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    background: #ffffff;
    box-shadow: 0 4px 10px rgba(15, 23, 42, .08);
  }

  .status-label {
    font-size: .84rem;
    font-weight: 700;
    color: #334155;
  }

  .status-value {
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -.02em;
    color: #0f172a;
    line-height: 1.15;
  }

  .status-card--blue { background: #eff6ff; border-color: #dbeafe; }
  .status-card--blue .status-icon { color: #2563eb; }
  .status-card--amber { background: #fffbeb; border-color: #fef3c7; }
  .status-card--amber .status-icon { color: #d97706; }
  .status-card--green { background: #f0fdf4; border-color: #dcfce7; }
  .status-card--green .status-icon { color: #16a34a; }
  .status-card--red { background: #fef2f2; border-color: #fee2e2; }
  .status-card--red .status-icon { color: #dc2626; }

  /* Chart */
  .charts-row {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
    gap: 1rem;
    align-items: stretch;
  }

  .chart-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 1.1rem 1.25rem 1.25rem;
    box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .chart-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .9rem;
  }

  .chart-title {
    display: flex;
    align-items: center;
    gap: .55rem;
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: -.02em;
    color: #0f172a;
  }

  .chart-badge {
    width: 30px;
    height: 30px;
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .95rem;
    color: #ffffff;
    flex: 0 0 auto;
  }

  .chart-badge--green { background: #16a34a; }
  .chart-badge--blue { background: #2563eb; }

  .chart-sub {
    margin-top: .2rem;
    font-size: .82rem;
    color: #64748b;
  }

  .chart-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .4rem .7rem;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: .78rem;
    font-weight: 700;
    color: #475569;
    white-space: nowrap;
    flex: 0 0 auto;
  }

  .chart-body {
    position: relative;
    height: 210px;
    min-width: 0;
  }

  .donut-wrap {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1 1 auto;
    min-width: 0;
    flex-wrap: wrap;
  }

  .donut-canvas {
    position: relative;
    width: 168px;
    height: 168px;
    flex: 0 0 auto;
  }

  .donut-center {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    text-align: center;
  }

  .donut-center strong {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
  }

  .donut-center span {
    font-size: .74rem;
    font-weight: 700;
    color: #64748b;
  }

  .donut-legend {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: .55rem;
    flex: 1 1 130px;
    min-width: 0;
  }

  .donut-legend li {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto auto;
    align-items: center;
    gap: .5rem;
    font-size: .84rem;
    color: #334155;
  }

  .donut-legend .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
  }

  .donut-legend .lg-count {
    font-weight: 800;
    color: #0f172a;
    text-align: right;
    min-width: 2.4rem;
  }

  .donut-legend .lg-pct {
    font-weight: 700;
    color: #64748b;
    text-align: right;
    min-width: 3.4rem;
  }

  .dashboard-cards {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
    margin-top: 1rem;
  }

  .metric-card {
    border-radius: 20px;
    padding: 1.2rem 1rem;
    color: #fff;
    box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: space-between;
    text-align: left;
    position: relative;
    overflow: hidden;
  }

  .metric-card:not(.metric-wide) h4 {
    margin: 0 0 .2rem;
    font-size: .85rem;
    font-weight: 600;
    opacity: .9;
  }

  .metric-value {
    font-size: 2.2rem;
    font-weight: 800;
    letter-spacing: -.03em;
    line-height: 1.1;
  }

  .metric-note {
    margin-top: .2rem;
    font-size: .9rem;
    opacity: .88;
  }

  .summary-card {
    margin-top: 0;
    border-radius: 24px;
    border: 1px solid #e5e7eb;
    background: #fff;
    overflow: hidden;
  }

  .summary-card .summary-head {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
  }

  .summary-card .summary-head h3 {
    margin: 0;
    font-size: 1.1rem;
    letter-spacing: -.02em;
    color: #0f172a;
  }

  .summary-card .summary-body {
    padding: 1.25rem;
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
    padding: 1rem;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
    text-decoration: none;
    color: #0f172a;
    font-weight: 700;
    font-size: .95rem;
  }

  .quick-link:hover {
    border-color: #c7d2fe;
    box-shadow: 0 12px 20px rgba(15, 23, 42, .06);
    background: #f8fafc;
  }

  .quick-links--4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }

  .quick-link .ql-icon {
    flex: 0 0 auto;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    background: #dbeafe;
    color: #1d4ed8;
  }

  .quick-link .ql-icon.ql-icon--green {
    background: #dcfce7;
    color: #15803d;
  }

  .quick-link .ql-text {
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .quick-link .ql-arrow {
    flex: 0 0 auto;
    color: #64748b;
    font-weight: 700;
    transition: transform .15s ease, color .15s ease;
  }

  .quick-link:hover .ql-arrow {
    transform: translateX(3px);
    color: #1565c0;
  }

  .summary-title-row {
    display: flex;
    align-items: center;
    gap: .7rem;
  }

  .summary-icon {
    flex: 0 0 auto;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #0f172a;
    color: #facc15;
    font-size: 1rem;
  }

  .dashboard-right {
    display: grid;
    gap: 1rem;
  }

  .right-card {
    border-radius: 24px;
    border: 1px solid #e5e7eb;
    background: #fff;
    overflow: hidden;
  }

  .right-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
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
    color: #0f172a;
  }

  .schedule {
    display: grid;
    gap: .7rem;
    padding: 1rem;
  }

  .schedule-item {
    display: grid;
    grid-template-columns: 72px minmax(0, 1fr) auto;
    gap: .75rem;
    align-items: center;
    padding: 1rem;
    border-radius: 18px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e5e7eb;
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
    min-height: 72px;
    border-radius: 16px;
    background: linear-gradient(135deg, #0b3d91 0%, #1565c0 100%);
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

  .schedule-row {
    display: flex;
    align-items: center;
    gap: .35rem;
    color: #64748b;
    font-size: .82rem;
    line-height: 1.45;
    min-width: 0;
  }

  .schedule-row i {
    flex: 0 0 auto;
    font-size: .85rem;
    color: #94a3b8;
  }

  .schedule-row span {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    word-break: break-word;
  }

  .schedule-chevron {
    flex: 0 0 auto;
    color: #cbd5e1;
    font-size: 1.05rem;
    transition: color .15s ease, transform .15s ease;
  }

  .schedule-item:hover .schedule-chevron {
    color: #1565c0;
    transform: translateX(2px);
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
    border: 1px solid #e5e7eb;
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

  @media (max-width: 1100px) {
    .metrics-row { grid-template-columns: minmax(0, 1fr); }
    .charts-row { grid-template-columns: minmax(0, 1fr); }
    .status-cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .quick-links--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }

  @media (max-width: 768px) {
    @if(auth()->user()?->role === 'super_admin')
    .dashboard-sidebar { display: none !important; }
    @endif
    .dashboard-topbar {
      padding: .7rem .85rem;
      gap: .6rem;
      border-radius: 16px 16px 0 0;
    }
    .topbar-menu-btn { display: inline-flex; }
    .topbar-search { max-width: none; }
    .topbar-search input { padding: .6rem .85rem .6rem 2.3rem; font-size: .88rem; }
    .topbar-right .topbar-icon-btn:first-child { display: none; }
    .topbar-user { padding: .3rem; border: none; background: transparent; }
    .topbar-user-meta, .topbar-user > i { display: none; }
    body.mobile-sidebar-open .dashboard-sidebar {
      display: block !important;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: 270px;
      max-height: none;
      z-index: 120;
      overflow-y: auto;
      box-shadow: 0 0 40px rgba(0,0,0,.35);
      border-radius: 0;
      padding-top: 1.5rem;
    }
    body.mobile-sidebar-open::after {
      content: "";
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .5);
      z-index: 110;
    }
    .dashboard-cards {
      grid-template-columns: minmax(0, 30%) minmax(0, 70%);
      gap: .4rem;
    }
    .metric-card {
      padding: .8rem .6rem;
      border-radius: 16px;
    }
    .metric-card:not(.metric-wide) h4 {
      font-size: .75rem;
      margin-bottom: .2rem;
    }
    .metric-value {
      font-size: 1.5rem;
    }
    .metric-note { display: none; }

    .dashboard-hero {
      flex-direction: column;
      padding: 1.25rem 1.25rem;
      gap: 1rem;
    }
    .dashboard-hero .hero-photo {
      width: 100%;
      opacity: .16;
      -webkit-mask-image: none;
      mask-image: none;
    }
    .dashboard-hero h2 { font-size: 1.5rem; }
    .dashboard-hero .subtitle { max-width: none; font-size: .92rem; }
    .hero-actions { width: 100%; flex-wrap: wrap; }
    .hero-actions .btn { flex: 1 1 auto; justify-content: center; }

    .metrics-row { gap: .6rem; }
    .metric-card.metric-wide {
      padding: 1rem;
      gap: .8rem;
      min-height: 0;
    }
    .metric-icon {
      width: 44px;
      height: 44px;
      border-radius: 13px;
      font-size: 1.2rem;
    }
    .metric-watermark, .metric-bars { display: none; }
    .metric-body h4 { font-size: .8rem; }
    .metric-badge { font-size: .68rem; padding: .14rem .42rem; }

    .status-cards { gap: .6rem; }
    .status-card { padding: .75rem .7rem; gap: .6rem; }
    .status-icon { width: 36px; height: 36px; font-size: 1rem; }
    .status-label { font-size: .76rem; }
    .status-value { font-size: 1.25rem; }

    .chart-card { padding: .95rem 1rem 1.05rem; }
    .chart-head { flex-direction: column; gap: .5rem; }
    .chart-body { height: 190px; }
    .donut-wrap { justify-content: center; }

    .quick-links--4 { grid-template-columns: minmax(0, 1fr); }

    .schedule-item {
      grid-template-columns: 56px minmax(0, 1fr) auto;
      padding: .8rem;
      gap: .6rem;
    }
    .dashboard-main {
      min-height: auto;
      padding: 0;
      gap: .15rem;
      background: transparent;
      border: none;
      border-radius: 0;
      box-shadow: none;
      backdrop-filter: none;
    }
    .dashboard-shell {
      gap: .4rem;
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
      border: 1px solid #e5e7eb;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,.04);
    }
    .dashboard-main {
      padding: 1rem;
    }
  }

  /* ===== Booking Detail Modal ===== */
  .modal-backdrop {
    display: none !important;
    position: fixed !important;
    inset: 0 !important;
    z-index: 1000 !important;
    background: rgba(15, 23, 42, 0.45) !important;
    backdrop-filter: blur(4px) !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 1.5rem !important;
    animation: modalFadeIn .25s ease;
  }

  .modal-backdrop.open {
    display: flex !important;
  }

  @keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  .modal-card {
    width: 100%;
    max-width: 780px;
    max-height: 85vh;
    overflow: hidden;
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 24px 48px rgba(15, 23, 42, 0.18);
    display: flex;
    flex-direction: column;
    animation: modalSlideUp .25s ease;
  }

  @keyframes modalSlideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }

  .modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex: 0 0 auto;
  }

  .modal-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -.02em;
  }

  .modal-close {
    flex: 0 0 auto;
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: 50%;
    border: 1px solid #e6eef9;
    background: #f6faff;
    color: #475569;
    font-size: 1rem;
    line-height: 1;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background .15s ease, color .15s ease, border-color .15s ease, transform .15s ease;
  }

  .modal-close i {
    font-size: 1.05rem;
    line-height: 1;
  }

  .modal-close:hover {
    background: #e8f1ff;
    border-color: #c9ddfa;
    color: #0b4fd6;
    transform: rotate(90deg);
  }

  .modal-close:active {
    transform: scale(.94);
  }

  .modal-close:focus-visible {
    outline: 2px solid #93c5fd;
    outline-offset: 2px;
  }

  /* ===== Popup Kalender Booking ===== */
  .calendar-modal-card {
    max-width: 900px;
  }

  .calendar-header {
    padding: 1.1rem 1.35rem;
    background: linear-gradient(120deg, #f4f9ff 0%, #ffffff 70%);
  }

  .calendar-heading {
    display: flex;
    align-items: center;
    gap: .75rem;
    min-width: 0;
  }

  .calendar-heading-icon {
    flex: 0 0 auto;
    width: 42px;
    height: 42px;
    border-radius: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.1rem;
    background: linear-gradient(135deg, #1d6ef5, #0b4fd6);
    box-shadow: 0 8px 18px rgba(11, 79, 214, .28);
  }

  .calendar-subtitle {
    margin-top: .15rem;
    font-size: .78rem;
    font-weight: 600;
    color: #64748b;
  }

  .calendar-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    padding: 0 1.35rem 1.1rem;
  }

  .calendar-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding: .95rem 0 .85rem;
  }

  .calendar-nav {
    display: flex;
    align-items: center;
    gap: .3rem;
    padding: .22rem;
    border: 1px solid #eaf2fc;
    border-radius: 12px;
    background: #f8fbff;
  }

  .calendar-nav-btn,
  .calendar-today-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .15rem;
    height: 32px;
    border: 1px solid transparent;
    border-radius: 9px;
    background: transparent;
    color: #475569;
    font-size: .8rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .15s ease, color .15s ease, border-color .15s ease, box-shadow .15s ease;
  }

  .calendar-nav-btn { width: 32px; }

  .calendar-today-btn { padding: 0 .7rem; }

  .calendar-nav-btn:hover,
  .calendar-today-btn:hover {
    background: #ffffff;
    border-color: #d9e8fb;
    color: #0b4fd6;
    box-shadow: 0 2px 6px rgba(15, 23, 42, .07);
  }

  .calendar-nav-btn:focus-visible,
  .calendar-today-btn:focus-visible {
    outline: 2px solid #93c5fd;
    outline-offset: 1px;
  }

  .calendar-month-label {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0b2545;
    letter-spacing: -.02em;
  }

  .calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 6px;
    padding: .45rem 0;
    margin-bottom: .5rem;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    font-size: .7rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .calendar-weekdays span { text-align: center; }

  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 6px;
  }

  .calendar-day {
    display: flex;
    flex-direction: column;
    gap: .25rem;
    min-height: 92px;
    padding: .4rem .35rem;
    border: 1px solid #eef4fb;
    border-radius: 12px;
    background: #ffffff;
    transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
  }

  .calendar-day:not(.is-empty):hover {
    border-color: #cfe3fb;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .06);
  }

  .calendar-day.is-empty { background: transparent; border-color: transparent; }

  .calendar-day.has-booking { background: #fcfdff; }

  .calendar-day.is-today {
    border-color: #93c5fd;
    background: linear-gradient(180deg, #f2f8ff, #ffffff);
    box-shadow: inset 0 0 0 1px rgba(147, 197, 253, .55);
  }

  .calendar-day-num {
    align-self: flex-start;
    min-width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    font-size: .74rem;
    font-weight: 700;
    color: #475569;
  }

  .calendar-day.is-weekend .calendar-day-num { color: #94a3b8; }

  .calendar-day.is-today .calendar-day-num {
    border-radius: 50%;
    background: #0b4fd6;
    color: #ffffff;
    box-shadow: 0 3px 8px rgba(11, 79, 214, .3);
  }

  .calendar-event {
    display: block;
    width: 100%;
    padding: .22rem .4rem;
    border: none;
    border-left: 3px solid transparent;
    border-radius: 7px;
    font-family: inherit;
    font-size: .67rem;
    font-weight: 700;
    text-align: left;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
    transition: transform .12s ease, filter .12s ease;
  }

  .calendar-event--masuk { background: #e5efff; color: #0b4fd6; border-left-color: #3b82f6; }
  .calendar-event--proses { background: #fff5dd; color: #92400e; border-left-color: #f59e0b; }
  .calendar-event--selesai { background: #e3fbec; color: #166534; border-left-color: #22c55e; }
  .calendar-event--cancel { background: #ffe9e9; color: #991b1b; border-left-color: #ef4444; }

  .calendar-event:hover { filter: brightness(.97); transform: translateX(1px); }

  .calendar-more {
    padding-left: .25rem;
    font-size: .64rem;
    font-weight: 700;
    color: #64748b;
  }

  .calendar-empty {
    grid-column: 1 / -1;
    padding: 2.5rem 0;
    text-align: center;
    font-size: .85rem;
    font-weight: 600;
    color: #94a3b8;
  }

  .calendar-footer {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: .5rem .9rem;
    margin-top: .9rem;
    padding-top: .85rem;
    border-top: 1px solid #f1f5f9;
  }

  .calendar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: .7rem;
    font-size: .72rem;
    color: #64748b;
  }

  .calendar-legend span {
    display: inline-flex;
    align-items: center;
    gap: .32rem;
    font-weight: 600;
  }

  .calendar-legend i {
    width: 9px;
    height: 9px;
    border-radius: 3px;
  }

  .calendar-hint {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .72rem;
    font-weight: 600;
    color: #94a3b8;
  }

  @media (max-width: 640px) {
    .calendar-modal-card { max-width: 100%; }
    .calendar-header { padding: .9rem 1rem; }
    .calendar-heading-icon { width: 36px; height: 36px; border-radius: 11px; font-size: .95rem; }
    .calendar-subtitle { font-size: .72rem; }
    .calendar-body { padding: 0 .65rem .9rem; }
    .calendar-toolbar { padding: .7rem 0 .7rem; flex-wrap: wrap; }
    .calendar-month-label { font-size: .95rem; }
    .calendar-nav { padding: .18rem; }
    .calendar-nav-btn { width: 30px; height: 30px; }
    .calendar-today-btn { height: 30px; padding: 0 .55rem; font-size: .74rem; }
    .calendar-weekdays { gap: 4px; font-size: .6rem; padding: .35rem 0; }
    .calendar-grid { gap: 4px; }
    .calendar-day { min-height: 52px; padding: .25rem .2rem; gap: .18rem; border-radius: 9px; }
    .calendar-day-num { align-self: center; height: 19px; min-width: 19px; font-size: .66rem; border-radius: 6px; }
    .calendar-event { height: 6px; padding: 0; font-size: 0; border-radius: 999px; border-left: none; }
    .calendar-more { font-size: .58rem; padding: 0; text-align: center; }
    .calendar-footer { gap: .4rem; }
    .calendar-legend { gap: .5rem; font-size: .64rem; }
    .calendar-hint { font-size: .64rem; }
  }

  .modal-scroll {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overscroll-behavior: contain;
    padding: 1.25rem 1.5rem;
  }

  .modal-body,
  .modal-scroll #booking_modal_body {
    padding: 0;
    flex: 0 0 auto;
    overflow: visible;
  }

  .modal-scroll .modal-section {
    padding: 0;
  }

  .modal-grid {
    display: grid;
    gap: .75rem;
  }

  .modal-row {
    display: grid;
    grid-template-columns: 160px 1fr;
    gap: .75rem;
    align-items: baseline;
    padding: .6rem .75rem;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
  }

  .modal-row .label {
    font-size: .85rem;
    font-weight: 600;
    color: #64748b;
    white-space: nowrap;
  }

  .modal-row .value {
    font-size: .9rem;
    color: #0f172a;
    word-break: break-word;
  }

  .modal-section {
    padding: 0 1.5rem;
    flex: 0 0 auto;
  }

  .modal-section .subtitle {
    font-size: .82rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin: 0 0 .6rem;
  }

  .wysiwyg-view {
    flex: 0 0 auto;
  }

  .w-content {
    font-size: .9rem;
    color: #0f172a;
    line-height: 1.6;
    white-space: pre-wrap;
  }

  .w-content p,
  .w-content div,
  .w-content li {
    margin: 0 0 .5rem;
  }

  .w-content p:last-child,
  .w-content div:last-child,
  .w-content li:last-child {
    margin-bottom: 0;
  }

  .modal-actions {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    flex: 0 0 auto;
    display: flex;
    gap: .75rem;
    justify-content: flex-end;
  }

  /* ===== Responsive ===== */
  @media (max-width: 768px) {
    .modal-backdrop {
      padding: 1rem;
    }
    .modal-card {
      max-height: 90vh;
      border-radius: 20px;
    }
    .modal-row {
      grid-template-columns: 110px 1fr;
      gap: .6rem;
      padding: .5rem .65rem;
    }
    .modal-header,
    .modal-body,
    .modal-actions {
      padding-left: 1rem;
      padding-right: 1rem;
    }
  }

  /* =========================================================
     REDESIGN: layout utama, sidebar, topbar, hero banner
     ========================================================= */
  body {
    background: #eef5ff;
  }

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

  .container {
    max-width: 100% !important;
    width: 100%;
    margin: 0 auto;
    padding: 1rem 1.25rem .5rem;
  }

  .dashboard-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 352px;
    gap: 1.25rem;
    align-items: start;
    min-height: calc(100vh - 6rem);
    padding-left: 252px;
  }

  .dashboard-main {
    display: grid;
    gap: 1.05rem;
    align-content: start;
    min-width: 0;
    min-height: 0;
    padding: 0 0 1rem;
    background: transparent;
    border: none;
    box-shadow: none;
  }

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

  .dashboard-topbar {
    position: sticky;
    top: .5rem;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .85rem;
    padding: .65rem .85rem;
    border: 1px solid #e8f0fb;
    border-radius: 18px;
    background: rgba(255, 255, 255, .94);
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 22px rgba(13, 43, 82, .05);
  }

  .topbar-menu-btn {
    display: none;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: 1px solid #e6eef9;
    border-radius: 12px;
    background: #f6faff;
    color: #0b4fd6;
    cursor: pointer;
    flex: 0 0 auto;
  }

  .topbar-search {
    position: relative;
    flex: 1 1 420px;
    min-width: 0;
    max-width: 470px;
  }

  .topbar-search i {
    position: absolute;
    left: .95rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .95rem;
  }

  .topbar-search input {
    width: 100%;
    padding: .6rem 5.4rem .6rem 2.45rem;
    border: 1px solid #e6eef9;
    border-radius: 999px;
    background: #f8fbff;
    color: #0b2545;
    font-size: .88rem;
    outline: none;
    transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
  }

  .topbar-search input:focus {
    background: #ffffff;
    border-color: #7aa7e8;
    box-shadow: 0 0 0 4px rgba(21, 101, 192, .1);
  }

  .topbar-shortcut {
    position: absolute;
    right: .4rem;
    top: 50%;
    transform: translateY(-50%);
    margin: 0;
    padding: .18rem .45rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #ffffff;
    color: #475569;
    font-size: .72rem;
    font-weight: 700;
    white-space: nowrap;
  }

  .topbar-left {
    overflow: visible;
  }

  .topbar-search-results {
    position: absolute;
    top: calc(100% + .45rem);
    left: 0;
    right: 0;
    z-index: 80;
    max-height: 340px;
    overflow-y: auto;
    padding: .45rem;
    border: 1px solid #e8f0fb;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 18px 36px rgba(13, 43, 82, .14);
  }

  .topbar-search-results[hidden] {
    display: none;
  }

  .search-result {
    display: flex;
    flex-direction: column;
    gap: .1rem;
    width: 100%;
    padding: .55rem .65rem;
    border: none;
    border-radius: 10px;
    background: transparent;
    text-align: left;
    cursor: pointer;
  }

  .search-result:hover,
  .search-result:focus-visible {
    background: #f2f8ff;
    outline: none;
  }

  .search-result-title {
    font-size: .85rem;
    font-weight: 700;
    color: #0b2545;
  }

  .search-result-meta {
    font-size: .74rem;
    color: #64748b;
  }

  .search-result-empty {
    padding: .7rem .65rem;
    font-size: .8rem;
    color: #64748b;
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex: 0 1 auto;
  }

  .topbar-icon-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: 1px solid #e8eef7;
    border-radius: 50%;
    background: #ffffff;
    color: #334155;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    transition: background .15s ease, border-color .15s ease, color .15s ease;
  }

  .topbar-icon-btn:hover {
    background: #f3f8ff;
    border-color: #cbd5e1;
    color: #0b4fd6;
  }

  .topbar-badge {
    position: absolute;
    top: 2px;
    right: 3px;
    width: 9px;
    height: 9px;
    min-width: 0;
    padding: 0;
    border-radius: 50%;
    border: 2px solid #ffffff;
    background: #ef4444;
  }

  .topbar-user {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .3rem .7rem .3rem .35rem;
    border: 1px solid #e8eef7;
    border-radius: 999px;
    background: #ffffff;
    cursor: pointer;
    transition: border-color .15s ease, box-shadow .15s ease;
  }

  .topbar-user:hover {
    border-color: #cbd5e1;
    box-shadow: 0 6px 16px rgba(15, 23, 42, .07);
  }

  .topbar-avatar {
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
    letter-spacing: .02em;
    flex-shrink: 0;
  }

  .topbar-user-meta {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
    min-width: 0;
    overflow: hidden;
  }

  .topbar-user-meta strong {
    max-width: 130px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: .82rem;
    font-weight: 800;
    color: #0b2545;
  }

  .topbar-user-meta span {
    font-size: .7rem;
    color: #64748b;
    text-transform: capitalize;
  }

  .topbar-user > i {
    color: #94a3b8;
    font-size: .75rem;
    transition: transform .15s ease;
  }

  .topbar-user-wrap {
    position: relative;
  }

  .topbar-user {
    font: inherit;
    text-align: left;
  }

  .topbar-user[aria-expanded="true"] {
    border-color: #cbd5e1;
    box-shadow: 0 6px 16px rgba(15, 23, 42, .07);
  }

  .topbar-user[aria-expanded="true"] > i {
    transform: rotate(180deg);
  }

  .topbar-user-menu {
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

  .topbar-user-menu[hidden] {
    display: none;
  }

  .topbar-user-menu-head {
    display: flex;
    flex-direction: column;
    gap: .15rem;
    padding: .55rem .7rem .6rem;
    margin-bottom: .35rem;
    border-bottom: 1px solid #eef2f8;
  }

  .topbar-user-menu-head strong {
    font-size: .82rem;
    font-weight: 800;
    color: #0b2545;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .topbar-user-menu-head span {
    font-size: .72rem;
    color: #64748b;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .topbar-user-menu-item {
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

  .topbar-user-menu-item i {
    font-size: .95rem;
    color: #64748b;
  }

  .topbar-user-menu-item:hover {
    background: #f1f5fb;
  }

  .topbar-user-menu-item--danger {
    color: #b91c1c;
  }

  .topbar-user-menu-item--danger i {
    color: #b91c1c;
  }

  .topbar-user-menu-item--danger:hover {
    background: #fef2f2;
  }

  .dashboard-hero {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    min-height: 166px;
    padding: 1.5rem 1.65rem;
    border-radius: 22px;
    border: 1px solid #dbeafe;
    overflow: hidden;
    background:
      linear-gradient(90deg, rgba(238, 245, 255, .97) 0%, rgba(238, 245, 255, .92) 34%, rgba(238, 245, 255, .38) 60%, rgba(238, 245, 255, 0) 88%),
      url('{{ asset('assets/img/gedung-sate-drive.png') }}') right 42% / cover no-repeat;
    box-shadow: 0 14px 30px rgba(13, 43, 82, .08);
  }

  .dashboard-hero::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 5px;
    background: linear-gradient(90deg, #22c55e 0%, #84d94e 38%, #2f80ed 100%);
    opacity: .9;
  }

  .dashboard-hero .hero-photo {
    display: none;
  }

  .dashboard-hero .hero-content {
    position: relative;
    z-index: 2;
    flex: 1 1 auto;
    min-width: 0;
    max-width: 560px;
  }

  .dashboard-hero .hero-eyebrow {
    margin-bottom: .3rem;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .14em;
    color: #1d6fe0;
  }

  .dashboard-hero h2 {
    margin: 0;
    font-size: 1.85rem;
    font-weight: 800;
    letter-spacing: -.03em;
    color: #0b2545;
  }

  .dashboard-hero .subtitle {
    margin: .4rem 0 0;
    max-width: 46ch;
    font-size: .93rem;
    line-height: 1.5;
    color: #3c5878;
  }

  .hero-actions {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: .6rem;
    flex: 0 0 auto;
  }

  .dashboard-hero .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    width: auto;
    padding: .68rem 1.05rem;
    border-radius: 12px;
    border: 1px solid transparent;
    font-size: .88rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    box-shadow: 0 8px 18px rgba(11, 61, 145, .16);
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }

  .dashboard-hero .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(11, 61, 145, .22);
  }

  .dashboard-hero .btn-primary {
    background: #0b4fd6;
    color: #ffffff;
  }

  .dashboard-hero .btn-primary:hover {
    background: #093fa9;
  }

  .dashboard-hero .btn:not(.btn-primary) {
    background: #ffffff;
    color: #0b4fd6;
    border-color: #cfe0f7;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .1);
  }

  .dashboard-hero .btn:not(.btn-primary):hover {
    background: #f6faff;
  }

  /* =========================================================
     REDESIGN: kartu statistik, chart, quick links, jadwal
     ========================================================= */
  .metrics-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.45fr);
    gap: 1rem;
  }

  .metric-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: space-between;
    gap: .25rem;
    overflow: hidden;
    padding: 1.05rem 1rem;
    border-radius: 18px;
    color: #ffffff;
    box-shadow: 0 10px 22px rgba(13, 43, 82, .1);
  }

  .metric-card.metric-wide {
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    min-height: 108px;
    padding: 1.1rem 1.25rem;
  }

  .metric-icon {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: rgba(255, 255, 255, .18);
    color: #ffffff;
    font-size: 1.35rem;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .22);
  }

  .metric-card.metric-card--light {
    border: 1px solid #e8f0fb;
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 10px 22px rgba(13, 43, 82, .06);
  }

  .metric-card--light .metric-icon {
    background: #e7f7ee;
    color: #16a34a;
    box-shadow: none;
  }

  .metric-body {
    flex: 1 1 auto;
    min-width: 0;
  }

  .metric-body h4 {
    margin: 0 0 .2rem;
    font-size: .83rem;
    font-weight: 700;
    color: inherit;
    opacity: .9;
  }

  .metric-row {
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .metric-value {
    font-size: 1.95rem;
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -.03em;
  }

  .metric-badge {
    display: inline-flex;
    align-items: center;
    gap: .15rem;
    padding: .2rem .5rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, .24);
    color: #ffffff;
    font-size: .72rem;
    font-weight: 800;
    line-height: 1.4;
  }

  .metric-card--light .metric-badge {
    background: #dcfce7;
    color: #15803d;
  }

  .metric-note {
    margin-top: .2rem;
    font-size: .78rem;
    opacity: .85;
  }

  .metric-watermark {
    position: absolute;
    right: .9rem;
    bottom: -.55rem;
    z-index: 0;
    color: rgba(255, 255, 255, .14);
    font-size: 5.6rem;
    line-height: 1;
    pointer-events: none;
  }

  .metric-bars {
    display: flex;
    align-items: flex-end;
    gap: .35rem;
    height: 58px;
    flex: 0 0 auto;
  }

  .metric-bars span {
    display: block;
    width: 12px;
    border-radius: 5px 5px 2px 2px;
    background: linear-gradient(180deg, #8ec5fb 0%, #2f80ed 100%);
  }

  .status-cards {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: .85rem;
  }

  .status-card {
    display: flex;
    align-items: center;
    gap: .7rem;
    padding: .85rem .9rem;
    border-radius: 16px;
    border: 1px solid transparent;
  }

  .status-icon {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #ffffff;
    font-size: 1.1rem;
    box-shadow: 0 4px 10px rgba(15, 23, 42, .08);
  }

  .status-label {
    font-size: .8rem;
    font-weight: 700;
    color: #475569;
  }

  .status-value {
    font-size: 1.4rem;
    font-weight: 800;
    line-height: 1.15;
    letter-spacing: -.02em;
    color: #0f172a;
  }

  .status-card--blue { background: #eef4ff; border-color: #dbe8fb; }
  .status-card--blue .status-icon { color: #2563eb; }
  .status-card--amber { background: #fffbeb; border-color: #fdf0c4; }
  .status-card--amber .status-icon { color: #d97706; }
  .status-card--green { background: #effbf3; border-color: #d8f4e2; }
  .status-card--green .status-icon { color: #16a34a; }
  .status-card--red { background: #fef3f2; border-color: #fbdcd9; }
  .status-card--red .status-icon { color: #dc2626; }

  .charts-row {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
    gap: 1rem;
    align-items: stretch;
  }

  .chart-card {
    display: flex;
    flex-direction: column;
    min-width: 0;
    padding: 1.05rem 1.2rem 1.15rem;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
  }

  .chart-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .8rem;
  }

  .chart-title {
    display: flex;
    align-items: center;
    gap: .55rem;
    font-size: .98rem;
    font-weight: 800;
    letter-spacing: -.02em;
    color: #0b2545;
  }

  .chart-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    width: 30px;
    height: 30px;
    border-radius: 9px;
    color: #ffffff;
    font-size: .9rem;
  }

  .chart-badge--green { background: linear-gradient(135deg, #22c55e, #16a34a); }
  .chart-badge--blue { background: linear-gradient(135deg, #2f80ed, #0b4fd6); }

  .chart-sub {
    margin-top: .2rem;
    font-size: .78rem;
    color: #64748b;
  }

  .chart-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    flex: 0 0 auto;
    padding: .35rem .65rem;
    border: 1px solid #e6eef9;
    border-radius: 9px;
    background: #f8fbff;
    color: #475569;
    font-size: .76rem;
    font-weight: 700;
    white-space: nowrap;
  }

  .chart-pill-select {
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    padding-right: 1.7rem;
    font-family: inherit;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M4 6l4 4 4-4' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .55rem center;
    background-size: .7rem;
  }

  .chart-pill-select:focus-visible {
    outline: 2px solid #93c5fd;
    outline-offset: 1px;
  }

  .chart-body {
    position: relative;
    min-width: 0;
    height: 208px;
  }

  .donut-wrap {
    display: flex;
    flex: 1 1 auto;
    align-items: center;
    gap: 1rem;
    min-width: 0;
    flex-wrap: wrap;
  }

  .donut-canvas {
    position: relative;
    flex: 0 0 auto;
    width: 156px;
    height: 156px;
  }

  .donut-center {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    pointer-events: none;
  }

  .donut-center strong {
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
  }

  .donut-center span {
    font-size: .72rem;
    font-weight: 700;
    color: #64748b;
  }

  .donut-legend {
    list-style: none;
    display: grid;
    flex: 1 1 130px;
    gap: .5rem;
    min-width: 0;
    margin: 0;
    padding: 0;
  }

  .donut-legend li {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto auto;
    align-items: center;
    gap: .45rem;
    font-size: .8rem;
    color: #334155;
  }

  .donut-legend .dot {
    display: inline-block;
    width: 9px;
    height: 9px;
    border-radius: 50%;
  }

  .donut-legend .lg-count {
    min-width: 2.2rem;
    text-align: right;
    font-weight: 800;
    color: #0f172a;
  }

  .donut-legend .lg-pct {
    min-width: 3.2rem;
    text-align: right;
    font-weight: 700;
    color: #64748b;
  }

  .summary-card {
    margin-top: 0;
    overflow: hidden;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .05);
  }

  .summary-card .summary-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding: .95rem 1.2rem .5rem;
    border-bottom: none;
  }

  .summary-card .summary-head h3 {
    margin: 0;
    font-size: 1.02rem;
    letter-spacing: -.02em;
    color: #0b2545;
  }

  .summary-card .summary-head .subtitle {
    font-size: .78rem;
    color: #64748b;
  }

  .summary-card .summary-body {
    padding: .75rem 1.2rem 1.15rem;
  }

  .summary-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: #0b2545;
    color: #facc15;
    font-size: .95rem;
  }

  .quick-links {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .7rem;
  }

  .quick-links--4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }

  .quick-link {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .75rem .85rem;
    border: 1px solid #dfeafb;
    border-radius: 13px;
    background: #eef4ff;
    color: #0b2545;
    font-size: .86rem;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s ease, border-color .15s ease, transform .15s ease;
  }

  .quick-link:hover {
    background: #e4eefe;
    border-color: #bed6f7;
    box-shadow: none;
    transform: translateY(-1px);
  }

  .quick-link .ql-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    width: 32px;
    height: 32px;
    border: 1px solid #d5e4fa;
    border-radius: 9px;
    background: #ffffff;
    color: #1d6fe0;
    font-size: .95rem;
  }

  .quick-link .ql-icon.ql-icon--green {
    border-color: #d3efdf;
    background: #ffffff;
    color: #16a34a;
  }

  .quick-link .ql-text {
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .quick-link .ql-arrow {
    flex: 0 0 auto;
    color: #1d6fe0;
    font-weight: 700;
    transition: transform .15s ease;
  }

  .quick-link:hover .ql-arrow {
    color: #0b4fd6;
    transform: translateX(3px);
  }

  .dashboard-cards {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1rem;
    margin-top: 0;
  }

  .dashboard-right {
    position: sticky;
    top: 1rem;
    display: grid;
    gap: 1rem;
    align-self: start;
    max-height: none;
    overflow: visible;
    padding: 0;
    border: none;
    background: transparent;
    box-shadow: none;
  }

  .right-card {
    overflow: hidden;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
  }

  .right-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    padding: .95rem 1.1rem;
    border-bottom: 1px solid #eef4fb;
  }

  .right-card-header h3 {
    display: flex;
    align-items: center;
    gap: .4rem;
    margin: 0;
    font-size: .98rem;
    font-weight: 800;
    letter-spacing: -.02em;
    color: #0b2545;
  }

  .right-card-header a {
    flex: 0 0 auto;
    font-size: .78rem;
    font-weight: 700;
    color: #1d6fe0;
    text-decoration: none;
    white-space: nowrap;
  }

  .schedule {
    display: grid;
    gap: .55rem;
    padding: .8rem;
  }

  .schedule-item {
    display: grid;
    grid-template-columns: 70px minmax(0, 1fr) auto;
    align-items: center;
    gap: .65rem;
    padding: .55rem .6rem;
    border: 1px solid #e8effb;
    border-radius: 13px;
    background: #f5f9ff;
    cursor: pointer;
    transition: background .15s ease, border-color .15s ease, transform .15s ease;
  }

  .schedule-item:hover {
    background: #eef5ff;
    border-color: #cfe0f7;
    box-shadow: none;
    transform: translateY(-1px);
  }

  .schedule-time {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .1rem;
    min-height: 0;
    padding: .42rem .25rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #2f7bf0 0%, #0b4fd6 100%);
    color: #ffffff;
    text-align: center;
  }

  .schedule-time strong {
    font-size: .9rem;
    font-weight: 800;
    line-height: 1.1;
  }

  .schedule-time span {
    font-size: .6rem;
    line-height: 1.2;
    opacity: .92;
  }

  .schedule-title {
    margin-bottom: .12rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: .88rem;
    font-weight: 800;
    color: #0b2545;
  }

  .schedule-meta {
    font-size: .82rem;
    line-height: 1.4;
    color: #64748b;
  }

  .schedule-row {
    display: flex;
    align-items: center;
    gap: .3rem;
    min-width: 0;
    font-size: .74rem;
    line-height: 1.35;
    color: #64748b;
  }

  .schedule-row i {
    flex: 0 0 auto;
    font-size: .78rem;
    color: #94a3b8;
  }

  .schedule-row span {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .schedule-chevron {
    flex: 0 0 auto;
    color: #b6c6dc;
    font-size: .9rem;
    transition: color .15s ease, transform .15s ease;
  }

  .schedule-item:hover .schedule-chevron {
    color: #1d6fe0;
    transform: translateX(2px);
  }

  .empty-state {
    padding: 1rem;
    text-align: center;
    font-size: .85rem;
    color: #64748b;
  }

  .mobile-grid-menu,
  .mobile-more-panel {
    display: none !important;
  }

  /* Footer tetap di bawah viewport, ikut saat halaman di-scroll */
  body .site-footer {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 50;
    margin-top: 0;
    padding: .6rem 0;
    border-top: 1px solid rgba(148, 178, 222, .4);
    background: #ffffff;
    text-align: left;
  }

  body .site-footer-inner {
    max-width: 100%;
    padding: 0 1.25rem 0 calc(252px + 1.25rem);
  }

  body .site-footer .footer-left,
  body .site-footer .footer-right {
    font-size: .78rem;
    color: #64748b;
  }

  /* Ruang bawah agar konten terakhir tidak tertutup footer tetap */
  body.admin-shell .container {
    padding-bottom: 4.5rem;
  }

  /* =========================================================
     REDESIGN: responsif
     ========================================================= */
  @media (max-width: 1400px) {
    .dashboard-shell {
      grid-template-columns: minmax(0, 1fr) 330px;
    }
  }

  @media (max-width: 1200px) {
    .dashboard-shell {
      grid-template-columns: minmax(0, 1fr);
      padding-left: 0;
    }

    .dashboard-sidebar {
      display: none;
    }

    .topbar-menu-btn {
      display: inline-flex;
    }

    .dashboard-right {
      position: static;
    }

    body .site-footer-inner {
      padding-left: 1rem;
      padding-right: 1rem;
    }

    body.mobile-sidebar-open .dashboard-sidebar {
      display: flex !important;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: 270px;
      max-height: none;
      overflow-y: auto;
      z-index: 120;
      border-radius: 0;
      box-shadow: 0 0 40px rgba(0, 0, 0, .35);
    }

    body.mobile-sidebar-open::after {
      content: "";
      position: fixed;
      inset: 0;
      z-index: 110;
      background: rgba(15, 23, 42, .5);
    }
  }

  @media (max-width: 1000px) {
    .metrics-row,
    .charts-row {
      grid-template-columns: minmax(0, 1fr);
    }

    .status-cards {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .quick-links--4 {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-cards {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 820px) {
    .dashboard-hero {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
      padding: 1.25rem;
      background:
        linear-gradient(180deg, rgba(238, 245, 255, .96) 0%, rgba(238, 245, 255, .88) 60%, rgba(238, 245, 255, .7) 100%),
        url('{{ asset('assets/img/gedung-sate-drive.png') }}') 62% 40% / cover no-repeat;
    }

    .hero-actions {
      flex-wrap: wrap;
    }
  }

  @media (max-width: 640px) {
    .container {
      padding: .75rem .75rem 0;
    }

    .dashboard-topbar {
      top: .25rem;
      gap: .5rem;
      padding: .55rem .6rem;
      border-radius: 14px;
    }

    .topbar-search {
      max-width: none;
    }

    .topbar-search input {
      padding-right: 2.4rem;
      font-size: .84rem;
    }

    .topbar-shortcut {
      display: none;
    }

    .topbar-right .topbar-icon-btn:first-child {
      display: none;
    }

    .topbar-icon-btn {
      width: 34px;
      height: 34px;
      font-size: .92rem;
    }

    .topbar-user {
      padding: .15rem;
      border: none;
      background: transparent;
    }

    .topbar-user-meta,
    .topbar-user > i {
      display: none;
    }

    .topbar-avatar {
      width: 34px;
      height: 34px;
    }

    .dashboard-hero {
      min-height: 0;
      border-radius: 18px;
    }

    .dashboard-hero h2 {
      font-size: 1.4rem;
    }

    .dashboard-hero .subtitle {
      font-size: .85rem;
    }

    .hero-actions {
      width: 100%;
    }

    .dashboard-hero .btn {
      flex: 1 1 auto;
    }

    .metric-card.metric-wide {
      min-height: 0;
      gap: .75rem;
      padding: .9rem 1rem;
    }

    .metric-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      font-size: 1.15rem;
    }

    .metric-value {
      font-size: 1.5rem;
    }

    .metric-watermark,
    .metric-bars {
      display: none;
    }

    .status-cards {
      gap: .55rem;
    }

    .status-card {
      gap: .55rem;
      padding: .7rem;
    }

    .status-icon {
      width: 34px;
      height: 34px;
      font-size: .95rem;
    }

    .status-label {
      font-size: .74rem;
    }

    .status-value {
      font-size: 1.15rem;
    }

    .chart-card {
      padding: .9rem .95rem 1rem;
    }

    .chart-head {
      flex-direction: column;
      gap: .45rem;
    }

    .chart-body {
      height: 185px;
    }

    .donut-canvas {
      width: 140px;
      height: 140px;
    }

    .quick-links,
    .quick-links--4 {
      grid-template-columns: minmax(0, 1fr);
    }

    .dashboard-cards {
      grid-template-columns: minmax(0, 1fr);
    }

    .summary-card .summary-head,
    .summary-card .summary-body {
      padding-left: .95rem;
      padding-right: .95rem;
    }

    .schedule-item {
      grid-template-columns: 64px minmax(0, 1fr) auto;
      gap: .55rem;
      padding: .5rem;
    }

    body .site-footer-inner {
      flex-direction: column;
      align-items: flex-start;
      gap: .2rem;
      padding: 0 .75rem;
    }
  }

  /* Di mobile footer disembunyikan, cukup sisakan ruang untuk bottom nav */
  @media (max-width: 768px) {
    body .site-footer {
      display: none;
    }

    body.admin-shell:has(.mobile-bottom-nav) .container {
      padding-bottom: 5rem;
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
    <div class="dashboard-topbar">
      <div class="topbar-left">
        <button class="topbar-menu-btn" id="topbarMenuBtn" aria-label="Toggle sidebar">
          <i class="bi bi-list"></i>
        </button>
        <div class="topbar-search">
          <i class="bi bi-search"></i>
          <input type="text" id="topbarSearchInput" placeholder="Cari booking, kendaraan, atau pelanggan..." aria-label="Cari booking" autocomplete="off">
          <span class="topbar-shortcut">Ctrl + K</span>
          <div class="topbar-search-results" id="topbarSearchResults" hidden></div>
        </div>
      </div>
      <div class="topbar-right">
        <button class="topbar-icon-btn" id="topbarCalendarBtn" type="button" title="Kalender booking" aria-label="Buka kalender booking">
          <i class="bi bi-calendar3"></i>
        </button>
        <button class="topbar-icon-btn" title="Notifikasi" type="button">
          <i class="bi bi-bell"></i>
          <span class="topbar-badge"></span>
        </button>
        <button class="topbar-icon-btn" title="Bantuan" type="button">
          <i class="bi bi-question-circle"></i>
        </button>
        <div class="topbar-user-wrap" id="topbarUserWrap">
          <button class="topbar-user" id="topbarUserBtn" type="button" title="{{ auth()->user()->name ?? 'Pengguna' }}" aria-haspopup="true" aria-expanded="false" aria-controls="topbarUserMenu">
            <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div class="topbar-user-meta">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ str_replace('_', ' ', auth()->user()->role ?? 'user') }}</span>
            </div>
            <i class="bi bi-chevron-down"></i>
          </button>
          <div class="topbar-user-menu" id="topbarUserMenu" role="menu" aria-labelledby="topbarUserBtn" hidden>
            <div class="topbar-user-menu-head">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ auth()->user()->email ?? '' }}</span>
            </div>
            <a class="topbar-user-menu-item" href="{{ route('accounts.show', auth()->id()) }}" role="menuitem">
              <i class="bi bi-person"></i> Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="topbar-user-menu-item topbar-user-menu-item--danger" role="menuitem">
                <i class="bi bi-box-arrow-right"></i> Logout
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="dashboard-hero">
      <div class="hero-content">
        <div class="hero-eyebrow">DASHBOARD</div>
        <h2>Halo, {{ auth()->user()->name ?? 'Pengguna' }} 👋</h2>
        <div class="subtitle">Pantau booking, kendaraan, dan mitra dalam satu tampilan yang lebih bersih dan modern.</div>
      </div>
      <div class="hero-actions">
        @if($role === 'super_admin')
          <a class="btn btn-primary" href="{{ route('bookings.index') }}"><i class="bi bi-calendar3"></i> Kelola Booking</a>
        @endif
        <a class="btn" href="{{ route('booking.create') }}"><i class="bi bi-plus-lg"></i> Tambah Booking</a>
      </div>
    </div>

    @if($role === 'super_admin')
    @php
        $donutTotal = ($metrics['booking_masuk'] ?? 0)
            + ($metrics['booking_proses'] ?? 0)
            + ($metrics['booking_selesai'] ?? 0)
            + ($metrics['booking_cancel'] ?? 0);
        $donutPct = function ($value) use ($donutTotal) {
            return $donutTotal > 0 ? number_format($value / $donutTotal * 100, 1, ',', '') : '0,0';
        };
    @endphp
    <div class="metrics-row">
      <div class="metric-card metric-wide" style="background: linear-gradient(135deg, #1e63e9 0%, #0b4fd6 100%);">
        <div class="metric-icon"><i class="bi bi-calendar3"></i></div>
        <div class="metric-body">
          <h4>Total Booking</h4>
          <div class="metric-row">
            <div class="metric-value">{{ $metrics['bookings'] ?? 0 }}</div>
            @if(($growthLabel = $fmtGrowth($metrics['bookings_growth'] ?? null)) !== null && ($metrics['bookings_growth'] ?? 0) > 0)
              <span class="metric-badge">
                <i class="bi bi-arrow-up"></i>{{ $growthLabel }}
              </span>
            @endif
          </div>
          <div class="metric-note">vs. bulan lalu</div>
        </div>
        <div class="metric-watermark" aria-hidden="true"><i class="bi bi-calendar3"></i></div>
      </div>

      <div class="metric-card metric-wide metric-card--light">
        <div class="metric-icon"><i class="bi bi-cash-coin"></i></div>
        <div class="metric-body">
          <h4>Pendapatan Kotor</h4>
          <div class="metric-row">
            <div class="metric-value">Rp {{ number_format($metrics['gross_revenue'] ?? 0, 0, ',', '.') }}</div>
            @if(($growthLabel = $fmtGrowth($metrics['revenue_growth'] ?? null)) !== null && ($metrics['revenue_growth'] ?? 0) > 0)
              <span class="metric-badge">
                <i class="bi bi-arrow-up"></i>{{ $growthLabel }}
              </span>
            @endif
          </div>
          <div class="metric-note">vs. bulan lalu</div>
        </div>
        <div class="metric-bars" aria-hidden="true">
          <span style="height: 34%"></span>
          <span style="height: 52%"></span>
          <span style="height: 42%"></span>
          <span style="height: 68%"></span>
          <span style="height: 58%"></span>
          <span style="height: 88%"></span>
        </div>
      </div>
    </div>

    <div class="status-cards">
      <div class="status-card status-card--blue">
        <div class="status-icon"><i class="bi bi-box-arrow-in-right"></i></div>
        <div>
          <div class="status-label">Masuk</div>
          <div class="status-value">{{ $metrics['booking_masuk'] ?? 0 }}</div>
        </div>
      </div>
      <div class="status-card status-card--amber">
        <div class="status-icon"><i class="bi bi-clock-history"></i></div>
        <div>
          <div class="status-label">Proses</div>
          <div class="status-value">{{ $metrics['booking_proses'] ?? 0 }}</div>
        </div>
      </div>
      <div class="status-card status-card--green">
        <div class="status-icon"><i class="bi bi-check2-circle"></i></div>
        <div>
          <div class="status-label">Selesai</div>
          <div class="status-value">{{ $metrics['booking_selesai'] ?? 0 }}</div>
        </div>
      </div>
      <div class="status-card status-card--red">
        <div class="status-icon"><i class="bi bi-x-circle"></i></div>
        <div>
          <div class="status-label">Cancel</div>
          <div class="status-value">{{ $metrics['booking_cancel'] ?? 0 }}</div>
        </div>
      </div>
    </div>

    <div class="charts-row">
      <div class="chart-card">
        <div class="chart-head">
          <div>
            <div class="chart-title">
              <span class="chart-badge chart-badge--green"><i class="bi bi-bar-chart-fill"></i></span>
              Tren Booking
            </div>
            <div class="chart-sub" id="trendSubtitle">Perkembangan jumlah booking 7 hari terakhir.</div>
          </div>
          <select class="chart-pill chart-pill-select" id="trendRangeSelect" aria-label="Filter periode tren booking">
            <option value="7d">7 Hari Terakhir</option>
            <option value="1w">1 Minggu Ini</option>
            <option value="1y">1 Tahun Terakhir</option>
            <option value="all">Semuanya</option>
          </select>
        </div>
        <div class="chart-body">
          <canvas id="trenBookingChart" aria-label="Grafik tren booking 7 hari terakhir" role="img"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <div class="chart-head">
          <div class="chart-title">
            <span class="chart-badge chart-badge--blue"><i class="bi bi-truck"></i></span>
            Status Booking
          </div>
        </div>
        <div class="donut-wrap">
          <div class="donut-canvas">
            <canvas id="statusBookingChart" aria-label="Grafik status booking" role="img"></canvas>
            <div class="donut-center">
              <strong>{{ $metrics['bookings'] ?? 0 }}</strong>
              <span>Total</span>
            </div>
          </div>
          <ul class="donut-legend">
            <li>
              <span class="dot" style="background:#16a34a"></span><span>Selesai</span>
              <span class="lg-count">{{ $metrics['booking_selesai'] ?? 0 }}</span>
              <span class="lg-pct">{{ $donutPct($metrics['booking_selesai'] ?? 0) }}%</span>
            </li>
            <li>
              <span class="dot" style="background:#eab308"></span><span>Proses</span>
              <span class="lg-count">{{ $metrics['booking_proses'] ?? 0 }}</span>
              <span class="lg-pct">{{ $donutPct($metrics['booking_proses'] ?? 0) }}%</span>
            </li>
            <li>
              <span class="dot" style="background:#2563eb"></span><span>Masuk</span>
              <span class="lg-count">{{ $metrics['booking_masuk'] ?? 0 }}</span>
              <span class="lg-pct">{{ $donutPct($metrics['booking_masuk'] ?? 0) }}%</span>
            </li>
            <li>
              <span class="dot" style="background:#dc2626"></span><span>Cancel</span>
              <span class="lg-count">{{ $metrics['booking_cancel'] ?? 0 }}</span>
              <span class="lg-pct">{{ $donutPct($metrics['booking_cancel'] ?? 0) }}%</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="summary-card">
      <div class="summary-head">
        <div class="summary-title-row">
          <span class="summary-icon"><i class="bi bi-lightning-charge-fill"></i></span>
          <div>
            <h3>Ringkasan &amp; Akses Cepat</h3>
            <div class="subtitle" style="margin-top:.25rem;">Akses cepat ke modul utama dan pantau jadwal di panel kanan.</div>
          </div>
        </div>
      </div>
      <div class="summary-body">
        <div class="quick-links quick-links--4">
          <a class="quick-link" href="{{ route('bookings.index') }}">
            <span class="ql-icon"><i class="bi bi-calendar3"></i></span>
            <span class="ql-text">Kelola Booking</span>
            <span class="ql-arrow">&rarr;</span>
          </a>
          <a class="quick-link" href="{{ route('dashboard.calendar') }}">
            <span class="ql-icon"><i class="bi bi-calendar-week"></i></span>
            <span class="ql-text">Buka Kalender</span>
            <span class="ql-arrow">&rarr;</span>
          </a>
          <a class="quick-link" href="{{ route('settings.smtp') }}">
            <span class="ql-icon ql-icon--green"><i class="bi bi-envelope-paper"></i></span>
            <span class="ql-text">SMTP Setting</span>
            <span class="ql-arrow">&rarr;</span>
          </a>
          <a class="quick-link" href="{{ route('email-logs.index') }}">
            <span class="ql-icon"><i class="bi bi-gear"></i></span>
            <span class="ql-text">Email Logs</span>
            <span class="ql-arrow">&rarr;</span>
          </a>
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
        <h3><i class="bi bi-calendar3" style="color:#1565c0; margin-right:.4rem;"></i>Jadwal Booking Terbaru</h3>
        <a href="{{ route('dashboard.calendar') }}" style="text-decoration:none; font-weight:700; color:#1565c0; font-size:.85rem; white-space:nowrap;">Lihat Kalender &rarr;</a>
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
      <button id="booking_modal_close" type="button" class="modal-close" aria-label="Tutup detail booking">
        <i class="bi bi-x-lg" aria-hidden="true"></i>
      </button>
    </div>
    <div class="modal-scroll">
      <div id="booking_modal_body" class="modal-grid"></div>
      <div class="modal-section" style="margin-top:.75rem;">
        <div class="subtitle" style="margin-bottom:.35rem; font-weight:700;">Travel Plans</div>
        <div class="wysiwyg-view">
          <div id="booking_modal_travel_content" class="w-content"></div>
        </div>
      </div>
    </div>
    @if(auth()->user()?->role === 'super_admin')
    <div class="modal-actions">
      <a id="booking_modal_edit" href="#" class="btn btn-primary" style="display:none;">Edit Booking</a>
    </div>
    @endif
  </div>
</div>

<div id="booking_calendar_modal" class="modal-backdrop">
  <div class="modal-card calendar-modal-card">
    <div class="modal-header calendar-header">
      <div class="calendar-heading">
        <span class="calendar-heading-icon"><i class="bi bi-calendar3"></i></span>
        <div>
          <div class="modal-title">Kalender Booking</div>
          <div class="calendar-subtitle" id="calendarMonthSummary">Ringkasan jadwal booking per bulan</div>
        </div>
      </div>
      <button id="booking_calendar_close" type="button" class="modal-close" aria-label="Tutup kalender">
        <i class="bi bi-x-lg" aria-hidden="true"></i>
      </button>
    </div>
    <div class="calendar-body">
      <div class="calendar-toolbar">
        <div class="calendar-month-label" id="calendarMonthLabel"></div>
        <div class="calendar-nav">
          <button type="button" id="calendarPrev" class="calendar-nav-btn" aria-label="Bulan sebelumnya">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button type="button" id="calendarToday" class="calendar-today-btn">Bulan Ini</button>
          <button type="button" id="calendarNext" class="calendar-nav-btn" aria-label="Bulan berikutnya">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>
      <div class="calendar-weekdays">
        <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
      </div>
      <div class="calendar-grid" id="calendarGrid"></div>
      <div class="calendar-footer">
        <div class="calendar-legend">
          <span><i style="background:#3b82f6;"></i>Masuk</span>
          <span><i style="background:#f59e0b;"></i>Proses</span>
          <span><i style="background:#22c55e;"></i>Selesai</span>
          <span><i style="background:#ef4444;"></i>Cancel</span>
        </div>
        <div class="calendar-hint">
          <i class="bi bi-hand-index-thumb"></i>Klik booking untuk lihat detail
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof Chart === 'undefined') return;

  Chart.defaults.font.family = "'Instrument Sans', ui-sans-serif, system-ui, sans-serif";

  const tooltipStyle = {
    backgroundColor: '#0f172a',
    padding: 10,
    cornerRadius: 10,
    displayColors: false,
    titleFont: { size: 12, weight: '700' },
    bodyFont: { size: 12 }
  };

  const trendCanvas = document.getElementById('trenBookingChart');
  if (trendCanvas) {
    const ctx = trendCanvas.getContext('2d');
    const areaFill = ctx.createLinearGradient(0, 0, 0, 210);
    areaFill.addColorStop(0, 'rgba(34, 197, 94, 0.40)');
    areaFill.addColorStop(1, 'rgba(34, 197, 94, 0.02)');

    const trendChart = new Chart(trendCanvas, {
      type: 'line',
      data: {
        labels: @json($trendLabels),
        datasets: [{
          label: 'Booking',
          data: @json($trendValues),
          borderColor: '#2563eb',
          backgroundColor: areaFill,
          fill: true,
          tension: 0.38,
          borderWidth: 3,
          pointBackgroundColor: '#2563eb',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: Object.assign({}, tooltipStyle, {
            callbacks: { label: function (c) { return c.parsed.y + ' booking'; } }
          })
        },
        scales: {
          x: {
            grid: { display: false },
            border: { display: false },
            ticks: { color: '#64748b', font: { size: 11 } }
          },
          y: {
            beginAtZero: true,
            grace: '10%',
            grid: { color: '#eef2f7' },
            border: { display: false },
            ticks: { precision: 0, color: '#94a3b8', font: { size: 11 }, padding: 6 }
          }
        }
      }
    });

    const trendRanges = @json($trendRanges);
    const trendRangeSelect = document.getElementById('trendRangeSelect');
    const trendSubtitle = document.getElementById('trendSubtitle');

    if (trendRangeSelect) {
      trendRangeSelect.addEventListener('change', function () {
        const range = trendRanges[this.value];
        if (!range) return;

        trendChart.data.labels = range.labels;
        trendChart.data.datasets[0].data = range.values;
        trendChart.update();

        if (trendSubtitle) trendSubtitle.textContent = range.subtitle;
        trendCanvas.setAttribute('aria-label', 'Grafik tren booking: ' + range.subtitle);
      });
    }
  }

  const donutCanvas = document.getElementById('statusBookingChart');
  if (donutCanvas) {
    new Chart(donutCanvas, {
      type: 'doughnut',
      data: {
        labels: ['Selesai', 'Proses', 'Masuk', 'Cancel'],
        datasets: [{
          data: [
            {{ $metrics['booking_selesai'] ?? 0 }},
            {{ $metrics['booking_proses'] ?? 0 }},
            {{ $metrics['booking_masuk'] ?? 0 }},
            {{ $metrics['booking_cancel'] ?? 0 }}
          ],
          backgroundColor: ['#16a34a', '#eab308', '#2563eb', '#dc2626'],
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: { display: false },
          tooltip: Object.assign({}, tooltipStyle, {
            callbacks: { label: function (c) { return c.label + ': ' + c.parsed; } }
          })
        }
      }
    });
  }
});
</script>
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

  const MONTHS_ID = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

  function formatDateID(value) {
    const raw = normalizeDate(value);
    if (!raw) return '-';
    const parts = raw.split('-');
    if (parts.length !== 3) return raw;
    const month = MONTHS_ID[parseInt(parts[1], 10) - 1] || parts[1];

    return parseInt(parts[2], 10) + ' ' + month + ' ' + parts[0];
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
    if (modal) modal.classList.add('open');
  }

  function closeModal() {
    if (modal) modal.classList.remove('open');
    if (modalEdit) modalEdit.style.display = 'none';
    if (modalTravel) modalTravel.innerHTML = '';
  }

  try {
    const res = await fetch('/api/bookings', { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Network response was not ok');
    const data = await res.json();

    const parsed = data
      .map(function (booking) {
        return {
          id: booking.id,
          raw: booking,
          createdAt: booking.created_at || '',
          date: booking.booking_date,
          displayDate: formatDateID(booking.booking_date),
          time: normalizeTime(booking.pickup_time),
          title: booking.customer_name ?? 'Customer',
          driver: (booking.mitra && booking.mitra.full_name) ? booking.mitra.full_name : 'No driver',
          location: booking.pickup_location || ''
        };
      })
      // Paling atas = booking yang paling baru dibuat (created_at desc).
      .sort((a, b) => b.createdAt.localeCompare(a.createdAt)
        || (b.date + ' ' + b.time).localeCompare(a.date + ' ' + a.time))
      .slice(0, 9);

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
        info.style.minWidth = '0';

        const title = document.createElement('div');
        title.className = 'schedule-title';
        title.textContent = item.title;
        info.appendChild(title);

        const driverRow = document.createElement('div');
        driverRow.className = 'schedule-row';
        driverRow.innerHTML = '<i class="bi bi-truck"></i>';
        const driverText = document.createElement('span');
        driverText.textContent = item.driver;
        driverRow.appendChild(driverText);
        info.appendChild(driverRow);

        if (item.location) {
          const locationRow = document.createElement('div');
          locationRow.className = 'schedule-row';
          locationRow.innerHTML = '<i class="bi bi-geo-alt"></i>';
          const locationText = document.createElement('span');
          locationText.textContent = item.location;
          locationRow.appendChild(locationText);
          info.appendChild(locationRow);
        }

        const chevron = document.createElement('i');
        chevron.className = 'bi bi-chevron-right schedule-chevron';
        chevron.setAttribute('aria-hidden', 'true');

        wrap.appendChild(time);
        wrap.appendChild(info);
        wrap.appendChild(chevron);
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

  // ===== Pencarian booking di topbar (AJAX) =====
  const searchInput = document.getElementById('topbarSearchInput');
  const searchResults = document.getElementById('topbarSearchResults');

  if (searchInput && searchResults) {
    let searchTimer = null;
    let searchRequestId = 0;

    function hideSearchResults() {
      searchResults.hidden = true;
      searchResults.innerHTML = '';
    }

    function renderSearchResults(bookings, term) {
      searchResults.innerHTML = '';

      if (!bookings.length) {
        const empty = document.createElement('div');
        empty.className = 'search-result-empty';
        empty.textContent = 'Tidak ada booking untuk "' + term + '".';
        searchResults.appendChild(empty);
        searchResults.hidden = false;
        return;
      }

      bookings.forEach(function (booking) {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'search-result';

        const title = document.createElement('span');
        title.className = 'search-result-title';
        title.textContent = booking.customer_name || 'Customer';
        item.appendChild(title);

        const vehicleName = booking.vehicle
          ? [booking.vehicle.make, booking.vehicle.model].filter(Boolean).join(' ') || booking.vehicle.plate_number
          : '';
        const metaParts = [
          formatDateID(booking.booking_date),
          vehicleName,
          booking.service ? booking.service.name : ''
        ].filter(Boolean);

        const meta = document.createElement('span');
        meta.className = 'search-result-meta';
        meta.textContent = metaParts.join(' • ');
        item.appendChild(meta);

        item.addEventListener('click', function () {
          hideSearchResults();
          searchInput.value = '';
          openBookingModal(booking);
        });

        searchResults.appendChild(item);
      });

      searchResults.hidden = false;
    }

    function runSearch(term) {
      const requestId = ++searchRequestId;

      fetch('/api/bookings?q=' + encodeURIComponent(term) + '&limit=8', {
        headers: { 'Accept': 'application/json' }
      })
        .then(function (res) {
          if (!res.ok) throw new Error('Network response was not ok');
          return res.json();
        })
        .then(function (data) {
          if (requestId !== searchRequestId) return;
          renderSearchResults(Array.isArray(data) ? data : [], term);
        })
        .catch(function () {
          if (requestId !== searchRequestId) return;
          searchResults.innerHTML = '<div class="search-result-empty">Gagal memuat data booking.</div>';
          searchResults.hidden = false;
        });
    }

    searchInput.addEventListener('input', function () {
      const term = searchInput.value.trim();
      clearTimeout(searchTimer);

      if (!term) {
        hideSearchResults();
        return;
      }

      searchTimer = setTimeout(function () { runSearch(term); }, 250);
    });

    searchInput.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        hideSearchResults();
        searchInput.blur();
        return;
      }

      if (e.key === 'Enter') {
        const firstResult = searchResults.querySelector('.search-result');
        if (firstResult) {
          e.preventDefault();
          firstResult.click();
        }
      }
    });

    document.addEventListener('click', function (e) {
      if (searchResults.hidden) return;
      if (searchResults.contains(e.target) || searchInput.contains(e.target)) return;
      hideSearchResults();
    });

    document.addEventListener('keydown', function (e) {
      if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault();
        searchInput.focus();
      }
    });
  }

  // ===== Popup kalender booking =====
  const calendarModal = document.getElementById('booking_calendar_modal');
  const calendarOpenBtn = document.getElementById('topbarCalendarBtn');

  if (calendarModal && calendarOpenBtn) {
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarMonthLabel = document.getElementById('calendarMonthLabel');
    const calendarPrev = document.getElementById('calendarPrev');
    const calendarNext = document.getElementById('calendarNext');
    const calendarToday = document.getElementById('calendarToday');
    const calendarMonthSummary = document.getElementById('calendarMonthSummary');
    const MONTHS_FULL_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const calendarNow = new Date();
    let calendarMonth = new Date(calendarNow.getFullYear(), calendarNow.getMonth(), 1);
    let calendarRequestId = 0;

    const padTwo = function (value) { return String(value).padStart(2, '0'); };

    function calendarStatusClass(status) {
      const value = (status || '').toLowerCase();
      if (['selesai_pelayanan', 'selesai_administrasi_fee', 'selesai'].indexOf(value) !== -1) return 'calendar-event--selesai';
      if (['konfirmasi', 'dijadwalkan'].indexOf(value) !== -1) return 'calendar-event--proses';
      if (['cancelled', 'cancel', 'batal'].indexOf(value) !== -1) return 'calendar-event--cancel';
      return 'calendar-event--masuk';
    }

    // Tanggal yang dicakup booking (mendukung booking multi-hari).
    function calendarDateKeys(booking) {
      const start = normalizeDate(booking.booking_date);
      if (!start) return [];

      const end = normalizeDate(booking.end_date) || start;
      const keys = [];
      let cursor = new Date(start + 'T00:00:00Z');
      const last = new Date(end + 'T00:00:00Z');

      while (cursor <= last && keys.length < 62) {
        keys.push(cursor.toISOString().slice(0, 10));
        cursor = new Date(cursor.getTime() + 86400000);
      }

      return keys;
    }

    function renderCalendar(bookings) {
      const year = calendarMonth.getFullYear();
      const month = calendarMonth.getMonth();
      calendarMonthLabel.textContent = MONTHS_FULL_ID[month] + ' ' + year;
      calendarMonthSummary.textContent = bookings.length
        ? bookings.length + ' booking terjadwal di bulan ini'
        : 'Belum ada booking di bulan ini';

      const byDate = {};
      bookings.forEach(function (booking) {
        calendarDateKeys(booking).forEach(function (key) {
          if (!byDate[key]) byDate[key] = [];
          byDate[key].push(booking);
        });
      });

      const firstWeekday = (new Date(Date.UTC(year, month, 1)).getUTCDay() + 6) % 7;
      const daysInMonth = new Date(Date.UTC(year, month + 1, 0)).getUTCDate();
      const todayKey = calendarNow.getFullYear() + '-' + padTwo(calendarNow.getMonth() + 1) + '-' + padTwo(calendarNow.getDate());

      calendarGrid.innerHTML = '';

      for (let i = 0; i < firstWeekday; i++) {
        const blank = document.createElement('div');
        blank.className = 'calendar-day is-empty';
        calendarGrid.appendChild(blank);
      }

      for (let day = 1; day <= daysInMonth; day++) {
        const key = year + '-' + padTwo(month + 1) + '-' + padTwo(day);
        const weekday = new Date(Date.UTC(year, month, day)).getUTCDay();
        const isWeekend = weekday === 0 || weekday === 6;
        const cell = document.createElement('div');
        cell.className = 'calendar-day'
          + (key === todayKey ? ' is-today' : '')
          + (isWeekend ? ' is-weekend' : '')
          + (byDate[key] && byDate[key].length ? ' has-booking' : '');

        const num = document.createElement('span');
        num.className = 'calendar-day-num';
        num.textContent = day;
        cell.appendChild(num);

        const dayBookings = (byDate[key] || []).slice().sort(function (a, b) {
          return normalizeTime(a.pickup_time).localeCompare(normalizeTime(b.pickup_time));
        });

        dayBookings.slice(0, 3).forEach(function (booking) {
          const chip = document.createElement('button');
          chip.type = 'button';
          chip.className = 'calendar-event ' + calendarStatusClass(booking.status);
          chip.textContent = (booking.customer_name || 'Booking') + ' • ' + normalizeTime(booking.pickup_time);
          chip.title = (booking.customer_name || 'Booking') + ' - ' + formatDateID(booking.booking_date) + ' ' + normalizeTime(booking.pickup_time);
          chip.addEventListener('click', function () {
            calendarModal.classList.remove('open');
            openBookingModal(booking);
          });
          cell.appendChild(chip);
        });

        if (dayBookings.length > 3) {
          const more = document.createElement('span');
          more.className = 'calendar-more';
          more.textContent = '+' + (dayBookings.length - 3) + ' lainnya';
          cell.appendChild(more);
        }

        calendarGrid.appendChild(cell);
      }

      while (calendarGrid.children.length % 7 !== 0) {
        const blank = document.createElement('div');
        blank.className = 'calendar-day is-empty';
        calendarGrid.appendChild(blank);
      }
    }

    function loadCalendarMonth() {
      const year = calendarMonth.getFullYear();
      const month = calendarMonth.getMonth();
      const start = year + '-' + padTwo(month + 1) + '-01';
      const next = new Date(year, month + 1, 1);
      const end = next.getFullYear() + '-' + padTwo(next.getMonth() + 1) + '-01';
      const requestId = ++calendarRequestId;

      calendarMonthLabel.textContent = MONTHS_FULL_ID[month] + ' ' + year;
      calendarMonthSummary.textContent = 'Memuat data booking...';
      calendarGrid.innerHTML = '<div class="calendar-empty">Memuat data booking...</div>';

      fetch('/api/bookings?start=' + start + '&end=' + end, { headers: { 'Accept': 'application/json' } })
        .then(function (res) {
          if (!res.ok) throw new Error('Network response was not ok');
          return res.json();
        })
        .then(function (data) {
          if (requestId !== calendarRequestId) return;
          renderCalendar(Array.isArray(data) ? data : []);
        })
        .catch(function () {
          if (requestId !== calendarRequestId) return;
          calendarMonthSummary.textContent = 'Gagal memuat data booking';
          calendarGrid.innerHTML = '<div class="calendar-empty">Gagal memuat kalender.</div>';
        });
    }

    function closeCalendarModal() {
      calendarModal.classList.remove('open');
    }

    calendarOpenBtn.addEventListener('click', function () {
      calendarModal.classList.add('open');
      loadCalendarMonth();
    });

    document.getElementById('booking_calendar_close').addEventListener('click', closeCalendarModal);

    calendarPrev.addEventListener('click', function () {
      calendarMonth = new Date(calendarMonth.getFullYear(), calendarMonth.getMonth() - 1, 1);
      loadCalendarMonth();
    });

    calendarNext.addEventListener('click', function () {
      calendarMonth = new Date(calendarMonth.getFullYear(), calendarMonth.getMonth() + 1, 1);
      loadCalendarMonth();
    });

    calendarToday.addEventListener('click', function () {
      calendarMonth = new Date(calendarNow.getFullYear(), calendarNow.getMonth(), 1);
      loadCalendarMonth();
    });

    calendarModal.addEventListener('click', function (e) {
      if (e.target === calendarModal) closeCalendarModal();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeCalendarModal();
    });
  }

  document.getElementById('booking_modal_close').addEventListener('click', closeModal);
  document.getElementById('booking_modal').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
  });
});

(function () {
  const btn = document.getElementById('topbarMenuBtn');
  if (!btn) return;
  btn.addEventListener('click', function () {
    document.body.classList.toggle('mobile-sidebar-open');
  });
  document.addEventListener('click', function (e) {
    if (!document.body.classList.contains('mobile-sidebar-open')) return;
    const sidebar = document.querySelector('.dashboard-sidebar');
    if (sidebar && !sidebar.contains(e.target) && !btn.contains(e.target)) {
      document.body.classList.remove('mobile-sidebar-open');
    }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') document.body.classList.remove('mobile-sidebar-open');
  });
})();

(function () {
  const wrap = document.getElementById('topbarUserWrap');
  const btn = document.getElementById('topbarUserBtn');
  const menu = document.getElementById('topbarUserMenu');
  if (!wrap || !btn || !menu) return;

  function setOpen(open) {
    menu.hidden = !open;
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  btn.addEventListener('click', function (e) {
    e.stopPropagation();
    setOpen(menu.hidden);
  });

  document.addEventListener('click', function (e) {
    if (!menu.hidden && !wrap.contains(e.target)) setOpen(false);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !menu.hidden) {
      setOpen(false);
      btn.focus();
    }
  });
})();
</script>
@endsection
