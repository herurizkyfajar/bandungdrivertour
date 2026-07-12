@extends('layouts.app', ['title' => 'Add Account'])

@section('content')
<style>
  .dashboard-wrap { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 1.25rem; align-items: start; }
  .content-card { max-width: none; margin: 0; background: #ffffff; border: 1px solid var(--border); border-radius: 16px; padding: 1rem; }
  @media (max-width: 1024px) { .dashboard-wrap { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <div class="content-card">
    @if($errors->any())
      <div style="margin-bottom:1rem; padding:.75rem 1rem; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; color:#991b1b;">
        <ul style="margin:0; padding-left:1.25rem;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('accounts.store') }}">
      @csrf
      <div style="margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between;">
        <div>
          <h1>Add Account</h1>
          <div class="subtitle">Create a new user account.</div>
        </div>
        <div><a class="btn" href="{{ route('accounts.index') }}">Back</a></div>
      </div>
      <div class="form-grid">
        <div class="col-6">
          <div class="field">
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
          </div>
        </div>
        <div class="col-6">
          <div class="field">
            <label for="role">Role</label>
            <select id="role" name="role" required>
              <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
              @foreach(['super_admin', 'mitra', 'user'] as $role)
                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ $role }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="actions">
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
@endsection
