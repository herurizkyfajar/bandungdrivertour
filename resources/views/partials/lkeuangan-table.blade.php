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
