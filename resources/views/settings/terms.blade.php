@extends('layouts.app', ['title' => 'Rental Duration & Service Terms'])

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
  .vm-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 1.25rem;
    align-items: start;
    min-height: calc(100vh - 6rem);
    padding-left: 252px;
  }
  .vm-main {
    display: grid;
    gap: 1.05rem;
    align-content: start;
    min-width: 0;
    min-height: 0;
    padding: 0 0 1rem;
  }

  /* ===== Sidebar (sama dengan dashboard) ===== */
  .vm-shell > .dashboard-sidebar {
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
  .vm-shell > .dashboard-sidebar::after {
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
  .vm-shell .dashboard-brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .45rem;
    padding: 0 .3rem 1rem;
    margin-bottom: .75rem;
    border-bottom: 1px solid rgba(255, 255, 255, .18);
  }
  .vm-shell .dashboard-brand img {
    width: 142px;
    height: auto;
    padding: .3rem .45rem;
    border-radius: 10px;
    background: #ffffff;
    object-fit: contain;
  }
  .vm-shell .dashboard-brand h3 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #ffffff;
  }
  .vm-shell .dashboard-brand .subtitle {
    font-size: .82rem;
    color: rgba(255, 255, 255, .82);
  }
  .vm-shell .dashboard-section-label {
    margin: .85rem .3rem .4rem;
    padding: 0;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .6);
  }
  .vm-shell .dashboard-menu {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: .12rem;
  }
  .vm-shell .dashboard-menu a,
  .vm-shell .dashboard-menu button {
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
  .vm-shell .dashboard-menu a:hover,
  .vm-shell .dashboard-menu button:hover {
    background: rgba(255, 255, 255, .12);
    color: #ffffff;
  }
  .vm-shell .dashboard-menu a.active {
    background: #ffffff;
    color: #0b4fd6;
    box-shadow: 0 6px 14px rgba(4, 33, 90, .22);
  }
  .vm-shell .dashboard-menu .nav-icon {
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
  .vm-shell .dashboard-menu a.active .nav-icon {
    background: #e7f0ff;
    color: #0b4fd6;
  }

  /* ===== Topbar ===== */
  .vm-topbar {
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
  .vm-topbar-left { display: flex; align-items: center; gap: .6rem; min-width: 0; }
  .vm-topbar-right { display: flex; align-items: center; gap: .45rem; flex: 0 0 auto; }
  .vm-menu-btn {
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
  .vm-page-label {
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
  .vm-page-label i { color: #1565c0; font-size: .95rem; }
  .vm-icon-btn {
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
  .vm-icon-btn:hover { background: #f4f9ff; border-color: #bcd5f5; color: #0b4fd6; }
  .vm-user-wrap { position: relative; }
  .vm-user {
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
  .vm-user:hover { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }
  .vm-user[aria-expanded="true"] { border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .07); }
  .vm-avatar {
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
  .vm-user-meta { display: flex; flex-direction: column; line-height: 1.15; min-width: 0; overflow: hidden; }
  .vm-user-meta strong { max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .82rem; font-weight: 800; color: #0b2545; }
  .vm-user-meta span { font-size: .7rem; color: #64748b; text-transform: capitalize; }
  .vm-user > i { color: #94a3b8; font-size: .75rem; transition: transform .15s ease; }
  .vm-user[aria-expanded="true"] > i { transform: rotate(180deg); }
  .vm-user-menu {
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
  .vm-user-menu[hidden] { display: none; }
  .vm-user-menu-head {
    display: flex;
    flex-direction: column;
    gap: .15rem;
    padding: .55rem .7rem .6rem;
    margin-bottom: .35rem;
    border-bottom: 1px solid #eef2f8;
  }
  .vm-user-menu-head strong { font-size: .82rem; font-weight: 800; color: #0b2545; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .vm-user-menu-head span { font-size: .72rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .vm-user-menu-item {
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
  .vm-user-menu-item i { font-size: .95rem; color: #64748b; }
  .vm-user-menu-item:hover { background: #f1f5fb; }
  .vm-user-menu-item--danger { color: #b91c1c; }
  .vm-user-menu-item--danger i { color: #b91c1c; }
  .vm-user-menu-item--danger:hover { background: #fef2f2; }

  /* ===== Hero ===== */
  .vm-hero {
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
  .vm-hero::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 5px;
    background: linear-gradient(90deg, #22c55e 0%, #84d94e 38%, #2f80ed 100%);
    opacity: .9;
  }
  .vm-hero-content { position: relative; z-index: 2; flex: 1 1 auto; min-width: 0; }
  .vm-eyebrow {
    margin-bottom: .3rem;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .14em;
    color: #1d6fe0;
  }
  .vm-hero h2 { margin: 0; font-size: 1.85rem; font-weight: 800; letter-spacing: -.03em; color: #0b2545; }
  .vm-hero-actions {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
    flex: 0 0 auto;
  }
  .vm-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .68rem 1.05rem;
    border-radius: 12px;
    border: 1px solid transparent;
    font-size: .88rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .vm-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(11, 61, 145, .22); }
  .vm-btn-primary { background: #0b4fd6; color: #ffffff; border: 1px solid transparent; }
  .vm-btn-primary:hover { background: #093fa9; }
  .vm-btn:not(.vm-btn-primary) { background: #ffffff; color: #0b4fd6; border-color: #cfe0f7; box-shadow: 0 8px 18px rgba(15, 23, 42, .1); }
  .vm-btn:not(.vm-btn-primary):hover { background: #f6faff; }
  .vm-btn--danger { background: #dc2626; color: #ffffff; border: 1px solid transparent; }
  .vm-btn--danger:hover { background: #b91c1c; }

  /* ===== Kartu konten ===== */
  .vm-card {
    background: #ffffff;
    border: 1px solid #e8f0fb;
    border-radius: 20px;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 10px 24px rgba(13, 43, 82, .06);
    min-width: 0;
  }
  .vm-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .6rem;
    flex-wrap: wrap;
    margin-bottom: .85rem;
  }
  .vm-card-head h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: .45rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0b2545;
    letter-spacing: -.01em;
  }
  .vm-card-head h3 i { color: #1565c0; font-size: 1rem; }

  .vm-card-section {
    background: #f7faff;
    border: 1px solid #e8f0fb;
    border-radius: 14px;
    padding: 1.1rem 1.2rem;
    margin-bottom: 1rem;
  }

  /* ===== Form ===== */
  .vm-form-field { margin-bottom: .85rem; }
  .vm-form-field label { display: block; font-size: .82rem; font-weight: 700; color: #334155; margin-bottom: .35rem; }
  .vm-form-field input,
  .vm-form-field select,
  .vm-form-field textarea {
    width: 100%;
    padding: .55rem .8rem;
    border: 1px solid #dbe7f6;
    border-radius: 10px;
    background: #ffffff;
    color: #0b2545;
    font-size: .85rem;
  }
  .vm-form-field input:focus,
  .vm-form-field select:focus,
  .vm-form-field textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .14);
    outline: none;
  }
  .vm-form-field textarea { min-height: 90px; resize: vertical; }
  .vm-form-hint { font-size: .75rem; color: #64748b; margin-top: .25rem; display: block; }

  /* ===== Flash message ===== */
  .vm-flash { padding: .75rem 1rem; border-radius: 10px; margin-bottom: 1rem; font-weight: 600; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

  /* ===== Actions ===== */
  .vm-actions { display: flex; gap: .5rem; margin-top: 1rem; }

  /* ===== Responsive ===== */
  @media (max-width: 1200px) {
    .vm-shell { padding-left: 0; }
    .vm-shell > .dashboard-sidebar { display: none !important; }
    .vm-menu-btn { display: inline-flex; }
    body.mobile-sidebar-open .vm-shell > .dashboard-sidebar {
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
    .vm-topbar { padding: .55rem .7rem; border-radius: 16px; }
    .vm-page-label span { display: none; }
    .vm-user { padding: .3rem; border: none; background: transparent; }
    .vm-user-meta, .vm-user > i { display: none; }
    .vm-hero { flex-direction: column; align-items: stretch; gap: .9rem; min-height: 0; padding: 1.15rem 1.15rem 1.4rem; border-radius: 18px; }
    .vm-hero h2 { font-size: 1.45rem; }
    .vm-hero-actions { width: 100%; justify-content: space-between; }
    .vm-hero-actions .vm-btn { flex: 1 1 auto; }
    body.mobile-sidebar-open .vm-shell > .dashboard-sidebar {
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
</style>
<div class="vm-shell">
  @include('partials.admin-sidebar')

  <main class="vm-main">
    <div class="vm-topbar">
      <div class="vm-topbar-left">
        <button class="vm-menu-btn" id="vmMenuBtn" type="button" aria-label="Buka menu"><i class="bi bi-list"></i></button>
        <div class="vm-page-label"><i class="bi bi-file-text"></i><span>Rental Duration & Service Terms</span></div>
      </div>
      <div class="vm-topbar-right">
        <a class="vm-icon-btn" href="{{ route('dashboard') }}" title="Kembali ke Dashboard" aria-label="Kembali ke Dashboard"><i class="bi bi-house-door"></i></a>
        <a class="vm-icon-btn" href="{{ route('dashboard.calendar') }}" title="Kalender booking" aria-label="Kalender booking"><i class="bi bi-calendar3"></i></a>
        <div class="vm-user-wrap" id="vmUserWrap">
          <button class="vm-user" id="vmUserBtn" type="button" title="{{ auth()->user()->name ?? 'Pengguna' }}" aria-haspopup="true" aria-expanded="false" aria-controls="vmUserMenu">
            <div class="vm-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
            <div class="vm-user-meta">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ str_replace('_', ' ', auth()->user()->role ?? 'user') }}</span>
            </div>
            <i class="bi bi-chevron-down"></i>
          </button>
          <div class="vm-user-menu" id="vmUserMenu" role="menu" aria-labelledby="vmUserBtn" hidden>
            <div class="vm-user-menu-head">
              <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>
              <span>{{ auth()->user()->email ?? '' }}</span>
            </div>
            <a class="vm-user-menu-item" href="{{ route('accounts.show', auth()->id()) }}" role="menuitem"><i class="bi bi-person"></i> Profil</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="vm-user-menu-item vm-user-menu-item--danger" role="menuitem"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <section class="vm-hero">
      <div class="vm-hero-content">
        <div class="vm-eyebrow">PENGATURAN</div>
        <h2>Rental Duration & Service Terms</h2>
      </div>
      <div class="vm-hero-actions">
        <a class="vm-btn vm-btn-primary" href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Kembali ke Dashboard</a>
        <a class="vm-btn" href="{{ route('dashboard.calendar') }}"><i class="bi bi-calendar3"></i> Kalender</a>
      </div>
    </section>

    <div class="vm-card">
      <div class="vm-card-head">
        <h3><i class="bi bi-file-text"></i>Ketentuan Sewa & Layanan</h3>
      </div>

    @if(session('success'))
      <div class="vm-flash">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('settings.terms.update') }}">
      @csrf
      @method('PUT')

      <div class="vm-card-section">
        <h3 style="margin:0 0 1rem; font-size:1rem; font-weight:700; color:#0f172a;">Isi Ketentuan</h3>
        <div id="toolbar" style="margin-bottom:.5rem; display:flex; gap:.35rem; flex-wrap:wrap; padding:.5rem; background:#fff; border:1px solid #cbd5e1; border-radius:12px 12px 0 0; border-bottom:none;">
          <button type="button" class="w-btn" data-cmd="bold" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; font-weight:700;">B</button>
          <button type="button" class="w-btn" data-cmd="italic" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; font-style:italic;">I</button>
          <button type="button" class="w-btn" data-cmd="underline" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; text-decoration:underline;">U</button>
          <div style="width:1px; background:#d1d5db; margin:0 .25rem;"></div>
          <button type="button" class="w-btn" data-cmd="formatBlock" data-value="h3" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; font-weight:700;">H3</button>
          <button type="button" class="w-btn" data-cmd="formatBlock" data-value="h4" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; font-weight:700;">H4</button>
          <button type="button" class="w-btn" data-cmd="formatBlock" data-value="p" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">P</button>
          <button type="button" class="w-btn" data-cmd="formatBlock" data-value="blockquote" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">Quote</button>
          <div style="width:1px; background:#d1d5db; margin:0 .25rem;"></div>
          <button type="button" class="w-btn" data-cmd="insertUnorderedList" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">&#8226; List</button>
          <button type="button" class="w-btn" data-cmd="insertOrderedList" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">1. List</button>
          <div style="width:1px; background:#d1d5db; margin:0 .25rem;"></div>
          <button type="button" class="w-btn" data-cmd="createLink" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">Link</button>
          <button type="button" class="w-btn" data-cmd="removeFormat" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">Clear</button>
        </div>
        <div id="terms_editor" class="w-content" contenteditable="true" style="min-height:400px; padding:1rem; border:1px solid #cbd5e1; border-radius:0 0 12px 12px; background:#fff; outline:none; line-height:1.7; font-size:.95rem;">{!! old('terms_html', $settings->terms_html) !!}</div>
        <input type="hidden" name="terms_html" id="terms_html_input" value="{{ old('terms_html', $settings->terms_html) }}">
      </div>

      <div class="vm-actions">
        <button type="submit" class="vm-btn vm-btn-primary">Save Terms</button>
      </div>
    </form>
  </div>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var editor = document.getElementById('terms_editor');
  var input = document.getElementById('terms_html_input');
  var toolbar = document.getElementById('toolbar');
  var savedRange = null;

  editor.addEventListener('mouseup', saveSelection);
  editor.addEventListener('keyup', saveSelection);

  function saveSelection() {
    var sel = window.getSelection();
    if (sel.rangeCount > 0) {
      savedRange = sel.getRangeAt(0);
    }
  }

  function restoreSelection() {
    if (savedRange) {
      var sel = window.getSelection();
      sel.removeAllRanges();
      sel.addRange(savedRange);
    } else {
      editor.focus();
    }
  }

  toolbar.addEventListener('mousedown', function(e) {
    e.preventDefault();
  });

  toolbar.addEventListener('click', function(e) {
    var btn = e.target.closest('.w-btn');
    if (!btn) return;
    e.preventDefault();

    var cmd = btn.getAttribute('data-cmd');

    if (cmd === 'insertUnorderedList') {
      restoreSelection();
      insertListHTML(false);
    } else if (cmd === 'insertOrderedList') {
      restoreSelection();
      insertListHTML(true);
    } else if (cmd === 'createLink') {
      restoreSelection();
      var url = prompt('Enter URL:');
      if (url) {
        document.execCommand('createLink', false, url);
      }
    } else if (cmd === 'removeFormat') {
      restoreSelection();
      document.execCommand('removeFormat', false, null);
    } else {
      restoreSelection();
      document.execCommand(cmd, false, btn.getAttribute('data-value') || null);
    }

    saveSelection();
  });

  function insertListHTML(ordered) {
    var tag = ordered ? 'ol' : 'ul';
    var sel = window.getSelection();
    if (sel.rangeCount === 0) return;

    var range = sel.getRangeAt(0);
    var selectedHtml = '';

    if (!range.collapsed) {
      var fragment = range.cloneContents();
      var tmp = document.createElement('div');
      tmp.appendChild(fragment);
      selectedHtml = tmp.innerHTML;
    }

    var listEl = document.createElement(tag);
    var liEl = document.createElement('li');
    if (selectedHtml) {
      liEl.innerHTML = selectedHtml;
    } else {
      liEl.innerHTML = '<br>';
    }
    listEl.appendChild(liEl);

    range.deleteContents();
    range.insertNode(listEl);

    range.setStart(liEl, 0);
    range.collapse(true);
    sel.removeAllRanges();
    sel.addRange(range);

    editor.focus();
    saveSelection();
  }

  var form = editor.closest('form');
  if (form) {
    form.addEventListener('submit', function() {
      input.value = editor.innerHTML;
    });
  }
});
</script>

<style>
  #terms_editor ul, #terms_editor ol { margin: 4px 0 8px 20px; padding-left: 20px; }
  #terms_editor ul { list-style: disc; }
  #terms_editor ol { list-style: decimal; }
  #terms_editor li { margin-bottom: 2px; }
  #terms_editor ul ul, #terms_editor ol ol, #terms_editor ul ol, #terms_editor ol ul { margin-top: 4px; margin-bottom: 4px; }
</style>
@endsection
