<aside class="dashboard-sidebar">
  <div class="dashboard-brand">
    <img src="{{ asset('Logo-Bandung-Driver-Tour.webp') }}" alt="BDT Rental">
    <div>
      <h3>BDT Rental</h3>
      <div class="subtitle">{{ auth()->user()?->role === 'super_admin' ? 'Super Admin Panel' : 'User Panel' }}</div>
    </div>
  </div>

  @if(auth()->user()?->role === 'super_admin')
  <div class="dashboard-section-label">Main Menu</div>
  <ul class="dashboard-menu">
    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li><a href="{{ route('dashboard.calendar') }}" class="{{ request()->routeIs('dashboard.calendar') ? 'active' : '' }}">Kalender</a></li>
    <li><a href="{{ route('booking.create') }}" class="{{ request()->routeIs('booking.create') ? 'active' : '' }}">Booking Form</a></li>
    <li><a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">Manage Bookings</a></li>
    <li><a href="{{ route('booking-data.index') }}" class="{{ request()->routeIs('booking-data.*') ? 'active' : '' }}">Booking Data</a></li>
    <li><a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">Manage Vehicles</a></li>
    <li><a href="{{ route('mitras.index') }}" class="{{ request()->routeIs('mitras.*') ? 'active' : '' }}">Manage Mitras</a></li>
    <li><a href="{{ route('services.index') }}" class="{{ request()->routeIs('services.*') ? 'active' : '' }}">Manage Services</a></li>
    <li><a href="{{ route('groups.index') }}" class="{{ request()->routeIs('groups.*') ? 'active' : '' }}">Manage Groups</a></li>
    <li><a href="{{ route('accounts.index') }}" class="{{ request()->routeIs('accounts.*') ? 'active' : '' }}">Manage Accounts</a></li>
    <li><a href="{{ route('itineraries.index') }}" class="{{ request()->routeIs('itineraries.*') ? 'active' : '' }}">Manage Itineraries</a></li>
  </ul>

  <div class="dashboard-section-label">Settings</div>
    <ul class="side-menu">
      <li><a href="{{ route('settings.invoice.edit') }}" class="{{ request()->routeIs('settings.invoice*') ? 'active' : '' }}">Invoice Settings</a></li>
      <li><a href="{{ route('settings.terms.edit') }}" class="{{ request()->routeIs('settings.terms*') ? 'active' : '' }}">Terms & Conditions</a></li>
      <li><a href="{{ route('settings.notification-sound') }}" class="{{ request()->routeIs('settings.notification-sound*') ? 'active' : '' }}">Sound Settings</a></li>
      <li><a href="{{ route('settings.smtp') }}" class="{{ request()->routeIs('settings.smtp*') ? 'active' : '' }}">SMTP Settings</a></li>
    <li><a href="{{ route('email-logs.index') }}" class="{{ request()->routeIs('email-logs.*') ? 'active' : '' }}">Log Email</a></li>
  </ul>

  <div class="dashboard-section-label">Quick Actions</div>
  <ul class="dashboard-menu">
    <li>
      <form method="POST" action="{{ route('dashboard.test-email') }}">
        @csrf
        <button type="submit">Send Test Email</button>
      </form>
    </li>
    <li>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="width:100%; text-align:left;">Logout</button>
      </form>
    </li>
  </ul>
  @else
  <div class="dashboard-section-label">Menu</div>
  <ul class="dashboard-menu">
    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li><a href="{{ route('booking.create') }}" class="{{ request()->routeIs('booking.create') ? 'active' : '' }}">Booking Form</a></li>
    <li><a href="{{ route('user.bookings.history') }}" class="{{ request()->routeIs('user.bookings.*') ? 'active' : '' }}">My Bookings</a></li>
    <li><a href="{{ route('itineraries.index') }}" class="{{ request()->routeIs('itineraries.*') ? 'active' : '' }}">My Itineraries</a></li>
    <li><a href="{{ route('accounts.index') }}" class="{{ request()->routeIs('accounts.*') ? 'active' : '' }}">Manage Accounts</a></li>
  </ul>

  <div class="dashboard-section-label">Account</div>
  <ul class="dashboard-menu">
    <li>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="width:100%; text-align:left;">Logout</button>
      </form>
    </li>
  </ul>
  @endif
</aside>
