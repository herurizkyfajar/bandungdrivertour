<aside class="dashboard-sidebar">
  <div class="dashboard-brand">
    <img src="{{ asset('Logo-Bandung-Driver-Tour.webp') }}" alt="BDT Rental">
    <div>
      <h3>BDT Rental</h3>
      <div class="subtitle">{{ auth()->user()?->role === 'super_admin' ? 'Super Admin Panel' : 'User Panel' }}</div>
    </div>
  </div>

  @if(auth()->user()?->role === 'super_admin')
  <div class="dashboard-section-label">MAIN MENU</div>
  <ul class="dashboard-menu">
    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-house-door"></i></span>Dashboard</a></li>
    <li><a href="{{ route('dashboard.calendar') }}" class="{{ request()->routeIs('dashboard.calendar') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-calendar3"></i></span>Kalender</a></li>
    <li><a href="{{ route('booking.create') }}" class="{{ request()->routeIs('booking.create') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-pencil-square"></i></span>Booking Form</a></li>
    <li><a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-layers"></i></span>Manage Bookings</a></li>
    <li><a href="{{ route('booking-data.index') }}" class="{{ request()->routeIs('booking-data.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-database"></i></span>Booking Data</a></li>
    <li><a href="{{ route('laporan-keuangan.index') }}" class="{{ request()->routeIs('laporan-keuangan.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-bar-chart"></i></span>Laporan Keuangan</a></li>
    <li><a href="{{ route('laporan-mobil.index') }}" class="{{ request()->routeIs('laporan-mobil.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>Laporan Mobil</a></li>
    <li><a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-car-front"></i></span>Manage Vehicles</a></li>
    <li><a href="{{ route('mitras.index') }}" class="{{ request()->routeIs('mitras.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-people"></i></span>Manage Mitras</a></li>
    <li><a href="{{ route('services.index') }}" class="{{ request()->routeIs('services.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-tools"></i></span>Manage Services</a></li>
    <li><a href="{{ route('groups.index') }}" class="{{ request()->routeIs('groups.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-people-fill"></i></span>Manage Groups</a></li>
    <li><a href="{{ route('accounts.index') }}" class="{{ request()->routeIs('accounts.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-person"></i></span>Manage Accounts</a></li>
    <li><a href="{{ route('itineraries.index') }}" class="{{ request()->routeIs('itineraries.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-map"></i></span>Manage Itineraries</a></li>
    <li><a href="{{ route('spj-manuals.index') }}" class="{{ request()->routeIs('spj-manuals.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span>SPJ Manual</a></li>
  </ul>

  <div class="dashboard-section-label">Settings</div>
  <ul class="side-menu">
    <li><a href="{{ route('settings.invoice.edit') }}" class="{{ request()->routeIs('settings.invoice*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-receipt"></i></span>Invoice Settings</a></li>
    <li><a href="{{ route('settings.terms.edit') }}" class="{{ request()->routeIs('settings.terms*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-file-text"></i></span>Terms &amp; Conditions</a></li>
    <li><a href="{{ route('settings.notification-sound') }}" class="{{ request()->routeIs('settings.notification-sound*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-volume-up"></i></span>Sound Settings</a></li>
    <li><a href="{{ route('settings.smtp') }}" class="{{ request()->routeIs('settings.smtp*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-envelope"></i></span>SMTP Settings</a></li>
    <li><a href="{{ route('settings.telegram') }}" class="{{ request()->routeIs('settings.telegram*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-send"></i></span>Telegram Settings</a></li>
    <li><a href="{{ route('settings.whatsapp') }}" class="{{ request()->routeIs('settings.whatsapp*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-chat-dots"></i></span>WhatsApp Settings</a></li>
    <li><a href="{{ route('settings.tele-jadwal') }}" class="{{ request()->routeIs('settings.tele-jadwal*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-alarm"></i></span>Tele Jadwal</a></li>
    <li><a href="{{ route('email-logs.index') }}" class="{{ request()->routeIs('email-logs.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-envelope-open"></i></span>Log Email</a></li>
  </ul>

  <div class="dashboard-section-label">Quick Actions</div>
  <ul class="dashboard-menu">
    <li>
      <form method="POST" action="{{ route('dashboard.test-email') }}">
        @csrf
        <button type="submit"><span class="nav-icon"><i class="bi bi-envelope-paper"></i></span>Send Test Email</button>
      </form>
    </li>
    <li>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="width:100%; text-align:left;"><span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span>Logout</button>
      </form>
    </li>
  </ul>
  @else
  <div class="dashboard-section-label">Menu</div>
  <ul class="dashboard-menu">
    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-house-door"></i></span>Dashboard</a></li>
    <li><a href="{{ route('booking.create') }}" class="{{ request()->routeIs('booking.create') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-pencil-square"></i></span>Booking Form</a></li>
    <li><a href="{{ route('user.bookings.history') }}" class="{{ request()->routeIs('user.bookings.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-journal-text"></i></span>My Bookings</a></li>
    <li><a href="{{ route('itineraries.index') }}" class="{{ request()->routeIs('itineraries.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-map"></i></span>My Itineraries</a></li>
    <li><a href="{{ route('accounts.index') }}" class="{{ request()->routeIs('accounts.*') ? 'active' : '' }}"><span class="nav-icon"><i class="bi bi-person"></i></span>Manage Accounts</a></li>
  </ul>

  <div class="dashboard-section-label">Account</div>
  <ul class="dashboard-menu">
    <li>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="width:100%; text-align:left;"><span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span>Logout</button>
      </form>
    </li>
  </ul>
  @endif
</aside>
