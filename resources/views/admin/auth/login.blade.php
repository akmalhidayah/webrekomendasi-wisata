@extends('layouts.admin')
@section('title', 'Login Admin')
@push('styles')
<style>
    .login-page { min-height:100vh; display:grid; place-items:center; padding:24px; background:#f4f7fb; }
    .login-card { width:min(430px,100%); padding:36px; border:1px solid #dfe7ef; border-radius:18px; background:#fff; }
    .login-logos { display:flex; justify-content:center; align-items:center; gap:18px; margin-bottom:22px; }
    .login-logos img { width:auto; height:68px; padding:0; object-fit:contain; border:0; border-radius:0; background:transparent; }
    .login-title { font-size:1.7rem; letter-spacing:-.03em; font-weight:800; }
    .login-input { position:relative; }.login-input > i { position:absolute; left:15px; top:50%; z-index:2; transform:translateY(-50%); color:#94a3b8; }
    .login-input .form-control { height:50px; padding-left:44px; border-color:#dce5ee; border-radius:11px; box-shadow:none!important; }.login-input .form-control:focus { border-color:#0284c7; }
    .password-toggle { position:absolute; right:7px; top:50%; z-index:2; transform:translateY(-50%); border:0; color:#64748b; background:transparent; padding:.5rem; }
    .login-button { height:50px; border:0; border-radius:11px; font-weight:700; background:#075985; box-shadow:none!important; }.login-button:hover { background:#0c4a6e; }
    .back-home { color:#64748b; font-size:.78rem; font-weight:600; text-decoration:none; }
    @media(max-width:480px){.login-page{padding:14px}.login-card{padding:28px 22px}}
</style>
@endpush
@section('content')
@php
    $loginLogoSulselPath = 'images/logos/logo-sulsel.png';
    $loginLogoDinasPath = 'images/logos/logo-dinas-pariwisata.png';
    $loginLogoSulselUrl = asset($loginLogoSulselPath).(file_exists(public_path($loginLogoSulselPath)) ? '?v='.filemtime(public_path($loginLogoSulselPath)) : '');
    $loginLogoDinasUrl = asset($loginLogoDinasPath).(file_exists(public_path($loginLogoDinasPath)) ? '?v='.filemtime(public_path($loginLogoDinasPath)) : '');
@endphp
<main class="login-page"><section class="login-card">
    <div class="login-logos"><img src="{{ $loginLogoSulselUrl }}" alt="Logo Sulawesi Selatan"><img src="{{ $loginLogoDinasUrl }}" alt="Logo Dinas Pariwisata"></div>
    <div class="text-center mb-4"><h1 class="login-title mb-1">Login Admin</h1><p class="text-muted small mb-0">Sistem Wisata Makassar</p></div>
    @if($errors->any())<div class="alert alert-danger small">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('admin.login.process') }}">@csrf
        <div class="mb-3"><label class="form-label small fw-bold">Email</label><div class="login-input"><i class="bi bi-envelope"></i><input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="admin@email.com" required autofocus autocomplete="email"></div></div>
        <div class="mb-3"><label class="form-label small fw-bold">Password</label><div class="login-input"><i class="bi bi-lock"></i><input type="password" name="password" id="adminPassword" class="form-control pe-5" placeholder="Password" required autocomplete="current-password"><button class="password-toggle" id="passwordToggle" type="button" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button></div></div>
        <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"><label class="form-check-label small" for="remember">Ingat saya</label></div>
        <button class="btn btn-primary login-button w-100">Masuk</button>
    </form>
    <div class="text-center mt-4"><a class="back-home" href="{{ route('wisatawan.home') }}"><i class="bi bi-arrow-left me-1"></i>Kembali ke website</a></div>
</section></main>
@endsection
@push('scripts')
<script>document.addEventListener('DOMContentLoaded',()=>{const input=document.getElementById('adminPassword'),button=document.getElementById('passwordToggle');button.addEventListener('click',()=>{const visible=input.type==='text';input.type=visible?'password':'text';button.innerHTML=`<i class="bi bi-eye${visible?'':'-slash'}"></i>`})});</script>
@endpush
