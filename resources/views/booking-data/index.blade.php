@extends('layouts.app', ['title' => 'Booking Data'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; overflow: hidden; }
  .stat-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem; margin-bottom: 1rem; }
  .stat-card { border-radius: 14px; padding: 1rem 1.1rem; border: 1px solid #e2e8f0; }
  .stat-card .stat-label { font-size: .82rem; color: var(--muted); margin-bottom: .25rem; }
  .stat-card .stat-value { font-size: 1.6rem; font-weight: 800; letter-spacing: -.03em; line-height: 1.1; }
  .chart-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
  .chart-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; }
  .chart-card h3 { margin: 0 0 .75rem; font-size: 1rem; font-weight: 700; color: #0f172a; }
  .chart-container { position: relative; max-width: 220px; margin: 0 auto; }
  .revenue-card { background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); border-radius: 14px; padding: 1.25rem; margin-bottom: 1rem; color: #fff; }
  .revenue-card .label { font-size: .85rem; opacity: .85; margin-bottom: .25rem; }
  .revenue-card .value { font-size: 1.75rem; font-weight: 800; letter-spacing: -.03em; }
  .toolbar { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; margin-bottom: .75rem; padding: .75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
  .toolbar select, .toolbar input { padding: .45rem .65rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: .82rem; background: #fff; color: #0f172a; outline: none; }
  .toolbar select:focus, .toolbar input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,.15); }
  .toolbar input[type="text"] { min-width: 180px; flex: 1; }
  .toolbar label { font-size: .78rem; font-weight: 600; color: #475569; white-space: nowrap; }
  .toolbar-group { display: flex; align-items: center; gap: .35rem; }
  .booking-table { width: 100%; border-collapse: separate; border-spacing: 0; }
  .booking-table thead th { text-align: left; font-weight: 600; color: var(--text); padding: .65rem .75rem; border-bottom: 1px solid var(--border); background: #f8fafc; font-size: .82rem; }
  .booking-table tbody td { padding: .65rem .75rem; border-bottom: 1px solid var(--border); vertical-align: middle; color: var(--text); word-break: break-word; font-size: .88rem; }
  .badge { display: inline-block; padding: .2rem .55rem; border-radius: 999px; font-size: .72rem; font-weight: 700; white-space: nowrap; }
  .badge-new { background: #eff6ff; color: #1d4ed8; }
  .badge-process { background: #fef3c7; color: #92400e; }
  .badge-done { background: #dcfce7; color: #166534; }
  .badge-cancel { background: #fee2e2; color: #991b1b; }
  .pagination-bar { display: flex; justify-content: space-between; align-items: center; margin-top: .75rem; font-size: .82rem; color: #64748b; }
  .pagination-bar button { padding: .35rem .75rem; border: 1px solid #d1d5db; border-radius: 8px; background: #fff; cursor: pointer; font-size: .8rem; font-weight: 600; color: #334155; }
  .pagination-bar button:hover:not(:disabled) { background: #f1f5f9; border-color: #94a3b8; }
  .pagination-bar button:disabled { opacity: .4; cursor: not-allowed; }
  .empty-msg { text-align: center; padding: 2rem; color: var(--muted); }
  .invoice-link { color: #2563eb; font-weight: 600; cursor: pointer; text-decoration: underline; text-underline-offset: 2px; }
  .invoice-link:hover { color: #1d4ed8; }

  .detail-backdrop { display: none; position: fixed; inset: 0; z-index: 9998; background: rgba(15,23,42,.45); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); }
  .detail-backdrop.open { display: flex; align-items: center; justify-content: center; }
  .detail-modal { width: min(900px, 96%); max-height: 90vh; overflow: auto; background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 24px 70px rgba(15,23,42,.35); }
  .detail-head { display: flex; justify-content: space-between; align-items: flex-start; padding: 1.25rem 1.5rem .75rem; border-bottom: 1px solid #f1f5f9; }
  .detail-head h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
  .detail-head .sub { font-size: .82rem; color: #64748b; margin-top: .15rem; }
  .detail-close { border: 1px solid #cbd5e1; background: #fff; color: #0f172a; border-radius: 999px; width: 36px; height: 36px; font-size: 1rem; cursor: pointer; flex: 0 0 auto; display: flex; align-items: center; justify-content: center; }
  .detail-close:hover { background: #f8fafc; }
  .detail-body { padding: 1rem 1.5rem 1.5rem; }
  .detail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: .75rem; margin-bottom: 1rem; }
  .detail-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: .65rem .8rem; }
  .detail-label { font-size: .72rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .03em; margin-bottom: .15rem; display: block; }
  .detail-value { font-size: .88rem; font-weight: 600; color: #0f172a; }
  .detail-section { margin-top: 1rem; }
  .detail-section h4 { margin: 0 0 .6rem; font-size: .9rem; font-weight: 700; color: #0f172a; }
  .detail-actions { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .75rem; }
  .action-link { display: inline-block; padding: .45rem .85rem; border-radius: 8px; font-size: .8rem; font-weight: 600; text-decoration: none; border: 1px solid #d1d5db; color: #334155; background: #fff; }
  .action-link:hover { background: #f1f5f9; border-color: #94a3b8; }
  .action-link--primary { background: #2563eb; color: #fff; border-color: #2563eb; }
  .action-link--primary:hover { background: #1d4ed8; }

  .group-filter-bar { display: flex; align-items: center; gap: .5rem; margin-bottom: 1rem; padding: .85rem 1rem; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; }
  .group-filter-bar label { font-size: .88rem; font-weight: 700; color: #0f172a; white-space: nowrap; }
  .group-filter-bar select { padding: .5rem .75rem; border: 1px solid #93c5fd; border-radius: 8px; font-size: .88rem; background: #fff; color: #0f172a; outline: none; font-weight: 600; min-width: 200px; }
  .group-filter-bar select:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,.15); }

  @media (max-width: 1200px) {
    .dashboard-wrap { grid-template-columns: 1fr; }
    .dashboard-sidebar { position: static; max-height: none; overflow: hidden; min-width: 0; }
  }
  @media (max-width: 768px) {
    .card { box-shadow: none; }
    .content-card { overflow: hidden; }
    .stat-cards { grid-template-columns: 1fr; }
    .chart-row { grid-template-columns: 1fr; }
    .toolbar { flex-direction: column; align-items: stretch; }
    .toolbar-group { width: 100%; }
    .toolbar input[type="text"] { min-width: 0; }
    .booking-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .booking-table { min-width: 520px; }
    .desktop-table { display: none !important; }
    .mobile-cards { display: block !important; }
    .detail-modal { width: 100%; border-radius: 0; max-height: 100vh; height: 100%; }
    .detail-grid { grid-template-columns: 1fr 1fr; }
    .group-filter-bar { flex-direction: column; align-items: stretch; }
    .group-filter-bar select { min-width: 0; }
  }
  .mobile-cards { display: none; }
  .mobile-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; margin-bottom: .75rem; }
  .mobile-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; margin-bottom: .35rem; }
  .mobile-card-title { font-weight: 700; color: #0f172a; font-size: .9rem; }
  .mobile-card-meta { font-size: .82rem; color: var(--muted); line-height: 1.5; }
</style>

@php
  $allRows = $rawBookings->values()->toArray();
@endphp

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <main class="content-card">
    <h2 style="margin:0 0 1rem;">Booking Data</h2>

    <div class="group-filter-bar">
      <label>Group:</label>
      <select id="filterGroup">
        <option value="">All Groups</option>
        @foreach($groups as $g)
          <option value="{{ $g->name }}">{{ $g->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="stat-cards">
      <div class="stat-card" style="background:#f0f9ff; border-color:#bae6fd;">
        <div class="stat-label">Total Bookings</div>
        <div class="stat-value" style="color:#0284c7;" id="statTotal">0</div>
      </div>
      <div class="stat-card" style="background:#fffbeb; border-color:#fde68a;">
        <div class="stat-label">Processing</div>
        <div class="stat-value" style="color:#d97706;" id="statProcessing">0</div>
      </div>
      <div class="stat-card" style="background:#f0fdf4; border-color:#bbf7d0;">
        <div class="stat-label">Completed</div>
        <div class="stat-value" style="color:#16a34a;" id="statCompleted">0</div>
      </div>
      <div class="stat-card" style="background:#fef2f2; border-color:#fecaca;">
        <div class="stat-label">Canceled</div>
        <div class="stat-value" style="color:#dc2626;" id="statCancelled">0</div>
      </div>
    </div>

    <div class="revenue-card">
      <div class="label">Total Revenue</div>
      <div class="value" id="statRevenue">Rp 0</div>
    </div>

    <div class="chart-card" style="margin-bottom:1rem;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.5rem; margin-bottom:.75rem;">
        <h3 style="margin:0;">Revenue History</h3>
        <div style="display:flex; align-items:center; gap:.35rem;">
          <label style="font-size:.78rem; font-weight:600; color:#475569;">From:</label>
          <input type="month" id="revFrom" style="padding:.4rem .55rem; border:1px solid #d1d5db; border-radius:8px; font-size:.82rem; background:#fff; color:#0f172a; outline:none;">
          <label style="font-size:.78rem; font-weight:600; color:#475569;">To:</label>
          <input type="month" id="revTo" style="padding:.4rem .55rem; border:1px solid #d1d5db; border-radius:8px; font-size:.82rem; background:#fff; color:#0f172a; outline:none;">
        </div>
      </div>
      <div style="position:relative; height:260px;">
        <canvas id="revenueHistoryChart"></canvas>
      </div>
    </div>

    <div class="chart-card" style="margin-bottom:1rem;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.5rem; margin-bottom:.75rem;">
        <h3 style="margin:0;">Revenue History (Completed)</h3>
        <div style="display:flex; align-items:center; gap:.35rem;">
          <label style="font-size:.78rem; font-weight:600; color:#475569;">From:</label>
          <input type="month" id="revCompFrom" style="padding:.4rem .55rem; border:1px solid #d1d5db; border-radius:8px; font-size:.82rem; background:#fff; color:#0f172a; outline:none;">
          <label style="font-size:.78rem; font-weight:600; color:#475569;">To:</label>
          <input type="month" id="revCompTo" style="padding:.4rem .55rem; border:1px solid #d1d5db; border-radius:8px; font-size:.82rem; background:#fff; color:#0f172a; outline:none;">
        </div>
      </div>
      <div style="position:relative; height:260px;">
        <canvas id="revenueHistoryCompletedChart"></canvas>
      </div>
    </div>

    <div class="chart-row">
      <div class="chart-card">
        <h3>Payment Methods</h3>
        <div class="chart-container"><canvas id="paymentChart"></canvas></div>
      </div>
      <div class="chart-card">
        <h3>Info Source</h3>
        <div class="chart-container"><canvas id="infoSourceChart"></canvas></div>
      </div>
    </div>

    <div class="toolbar" id="toolbar">
      <div class="toolbar-group">
        <label>Rows:</label>
        <select id="perPage">
          <option value="5">5</option>
          <option value="10" selected>10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div class="toolbar-group">
        <label>Search:</label>
        <input type="text" id="searchInput" placeholder="Invoice, customer, vehicle...">
      </div>
      <div class="toolbar-group">
        <label>Filter:</label>
        <select id="filterStatus">
          <option value="">All Status</option>
          <option value="masuk">Booking Masuk</option>
          <option value="proses">Booking Proses</option>
          <option value="cancel">Booking Cancel</option>
          <option value="selesai">Booking Selesai</option>
        </select>
      </div>
      <div class="toolbar-group">
        <label>Sort:</label>
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

    <h3 style="margin:0 0 .75rem; font-size:1rem; font-weight:700;">All Bookings</h3>

    <div class="desktop-table">
      <div class="booking-table-wrap">
        <table class="booking-table">
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

    <div class="pagination-bar">
      <span id="pageInfo"></span>
      <div style="display:flex; gap:.35rem;">
        <button id="btnPrev" disabled>&laquo; Prev</button>
        <button id="btnNext" disabled>Next &raquo;</button>
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
@endsection
