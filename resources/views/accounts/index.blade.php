@extends('layouts.app', ['title' => 'Manage Accounts'])

@section('content')
<style>
  html, body { overflow-x: hidden; }
  body.admin-shell { overflow-x: hidden; }
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr) 320px; gap: 1.25rem; }
  .content-card { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; min-width: 0; overflow: hidden; }
  .calendar-side { background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; position: sticky; top: 76px; height: fit-content; }
  .account-profile { display: flex; align-items: center; gap: 1rem; padding: 1.5rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 16px; margin-bottom: 1.5rem; }
  .account-avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: 800; flex-shrink: 0; }
  .account-info h2 { margin: 0 0 .25rem; font-size: 1.3rem; }
  .account-info .subtitle { margin: 0; }
  .account-detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
  .account-detail-item { padding: 1rem; background: #fff; border: 1px solid var(--border); border-radius: 12px; }
  .account-detail-item .label { font-size: .85rem; color: var(--muted); margin-bottom: .25rem; }
  .account-detail-item .value { font-weight: 700; color: #0f172a; word-break: break-word; }
  .badge { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
  .badge-user { background: #f8fafc; color: #64748b; }
  .badge-super_admin { background: #fef2f2; color: #dc2626; }
  @media (max-width: 1200px) {
    .dashboard-wrap { grid-template-columns: 1fr; }
    .dashboard-sidebar { position: static; max-height: none; overflow: hidden; min-width: 0; }
    .calendar-side { display: none !important; }
  }
  @media (max-width: 768px) {
    .card { box-shadow: none; }
    .content-card { overflow: hidden; }
    .account-detail-grid { grid-template-columns: 1fr; }
    .account-profile { flex-direction: column; text-align: center; }
    .desktop-table { display: none !important; }
    .mobile-cards { display: block !important; }
  }
  .mobile-cards { display: none; }
  .mobile-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 1rem; margin-bottom: .75rem; }
  .mobile-card-header { font-weight: 700; color: #0f172a; font-size: .95rem; margin-bottom: .25rem; }
  .mobile-card-meta { font-size: .85rem; color: var(--muted); margin-bottom: .5rem; }
  .mobile-card-actions { display: flex; gap: .35rem; flex-wrap: wrap; }
</style>
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  @if(auth()->user()->role === 'super_admin')
  <main class="content-card">
    <div class="actions" style="justify-content: space-between;">
      <h2>Manage Accounts</h2>
      <a class="btn btn-primary" href="{{ route('accounts.create') }}">Add Account</a>
    </div>
    @if(session('success'))
      <div style="margin-top:.75rem; padding:.75rem 1rem; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; color:#166534;">{{ session('success') }}</div>
    @endif

    {{-- Desktop table --}}
    <div class="table-wrap desktop-table" style="margin-top:.75rem;">
      <table class="table" style="width:100%; border-collapse:separate; border-spacing:0;">
        <thead>
          <tr>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Name</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Email</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Role</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Created</th>
            <th style="text-align:left; font-weight:600; color:var(--text); padding:.65rem .75rem; border-bottom:1px solid var(--border); background:#f8fafc;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($accounts as $account)
          <tr>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $account->name }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $account->email }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;"><span class="badge badge-{{ $account->role }}">{{ $account->role }}</span></td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">{{ $account->created_at->format('d M Y') }}</td>
            <td style="padding:.65rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); word-break:break-word;">
              <div style="display:flex; gap:.35rem; flex-wrap:wrap;">
                <a class="btn" href="{{ route('accounts.show', $account) }}">Detail</a>
                <a class="btn" href="{{ route('accounts.edit', $account) }}">Edit</a>
                @if($account->id !== auth()->id())
                <form method="POST" action="{{ route('accounts.destroy', $account) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn" type="submit" onclick="return confirm('Delete account {{ $account->name }}?')">Delete</button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5"><div class="subtitle">No accounts yet.</div></td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div style="margin-top:.75rem;">
        {{ $accounts->links() }}
      </div>
    </div>

    {{-- Mobile cards --}}
    <div class="mobile-cards" style="margin-top:.75rem;">
      @forelse($accounts as $account)
      <div class="mobile-card">
        <div class="mobile-card-header">{{ $account->name }}</div>
        <div class="mobile-card-meta">
          {{ $account->email }}<br>
          <span class="badge badge-{{ $account->role }}">{{ $account->role }}</span> &middot; {{ $account->created_at->format('d M Y') }}
        </div>
        <div class="mobile-card-actions">
          <a class="btn" href="{{ route('accounts.show', $account) }}">Detail</a>
          <a class="btn" href="{{ route('accounts.edit', $account) }}">Edit</a>
          @if($account->id !== auth()->id())
          <form method="POST" action="{{ route('accounts.destroy', $account) }}">
            @csrf
            @method('DELETE')
            <button class="btn" type="submit" onclick="return confirm('Delete account {{ $account->name }}?')">Delete</button>
          </form>
          @endif
        </div>
      </div>
      @empty
      <div class="subtitle" style="text-align:center; padding:2rem;">No accounts yet.</div>
      @endforelse
      @if($accounts->hasPages())
      <div style="margin-top:.75rem;">
        {{ $accounts->links() }}
      </div>
      @endif
    </div>
  </main>
  <aside class="calendar-side">
    <h2>Quick Actions</h2>
    <div class="form-grid">
      <div class="col-12">
        <a class="btn btn-primary" href="{{ route('accounts.create') }}">Add Account</a>
      </div>
    </div>
  </aside>

  @else
  <main class="content-card">
    <h2>My Account</h2>
    @if(session('success'))
      <div style="margin-top:.75rem; padding:.75rem 1rem; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; color:#166534;">{{ session('success') }}</div>
    @endif

    @php($account = auth()->user())
    <div class="account-profile">
      <div class="account-avatar">{{ strtoupper(substr($account->name, 0, 1)) }}</div>
      <div class="account-info">
        <h2>{{ $account->name }}</h2>
        <div class="subtitle">{{ $account->email }}</div>
      </div>
    </div>

    <div class="account-detail-grid">
      <div class="account-detail-item">
        <div class="label">Name</div>
        <div class="value">{{ $account->name }}</div>
      </div>
      <div class="account-detail-item">
        <div class="label">Email</div>
        <div class="value">{{ $account->email }}</div>
      </div>
      <div class="account-detail-item">
        <div class="label">Role</div>
        <div class="value"><span class="badge badge-{{ $account->role }}">{{ $account->role }}</span></div>
      </div>
      <div class="account-detail-item">
        <div class="label">Member Since</div>
        <div class="value">{{ $account->created_at->format('d M Y') }}</div>
      </div>
    </div>

    <div style="margin-top:1.5rem; display:flex; gap:.5rem;">
      <a class="btn btn-primary" href="{{ route('accounts.edit', $account) }}">Edit Account</a>
    </div>
  </main>
  <aside class="calendar-side">
    <h2>Quick Actions</h2>
    <div class="form-grid">
      <div class="col-12">
        <a class="btn" href="{{ route('accounts.edit', $account) }}">Edit Account</a>
      </div>
      <div class="col-12">
        <a class="btn" href="{{ route('booking.create') }}">Create Booking</a>
      </div>
    </div>
  </aside>
  @endif
</div>
@endsection
