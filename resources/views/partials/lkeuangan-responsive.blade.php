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
