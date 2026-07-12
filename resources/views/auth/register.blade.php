@extends('layouts.app', ['title' => 'Register'])

@section('content')
<h1>Registrasi</h1>
<form method="POST" action="{{ route('register.perform') }}">
    @csrf
    <label>Nama
        <input type="text" name="name" value="{{ old('name') }}" required>
    </label>
    @error('name')<div style="color:red">{{ $message }}</div>@enderror
    <label>Email
        <input type="email" name="email" value="{{ old('email') }}" required>
    </label>
    @error('email')<div style="color:red">{{ $message }}</div>@enderror
    <label>Password
        <input type="password" name="password" required>
    </label>
    <label>Konfirmasi Password
        <input type="password" name="password_confirmation" required>
    </label>
    <label>Peran
        <select name="role">
            <option value="client">Klien</option>
            <option value="user">User</option>
            <option value="management">Management</option>
            <option value="mitra_driver">Mitra Driver</option>
            <option value="super_admin">Super Admin</option>
        </select>
    </label>
    <button type="submit">Daftar</button>
</form>
@endsection
