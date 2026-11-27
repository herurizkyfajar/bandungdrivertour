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
