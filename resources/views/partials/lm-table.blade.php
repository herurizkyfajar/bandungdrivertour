  /* ===== Kartu konten ===== */
  .lm-card {
    background: #ffffff;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
    min-width: 0;
  }

  .lm-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: .85rem;
  }

  .lm-card-head h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0b2545;
    letter-spacing: -.01em;
  }

  .lm-card-head h3 i { color: #1565c0; font-size: 1rem; }

  /* ===== Toolbar (filter periode & status) ===== */
  .lm-toolbar {
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

  .lm-field { display: flex; align-items: center; gap: .35rem; }
  .lm-toolbar label { margin: 0; font-size: .74rem; font-weight: 700; color: #64748b; white-space: nowrap; }

  .lm-toolbar select,
  .lm-toolbar input {
    padding: .45rem .6rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .8rem;
    outline: none;
  }

  .lm-toolbar select:focus,
  .lm-toolbar input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .14); }

  .lm-toolbar input[type="text"] { min-width: 180px; flex: 1; }

  .lm-toolbar button {
    padding: .45rem 1rem;
    border-radius: 10px;
    font-size: .8rem;
    font-weight: 700;
  }

  .lm-toolbar button { border: none; background: #0b4fd6; color: #ffffff; cursor: pointer; }
  .lm-toolbar button:hover { filter: brightness(1.05); }

  .lm-toolbar a.btn-primary { text-decoration: none; }

  .period-btn { padding: .45rem .9rem; font-size: .85rem; background: #fff; color: #334155; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; font-weight: 600; }
  .period-btn:hover { background: #f8fafc; }
  .period-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }

  /* ===== Tabel ===== */
  .lm-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border: 1px solid #e8f0fb; border-radius: 14px; }
  .lm-table { width: 100%; border-collapse: separate; border-spacing: 0; }

  .lm-table thead th {
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

  .lm-table tbody td {
    padding: .68rem .8rem;
    border-bottom: 1px solid #eef4fc;
    font-size: .85rem;
    color: #1f3a5f;
    vertical-align: middle;
    word-break: break-word;
  }

  .lm-table tbody tr:last-child td { border-bottom: none; }
  .lm-table tbody tr:hover td { background: #f9fbff; }

  .badge { display: inline-block; padding: .24rem .6rem; border-radius: 999px; font-size: .72rem; font-weight: 800; white-space: nowrap; }

  .empty-msg { text-align: center; padding: 2rem; color: #94a3b8; font-size: .86rem; }

  .lm-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-top: .9rem;
    font-size: .8rem;
    color: #64748b;
  }

  .lm-pagination button {
    padding: .42rem .8rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .78rem;
    font-weight: 700;
    cursor: pointer;
  }

  .lm-pagination button:hover:not(:disabled) { background: #f2f7ff; border-color: #bcd5f5; }
  .lm-pagination button:disabled { opacity: .45; cursor: not-allowed; }

  /* ===== Chart ===== */
  .lm-chart-box { position: relative; height: 280px; }
  .lm-chart-card {
    background: #ffffff;
    border: 1px solid #e8f0fb;
    border-radius: 14px;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
    min-width: 0;
  }

  .lm-chart-card h3 {
    margin: 0 0 .75rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0b2545;
    display: flex;
    align-items: center;
    gap: .45rem;
  }

  .lm-chart-card h3 i { color: #1565c0; font-size: 1rem; }

  /* ===== Usage bar ===== */
  .usage-bar { background: #e2e8f0; border-radius: 999px; height: 8px; overflow: hidden; min-width: 80px; }
  .usage-bar > span { display: block; height: 100%; background: #2563eb; border-radius: 999px; }
