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
