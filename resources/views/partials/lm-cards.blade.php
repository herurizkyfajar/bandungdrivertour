  /* ===== Kartu statistik ===== */
  .lm-status-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .85rem; }

  .lm-status-card {
    display: flex;
    align-items: center;
    gap: .8rem;
    padding: .95rem 1rem;
    border-radius: 18px;
    border: 1px solid transparent;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .05);
  }

  .lm-status-icon {
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

  .lm-status-label { font-size: .82rem; font-weight: 700; color: #334155; }
  .lm-status-value { font-size: 1.5rem; font-weight: 800; letter-spacing: -.02em; color: #0f172a; line-height: 1.15; }

  .lm-status-card--blue { background: #eff6ff; border-color: #dbeafe; }
  .lm-status-card--blue .lm-status-icon { color: #2563eb; }
  .lm-status-card--amber { background: #fffbeb; border-color: #fef3c7; }
  .lm-status-card--amber .lm-status-icon { color: #d97706; }
  .lm-status-card--green { background: #f0fdf4; border-color: #dcfce7; }
  .lm-status-card--green .lm-status-icon { color: #16a34a; }
  .lm-status-card--purple { background: #faf5ff; border-color: #ede9fe; }
  .lm-status-card--purple .lm-status-icon { color: #a855f7; }
