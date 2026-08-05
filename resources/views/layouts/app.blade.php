<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BDT Rental' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css','resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = { theme: { fontFamily: { sans: ['Instrument Sans','ui-sans-serif','system-ui','sans-serif'] } } }
        </script>
    @endif
    <style>
        :root { --primary: #2563eb; --bg: #ffffff; --card: #ffffff; --text: #1f2937; --muted: #6b7280; --border: #e5e7eb; }
        body { background: var(--bg); color: var(--text); overflow-x: hidden; }
        body.admin-shell { overflow-x: hidden; }
        .container { max-width: 980px; margin: 1rem auto 2rem; padding: 0 1rem; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 8px 20px rgba(0,0,0,.06); }
        .card-header { padding: 1.25rem 1.25rem 0; }
        .card-body { padding: 1.25rem; }
        .subtitle { color: var(--muted); font-size: .95rem; }
        .form-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 1rem; }
        .col-12 { grid-column: span 12; }
        .col-6 { grid-column: span 6; }
        .col-4 { grid-column: span 4; }
        .col-3 { grid-column: span 3; }
        label { display: block; font-weight: 600; margin-bottom: .4rem; color: var(--text); font-size: .9rem; }
        .field { display: block; }
        .field label { width: auto; white-space: normal; }
        .field input[type="text"], .field input[type="email"], .field input[type="number"], .field input[type="date"], .field input[type="time"], .field select {
            width: 100%; padding: .75rem .9rem; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; color: var(--text); height: 44px;
        }
        .field input[type="password"] {
            width: 100%; padding: .75rem .9rem; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; color: var(--text); height: 44px;
        }
        .field textarea { min-height: 110px; }
        .btn { display: inline-block; padding: .8rem 1.2rem; border-radius: 12px; text-decoration: none; font-weight: 600; }
        .btn-primary { background: var(--primary); color: white; border: none; }
        .btn-primary:hover { filter: brightness(1.05); }
        .actions { display: flex; gap: .75rem; justify-content: flex-end; margin-top: .5rem; }
        .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-wrap > table { min-width: 760px; }
        .icon-actions { display:flex; gap:.35rem; flex-wrap:wrap; }
        .search-select { position: relative; }
        .search-select .search-input { width: 100%; padding: .75rem .9rem; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; color: var(--text); }
        .search-select .search-dropdown { position: absolute; left: 0; right: 0; margin-top: .25rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,.08); max-height: 220px; overflow-y: auto; z-index: 30; display: none; }
        .search-select.open .search-dropdown { display: block; }
        .search-item { padding: .6rem .9rem; cursor: pointer; }
        .search-item:hover { background: #f1f5f9; }
        .search-empty { padding: .6rem .9rem; color: var(--muted); }
        .dashboard-shell,
        .dashboard-wrap { display: grid; gap: 1.25rem; align-items: start; }
        .dashboard-shell { grid-template-columns: 250px minmax(0, 1fr) 320px; min-height: calc(100vh - 7rem); }
        .dashboard-wrap { grid-template-columns: 240px minmax(0, 1fr) 280px; }
        .dashboard-sidebar,
        .sidebar,
        .calendar-side {
            background: rgba(255,255,255,.9);
            border: 1px solid rgba(226,232,240,.95);
            border-radius: 24px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
            backdrop-filter: blur(10px);
            padding: 1rem;
            position: sticky;
            top: 5.75rem;
            max-height: calc(100vh - 7.25rem);
            overflow-y: auto;
            height: fit-content;
        }
        .dashboard-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding-bottom: .95rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .dashboard-brand img { width: 44px; height: 44px; object-fit: contain; }
        .dashboard-brand h3 { margin: 0; font-size: 1rem; font-weight: 800; letter-spacing: -.02em; }
        .dashboard-brand .subtitle { font-size: .85rem; }
        .dashboard-section-label {
            margin-top: 1rem;
            margin-bottom: .55rem;
            font-size: .82rem;
            font-weight: 800;
            color: #94a3b8;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .dashboard-menu,
        .side-menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: .45rem;
        }
        .dashboard-menu a,
        .dashboard-menu button,
        .side-menu a,
        .side-menu button {
            display: flex;
            align-items: center;
            gap: .7rem;
            width: 100%;
            padding: .76rem .9rem;
            border-radius: 14px;
            text-decoration: none;
            color: var(--text);
            border: 1px solid #e2e8f0;
            background: #fff;
            font-weight: 600;
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease, background .15s ease;
        }
        .dashboard-menu a:hover,
        .dashboard-menu button:hover,
        .side-menu a:hover,
        .side-menu button:hover {
            transform: translateY(-1px);
            border-color: #c7d2fe;
            box-shadow: 0 10px 18px rgba(15, 23, 42, .06);
            background: #f8fafc;
        }
        .dashboard-menu a.active,
        .side-menu a.active {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 12px 24px rgba(37, 99, 235, .25);
        }
        .homepage-header {
            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }
        .homepage-header-inner {
            max-width: 1120px;
            margin: 0 auto;
            min-height: 90px;
            padding: .8rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .homepage-brand {
            display: inline-flex;
            align-items: center;
        }
        .homepage-brand img {
            display: block;
            width: 130px;
            height: auto;
            object-fit: contain;
        }
        .homepage-nav {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
        }
        .homepage-nav a {
            text-decoration: none;
            color: var(--text);
            padding: .6rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            transition: background .15s ease, color .15s ease, box-shadow .15s ease;
        }
        .homepage-nav a:hover { background: #f8fafc; }
        .homepage-nav a.active {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(37, 99, 235, .18);
        }
        body.homepage-with-header .container {
            margin-top: 1.25rem;
        }
        body.admin-shell .container {
            max-width: 100% !important;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .sidebar-toggle-btn { position: fixed; left: 16px; bottom: 20px; z-index: 70; display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: 1px solid #e2e8f0; background: #ffffff; color: #64748b; border-radius: 12px; padding: .5rem .75rem; font-size: .82rem; font-weight: 600; box-shadow: 0 4px 12px rgba(15, 23, 42, .08); cursor: pointer; transition: all .2s ease; }
        .sidebar-toggle-btn:hover { background: #f1f5f9; color: #334155; border-color: #cbd5e1; box-shadow: 0 6px 16px rgba(15, 23, 42, .12); }
        .sidebar-toggle-btn svg { width: 14px; height: 14px; }
        body.sidebar-collapsed .dashboard-wrap { grid-template-columns: 1fr !important; }
        body.sidebar-collapsed .dashboard-shell { grid-template-columns: 1fr !important; }
        body.sidebar-collapsed .dashboard-sidebar { display: none !important; }
        body.sidebar-collapsed .sidebar { display: none !important; }
        body.sidebar-collapsed .calendar-side { display: none !important; }
        @media (max-width: 1024px) {
            .container { max-width: 820px; }
            .form-grid { grid-template-columns: repeat(6, 1fr); }
            .col-12 { grid-column: span 6; }
            .col-6 { grid-column: span 6; }
            .col-4 { grid-column: span 6; }
            .col-3 { grid-column: span 3; }
            .table-wrap > table { min-width: 640px; }
            .dashboard-shell,
            .dashboard-wrap { grid-template-columns: 1fr !important; }
            .dashboard-sidebar,
            .sidebar,
            .calendar-side { position: static; top: auto; max-height: none; }
            .content-card { min-width: 0; overflow: hidden; }
        }
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 80;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            box-shadow: 0 -4px 16px rgba(0,0,0,.06);
            padding: .4rem 0 env(safe-area-inset-bottom, .4rem);
        }
        .mobile-bottom-nav-inner {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 480px;
            margin: 0 auto;
        }
        .mobile-bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .2rem;
            text-decoration: none;
            color: #94a3b8;
            font-size: .65rem;
            font-weight: 600;
            padding: .35rem .5rem;
            border-radius: 10px;
            transition: color .15s ease, background .15s ease;
            min-width: 60px;
        }
        .mobile-bottom-nav a svg {
            width: 22px;
            height: 22px;
            stroke-width: 1.8;
        }
        .mobile-bottom-nav a.active {
            color: #2563eb;
            background: #eff6ff;
        }
        @media (max-width: 768px) {
            .mobile-bottom-nav { display: block; }
            body.admin-shell .container { padding-bottom: 4.5rem; }
        }
        @media (max-width: 640px) {
            .container { max-width: 100%; margin: 1rem auto; padding: 0 .75rem; }
            .form-grid { grid-template-columns: repeat(12, 1fr); }
            .col-12, .col-6, .col-4, .col-3 { grid-column: span 12; }
            .actions { justify-content: stretch; }
            .actions .btn { width: 100%; }
            .table-wrap > table { min-width: 560px; }
        }
        .wa-float { position: fixed; right: 20px; bottom: 20px; z-index: 60; display: flex; align-items: center; gap: .6rem; background: linear-gradient(135deg, #25D366 0%, #22c35e 100%); color: #ffffff; border-radius: 999px; padding: .7rem 1rem; box-shadow: 0 12px 24px rgba(34, 195, 94, .35); text-decoration: none; transition: transform .2s ease, box-shadow .2s ease; }
        .wa-float:hover { transform: translateY(-2px); box-shadow: 0 16px 30px rgba(34, 195, 94, .45); }
        .wa-icon-wrap { width: 28px; height: 28px; border-radius: 999px; background: rgba(255,255,255,.18); display: flex; align-items: center; justify-content: center; }
        .wa-icon { width: 18px; height: 18px; color: #000000; }
        .wa-label { font-weight: 700; color: #ffffff; letter-spacing: .2px; }
        @media (max-width: 640px) {
            .wa-label { display: none; }
            .wa-float { padding: .6rem; }
            .homepage-header-inner {
                min-height: 76px;
                padding: .7rem .75rem;
            }
            .homepage-brand img { width: 112px; }
            .homepage-nav { gap: .35rem; }
            .homepage-nav a { padding: .5rem .8rem; font-size: .95rem; }
        }
    </style>
</head>
@php($showHomepageHeader = request()->routeIs('booking.create'))
@php($isAdminShell = request()->routeIs('dashboard*', 'bookings.*', 'vehicles.*', 'services.*', 'mitras.*', 'groups.*', 'settings.*', 'email-logs.*', 'itineraries.*', 'accounts.*', 'booking-data.*', 'user.bookings.*'))
<body class="{{ $showHomepageHeader ? 'homepage-with-header' : '' }} {{ $isAdminShell ? 'admin-shell' : '' }}">
    @if($showHomepageHeader)
    <header class="homepage-header">
        <div class="homepage-header-inner">
            <a href="{{ url('/') }}" class="homepage-brand" aria-label="BDT Rental home">
                <img src="{{ asset('Logo-Bandung-Driver-Tour.webp') }}" alt="Bandung Driver Tour">
            </a>
            <nav class="homepage-nav" aria-label="Primary">
                @auth
                    <a href="{{ route('dashboard') }}" class="active">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="active">Login</a>
                @endauth
            </nav>
        </div>
    </header>
    @endif
    <main class="container">
        @if(session('success') && !request()->routeIs('booking.create'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif
        @if(session('error'))
            <p style="color: red;">{{ session('error') }}</p>
        @endif
        @yield('content')
    </main>
    <a class="wa-float" href="https://wa.me/6282129292884?text=I%20need%20assistance%20with%20my%20booking." target="_blank" rel="noopener" aria-label="Contact admin via WhatsApp">
        <span class="wa-icon-wrap">
            <svg class="wa-icon" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M19.11 17.93c-.3-.17-1.76-.86-2.03-.96-.27-.1-.47-.17-.66.1-.19.27-.76.96-.93 1.16-.17.2-.34.22-.64.08-.3-.15-1.28-.47-2.43-1.5-.9-.8-1.51-1.79-1.68-2.09-.17-.3-.02-.46.13-.62.13-.13.3-.34.44-.51.15-.17.2-.3.31-.5.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.91-2.18-.24-.58-.48-.5-.66-.5-.17 0-.37-.02-.57-.02-.2 0-.52.07-.8.37-.27.3-1.05 1.02-1.05 2.49s1.08 2.88 1.23 3.08c.15.2 2.12 3.23 5.14 4.52.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.08-.12-.28-.19-.58-.34z"/><path d="M27.2 4.8C24.3 1.9 20.4.2 16.3.3 8.5.4 2.1 6.7 2.1 14.5c0 2.6.7 5.1 2.1 7.3L2 30l8.4-2.2c2.1 1.1 4.5 1.7 6.9 1.7h.1c7.8 0 14.2-6.3 14.3-14.1.1-3.8-1.3-7.3-3.9-10.1zM17.4 27.5h-.1c-2.2 0-4.4-.6-6.3-1.6l-.5-.3-5 1.3 1.4-4.8-.3-.5c-1.4-2.1-2.1-4.6-2.1-7.1 0-6.8 5.5-12.3 12.3-12.3 3.3 0 6.4 1.3 8.8 3.7 2.4 2.4 3.7 5.6 3.6 9-.1 6.8-5.6 12.3-12.5 12.6z"/></svg>
        </span>
        <span class="wa-label">Contact Admin</span>
    </a>
    @if($isAdminShell)
    <nav class="mobile-bottom-nav" aria-label="Mobile navigation">
        <div class="mobile-bottom-nav-inner">
            <a href="{{ route('booking.create') }}" class="{{ request()->routeIs('booking.create') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Home
            </a>
            <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Booking
            </a>
            <a href="{{ route('laporan-keuangan.index') }}" class="{{ request()->routeIs('laporan-keuangan.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Keuangan
            </a>
            <a href="{{ route('dashboard.calendar') }}" class="{{ request()->routeIs('dashboard.calendar') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Kalender
            </a>
        </div>
    </nav>
    @endif
    <script>
        (function () {
            const storageKey = 'bdt_sidebar_collapsed';
            const collapsed = localStorage.getItem(storageKey) === '1';

            function setCollapsedState(nextCollapsed) {
                document.body.classList.toggle('sidebar-collapsed', nextCollapsed);
                localStorage.setItem(storageKey, nextCollapsed ? '1' : '0');
                document.querySelectorAll('.sidebar-toggle-btn').forEach((button) => {
                    button.innerHTML = nextCollapsed
                        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg> Show'
                        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg> Hide';
                });
            }

            function createToggleButton() {
                if (!document.querySelector('.sidebar, .dashboard-sidebar, .calendar-side')) return;
                if (document.getElementById('sidebarToggleButton')) return;
                const toggleButton = document.createElement('button');
                toggleButton.type = 'button';
                toggleButton.className = 'sidebar-toggle-btn';
                toggleButton.id = 'sidebarToggleButton';
                toggleButton.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg> Hide';
                toggleButton.addEventListener('click', function () {
                    setCollapsedState(!document.body.classList.contains('sidebar-collapsed'));
                });
                document.body.appendChild(toggleButton);
            }

            document.addEventListener('DOMContentLoaded', function () {
                createToggleButton();
                setCollapsedState(collapsed);
            });
        })();
    </script>
    @auth
    @if(auth()->user()?->role === 'super_admin')
    <script>
      (function () {
        if ('serviceWorker' in navigator) {
          navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
        const endpoint = @json(route('notifications.invoices.latest'));
        const pushSubscribeEndpoint = @json(route('notifications.push.subscribe'));
        const vapidPublicKey = @json(config('services.webpush.public_key'));
        const storageKey = 'bdt_last_seen_invoice_id';
        const pollMs = 12000;
        let isFirstFetch = true;
        let audioCtx = null;

        function base64UrlToUint8Array(base64String) {
          const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
          const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
          const rawData = atob(base64);
          const outputArray = new Uint8Array(rawData.length);
          for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
          }
          return outputArray;
        }

        async function subscribeForPush() {
          if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
          if (!vapidPublicKey) return;
          if (!('Notification' in window)) return;

          if (Notification.permission === 'default') {
            try { await Notification.requestPermission(); } catch (_) {}
          }
          if (Notification.permission !== 'granted') return;

          try {
            const reg = await navigator.serviceWorker.ready;
            let sub = await reg.pushManager.getSubscription();
            if (!sub) {
              sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: base64UrlToUint8Array(vapidPublicKey),
              });
            }
            await fetch(pushSubscribeEndpoint, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
                'X-Requested-With': 'XMLHttpRequest',
              },
              credentials: 'same-origin',
              body: JSON.stringify(sub.toJSON()),
            });
          } catch (_) {}
        }

        function ensureAudioContext() {
          if (!audioCtx) {
            const Ctx = window.AudioContext || window.webkitAudioContext;
            if (Ctx) audioCtx = new Ctx();
          }
          if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume().catch(() => {});
          }
        }

        function playBeep() {
          try {
            ensureAudioContext();
            if (!audioCtx) return;
            const now = audioCtx.currentTime;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, now);
            gain.gain.setValueAtTime(0.0001, now);
            gain.gain.exponentialRampToValueAtTime(0.15, now + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.35);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start(now);
            osc.stop(now + 0.36);
          } catch (_) {}
        }

        async function showBrowserNotification(title, bodyText) {
          if (!('Notification' in window)) return;
          if (Notification.permission === 'default') {
            try { await Notification.requestPermission(); } catch (_) {}
          }
          if (Notification.permission !== 'granted') return;
          const text = bodyText || 'Invoice baru telah dibuat.';
          try {
            const reg = await navigator.serviceWorker?.getRegistration();
            if (reg) {
              reg.showNotification(title, {
                body: text,
                badge: '/favicon.ico',
                icon: '/favicon.ico',
                vibrate: [100, 50, 100],
                tag: 'invoice-new',
              });
              return;
            }
          } catch (_) {}
          new Notification(title, { body: text, icon: '/favicon.ico', tag: 'invoice-new' });
        }

        async function pollLatestInvoice() {
          try {
            const res = await fetch(endpoint, {
              headers: { 'X-Requested-With': 'XMLHttpRequest' },
              credentials: 'same-origin',
              cache: 'no-store',
            });
            if (!res.ok) return;
            const data = await res.json();
            const latestId = Number(data.latest_id || 0);
            if (!latestId) return;

            const stored = Number(localStorage.getItem(storageKey) || 0);
            if (isFirstFetch) {
              localStorage.setItem(storageKey, String(Math.max(stored, latestId)));
              isFirstFetch = false;
              return;
            }

            if (latestId > stored) {
              localStorage.setItem(storageKey, String(latestId));
              const title = 'Invoice Baru Masuk';
              const amount = Number(data.amount || 0).toLocaleString('id-ID');
              const body = `${data.invoice_number || 'Invoice'} - ${data.customer_name || 'Customer'} - IDR ${amount}`;
              playBeep();
              showBrowserNotification(title, body);
            }
          } catch (_) {}
        }

        document.addEventListener('click', ensureAudioContext, { once: true });
        document.addEventListener('keydown', ensureAudioContext, { once: true });
        subscribeForPush();
        pollLatestInvoice();
        setInterval(pollLatestInvoice, pollMs);
      })();
    </script>
    @endif
    @endauth
</body>
</html>
