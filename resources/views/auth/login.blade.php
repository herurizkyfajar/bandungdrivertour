@extends('layouts.app', ['title' => 'Login'])

@section('content')
<style>
  .login-shell {
    min-height: calc(100vh - 7rem);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem 0 2rem;
  }
  .login-card {
    width: min(100%, 420px);
  }
  .login-card .card-header,
  .login-card .card-body {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
  .login-card .card-header { padding-top: 1.5rem; }
  .login-card .card-body { padding-bottom: 1.5rem; }
  .login-actions {
    margin-top: 1rem;
    justify-content: stretch;
  }
  .login-actions .btn {
    width: 100%;
  }
  .login-footer {
    margin-top: .75rem;
    text-align: center;
    color: var(--muted);
    font-size: .92rem;
  }
  .register-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }
  .register-backdrop.open { display: flex; }
  .register-modal {
    background: #fff;
    border-radius: 16px;
    width: min(100%, 440px);
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
  }
  .register-modal__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem 0;
  }
  .register-modal__head h2 { margin: 0; font-size: 1.2rem; }
  .register-modal__close {
    width: 32px; height: 32px;
    border: none; background: #f1f5f9;
    border-radius: 8px; font-size: 1.1rem;
    cursor: pointer; display: flex;
    align-items: center; justify-content: center;
    color: #64748b;
  }
  .register-modal__close:hover { background: #e2e8f0; }
  .register-modal__body { padding: 1rem 1.5rem 1.5rem; }
  .pw-wrap { position: relative; }
  .pw-wrap input { width: 100%; padding-right: 2.5rem; }
  .pw-toggle {
    position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
    width: 30px; height: 30px; border: none; background: none;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: #64748b; font-size: 1.1rem; border-radius: 6px;
  }
  .pw-toggle:hover { background: #f1f5f9; }
</style>

<div class="login-shell">
  <div class="card login-card">
    <div class="card-header" style="text-align:center;">
      <h1 style="margin-bottom:.35rem;">Login</h1>
      <div class="subtitle">Masuk untuk mengelola booking, kendaraan, dan driver.</div>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('login.perform') }}">
        @csrf
        <div class="form-grid">
          <div class="col-12">
            <div class="field">
              <label>Email</label>
              <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@domain.com" required>
            </div>
            @error('email')<div style="color:red">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <div class="field">
              <label>Password</label>
              <div class="pw-wrap">
                <input id="login_password" type="password" name="password" placeholder="Kata sandi" required>
                <button type="button" class="pw-toggle" onclick="togglePw('login_password', this)" title="Show/Hide">&#128065;</button>
              </div>
            </div>
            @error('password')<div style="color:red">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label><input type="checkbox" name="remember"> Ingat saya</label>
          </div>
        </div>
        <div class="actions login-actions">
          <button type="submit" class="btn btn-primary">Masuk</button>
        </div>
      </form>
      <div class="login-footer">
        Akses aman untuk admin panel BDT Rental.
        <div style="margin-top:.75rem;">
          <button type="button" class="btn" id="openRegisterBtn" style="width:auto; display:inline-flex;">Buat Akun</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="register-backdrop" id="registerBackdrop">
  <div class="register-modal">
    <div class="register-modal__head">
      <h2>Buat Akun Baru</h2>
      <button type="button" class="register-modal__close" id="closeRegisterBtn">&times;</button>
    </div>
    <div class="register-modal__body">
      @if($errors->any() && request()->is('register*'))
        <div style="margin-bottom:1rem; padding:.75rem 1rem; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; color:#991b1b;">
          @foreach($errors->all() as $err)
            <div>{{ $err }}</div>
          @endforeach
        </div>
      @endif
      <form method="POST" action="{{ route('register.perform') }}">
        @csrf
        <div class="form-grid">
          <div class="col-12">
            <div class="field">
              <label for="reg_name">Name</label>
              <input id="reg_name" type="text" name="name" value="{{ old('name') }}" placeholder="Full name" required>
            </div>
            @error('name')<div style="color:red">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <div class="field">
              <label for="reg_email">Email</label>
              <input id="reg_email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@domain.com" required>
            </div>
            @error('email')<div style="color:red">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <div class="field">
              <label for="reg_password">Password</label>
              <div class="pw-wrap">
                <input id="reg_password" type="password" name="password" placeholder="Min 6 characters" required>
                <button type="button" class="pw-toggle" onclick="togglePw('reg_password', this)" title="Show/Hide">&#128065;</button>
              </div>
            </div>
            @error('password')<div style="color:red">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <div class="field">
              <label for="reg_password_confirmation">Confirm Password</label>
              <div class="pw-wrap">
                <input id="reg_password_confirmation" type="password" name="password_confirmation" placeholder="Repeat password" required>
                <button type="button" class="pw-toggle" onclick="togglePw('reg_password_confirmation', this)" title="Show/Hide">&#128065;</button>
              </div>
            </div>
          </div>
        </div>
        <div class="actions" style="margin-top:1rem;">
          <button type="submit" class="btn btn-primary" style="width:100%;">Buat Akun</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function togglePw(id, btn) {
  var inp = document.getElementById(id);
  if (inp.type === 'password') {
    inp.type = 'text';
    btn.innerHTML = '&#128064;';
  } else {
    inp.type = 'password';
    btn.innerHTML = '&#128065;';
  }
}
(function() {
  var backdrop = document.getElementById('registerBackdrop');
  var openBtn = document.getElementById('openRegisterBtn');
  var closeBtn = document.getElementById('closeRegisterBtn');

  openBtn.addEventListener('click', function() { backdrop.classList.add('open'); });
  closeBtn.addEventListener('click', function() { backdrop.classList.remove('open'); });
  backdrop.addEventListener('click', function(e) { if (e.target === backdrop) backdrop.classList.remove('open'); });
})();
</script>
@endsection
