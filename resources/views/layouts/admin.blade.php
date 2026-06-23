<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071f31">
    <title>@yield('title', 'Admin Wisata Makassar')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width:278px; --sidebar-mini:88px; --admin-navy:#071f31; --admin-blue:#0369a1; --admin-bg:#f4f7fb; --admin-ink:#142033; }
        * { scrollbar-width:thin; scrollbar-color:#b8c5d3 transparent; }
        body { margin:0; background:var(--admin-bg); color:var(--admin-ink); font-family:'Manrope',sans-serif; }
        .admin-sidebar { position:fixed; inset:0 auto 0 0; z-index:1040; width:var(--sidebar-width); display:flex; flex-direction:column; color:#fff; background:#082f49; box-shadow:none; transition:width .28s ease,transform .28s ease; overflow:hidden; }
        .sidebar-brand { height:86px; display:flex; align-items:center; gap:12px; padding:0 23px; color:#fff; text-decoration:none; border-bottom:1px solid rgba(255,255,255,.08); white-space:nowrap; }
        .admin-brand-logos { display:flex; align-items:center; gap:8px; flex:0 0 auto; }.admin-brand-logos img { width:auto; height:44px; padding:0; object-fit:contain; border:0; border-radius:0; background:transparent; }
        .brand-copy strong { display:block; font-size:.9rem; }
        .sidebar-scroll { flex:1; overflow-y:auto; overflow-x:hidden; padding:18px 14px; }
        .sidebar-section { padding:14px 12px 7px; color:rgba(255,255,255,.38); font-size:.65rem; font-weight:800; letter-spacing:.13em; text-transform:uppercase; white-space:nowrap; }
        .sidebar-link { position:relative; display:flex; align-items:center; gap:13px; min-height:48px; padding:10px 13px; margin:3px 0; border-radius:14px; color:rgba(255,255,255,.7); font-size:.86rem; font-weight:600; text-decoration:none; white-space:nowrap; transition:.2s ease; }
        .sidebar-link i { flex:0 0 auto; width:25px; text-align:center; font-size:1.05rem; }
        .sidebar-link:hover { color:#fff; background:rgba(255,255,255,.08); transform:translateX(3px); }
        .sidebar-link.active { color:#fff; border-left:3px solid #22d3ee; background:rgba(14,165,233,.22); }
        .sidebar-footer { padding:15px; border-top:1px solid rgba(255,255,255,.08); }
        .admin-profile { display:flex; align-items:center; gap:10px; padding:10px; border-radius:15px; background:rgba(255,255,255,.06); overflow:hidden; }
        .profile-avatar { flex:0 0 auto; width:38px; height:38px; display:grid; place-items:center; border-radius:13px; color:#083344; background:#a5f3fc; font-weight:800; }
        .profile-copy { min-width:0; flex:1; }.profile-copy strong,.profile-copy small { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }.profile-copy strong { font-size:.78rem; }.profile-copy small { color:rgba(255,255,255,.45); font-size:.66rem; }
        .logout-icon { border:0; color:#fca5a5; background:transparent; border-radius:9px; padding:.35rem .5rem; }.logout-icon:hover { color:#fff; background:rgba(239,68,68,.25); }
        .admin-main { min-height:100vh; margin-left:var(--sidebar-width); transition:margin-left .28s ease; }
        .admin-topbar { position:sticky; top:0; z-index:1020; height:86px; display:flex; align-items:center; padding:0 30px; border-bottom:1px solid #e5ebf2; background:rgba(255,255,255,.9); backdrop-filter:blur(18px); }
        .sidebar-toggle { width:42px; height:42px; display:grid; place-items:center; border:1px solid #e2e8f0; border-radius:13px; color:#334155; background:#fff; transition:.2s; }.sidebar-toggle:hover { color:#0369a1; border-color:#bae6fd; background:#f0f9ff; }
        .topbar-title { margin:0; font-size:1rem; font-weight:800; }.topbar-subtitle { color:#94a3b8; font-size:.72rem; }
        .topbar-action { width:40px; height:40px; display:grid; place-items:center; border:1px solid #e2e8f0; border-radius:13px; color:#475569; text-decoration:none; background:#fff; }
        .notification-action { position:relative; }
        .notification-badge { position:absolute; top:-6px; right:-6px; min-width:19px; height:19px; display:grid; place-items:center; padding:0 5px; border-radius:999px; color:#fff; background:#ef4444; font-size:.62rem; font-weight:800; }
        .notification-menu { width:min(360px,calc(100vw - 32px)); padding:.55rem; border:1px solid #e2e8f0; border-radius:14px; box-shadow:none; }
        .notification-item { display:block; padding:.75rem; border-radius:11px; color:#142033; text-decoration:none; }
        .notification-item:hover { background:#f0f9ff; }
        .notification-item strong { display:block; font-size:.82rem; }
        .notification-item small { display:block; color:#64748b; font-size:.68rem; margin-top:.2rem; }
        .admin-content { padding:30px; }
        .admin-content > .alert { border:1px solid currentColor; border-radius:12px; box-shadow:none; }
        .admin-content .card { border:1px solid #e2e8f0!important; border-radius:14px!important; box-shadow:none!important; }
        .admin-content .card-header { padding:1rem 1.2rem; border-bottom:1px solid #edf2f7; background:#fff; border-radius:14px 14px 0 0!important; }
        .admin-content .table { --bs-table-bg:transparent; margin-bottom:0; }.admin-content .table thead th { padding:1rem; border-bottom:1px solid #e5eaf0; color:#64748b; background:#f8fafc; font-size:.71rem; font-weight:800; letter-spacing:.04em; text-transform:uppercase; white-space:nowrap; }.admin-content .table tbody td { padding:.9rem 1rem; border-color:#edf2f7; vertical-align:middle; font-size:.84rem; }.admin-content .table-hover tbody tr { transition:.18s; }.admin-content .table-hover tbody tr:hover { --bs-table-hover-bg:#f6fbff; }
        .admin-content .form-control,.admin-content .form-select { min-height:46px; border-color:#dfe7ef; border-radius:12px; box-shadow:none; }.admin-content textarea.form-control { min-height:auto; }.admin-content .form-control:focus,.admin-content .form-select:focus { border-color:#0284c7; box-shadow:none; }
        .admin-content .btn { border-radius:11px; font-size:.82rem; font-weight:700; box-shadow:none!important; }.admin-content .btn-primary { border-color:#0369a1; background:#0369a1; }.admin-content .btn-primary:hover { border-color:#075985; background:#075985; }
        .admin-content h1.h3 { font-weight:800; letter-spacing:-.03em; }.thumb { width:86px; height:62px; object-fit:cover; border-radius:12px!important; }
        .page-item .page-link { margin:0 2px; border:0; border-radius:9px!important; color:#475569; }.page-item.active .page-link { color:#fff; background:#0369a1; }
        .admin-reveal { opacity:0; transform:translateY(16px); animation:adminReveal .55s ease forwards; }
        .sidebar-overlay { display:none; position:fixed; inset:0; z-index:1035; background:rgba(2,12,20,.55); backdrop-filter:blur(3px); }
        body.sidebar-collapsed .admin-sidebar { width:var(--sidebar-mini); } body.sidebar-collapsed .admin-main { margin-left:var(--sidebar-mini); } body.sidebar-collapsed .brand-copy,body.sidebar-collapsed .sidebar-label,body.sidebar-collapsed .sidebar-section,body.sidebar-collapsed .profile-copy,body.sidebar-collapsed .logout-icon,body.sidebar-collapsed .admin-brand-logos img:last-child { opacity:0; pointer-events:none; } body.sidebar-collapsed .sidebar-link { justify-content:center; } body.sidebar-collapsed .sidebar-link:hover { transform:none; } body.sidebar-collapsed .sidebar-brand { padding:0 25px; } body.sidebar-collapsed .admin-brand-logos{gap:0}
        .guest-admin-page { min-height:100vh; background:#eef3f8; }
        .guest-admin-page .shadow-sm,.admin-authenticated .shadow-sm { box-shadow:none!important; }
        @keyframes adminReveal { to { opacity:1; transform:none; } }
        @media(max-width:991.98px){.admin-sidebar{width:min(86vw,290px);transform:translateX(-105%)}.admin-main,body.sidebar-collapsed .admin-main{margin-left:0}.admin-topbar{height:72px;padding:0 16px}.admin-content{padding:20px 14px}.sidebar-overlay{display:block;opacity:0;pointer-events:none;transition:.25s}body.sidebar-open .admin-sidebar{transform:none}body.sidebar-open .sidebar-overlay{opacity:1;pointer-events:auto}body.sidebar-collapsed .admin-sidebar{width:min(86vw,290px)}body.sidebar-collapsed .brand-copy,body.sidebar-collapsed .sidebar-label,body.sidebar-collapsed .sidebar-section,body.sidebar-collapsed .profile-copy,body.sidebar-collapsed .logout-icon,body.sidebar-collapsed .admin-brand-logos img:last-child{opacity:1;pointer-events:auto}body.sidebar-collapsed .admin-brand-logos{gap:6px}body.sidebar-collapsed .sidebar-link{justify-content:flex-start}}
        @media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important}.admin-reveal{opacity:1;transform:none}}
    </style>
    @stack('styles')
</head>
<body class="{{ auth()->check() ? 'admin-authenticated' : 'guest-admin-page' }}">
@auth
<aside class="admin-sidebar" id="adminSidebar">
    <a class="sidebar-brand" href="{{ route('admin.dashboard') }}"><span class="admin-brand-logos"><img src="{{ asset('images/logos/logo-sulsel.png') }}" alt="Logo Sulawesi Selatan"><img src="{{ asset('images/logos/logo-dinas-pariwisata.png') }}" alt="Logo Dinas Pariwisata"></span><span class="brand-copy"><strong>Admin Wisata</strong></span></a>
    <div class="sidebar-scroll">
        <div class="sidebar-section">Ringkasan</div>
        <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2-fill"></i><span class="sidebar-label">Dashboard</span></a>
        <div class="sidebar-section">Master Data</div>
        <a class="sidebar-link {{ request()->routeIs('admin.kategori-wisata.*') ? 'active' : '' }}" href="{{ route('admin.kategori-wisata.index') }}"><i class="bi bi-tags-fill"></i><span class="sidebar-label">Kategori Wisata</span></a>
        <a class="sidebar-link {{ request()->routeIs('admin.wisata.*') ? 'active' : '' }}" href="{{ route('admin.wisata.index') }}"><i class="bi bi-map-fill"></i><span class="sidebar-label">Data Wisata</span></a>
        <div class="sidebar-section">Aktivitas</div>
        <a class="sidebar-link {{ request()->routeIs('admin.survey-preferensi.*') ? 'active' : '' }}" href="{{ route('admin.survey-preferensi.index') }}"><i class="bi bi-ui-checks-grid"></i><span class="sidebar-label">Data Survei</span></a>
        <a class="sidebar-link {{ request()->routeIs('admin.hasil-rekomendasi.*') ? 'active' : '' }}" href="{{ route('admin.hasil-rekomendasi.index') }}"><i class="bi bi-stars"></i><span class="sidebar-label">Hasil Rekomendasi</span></a>
        <a class="sidebar-link {{ request()->routeIs('admin.rating-kunjungan.*') ? 'active' : '' }}" href="{{ route('admin.rating-kunjungan.index') }}"><i class="bi bi-chat-square-heart-fill"></i><span class="sidebar-label">Rating Kunjungan</span></a>
        <div class="sidebar-section">Website</div>
        <a class="sidebar-link" href="{{ route('wisatawan.home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i><span class="sidebar-label">Lihat Situs Publik</span></a>
    </div>
    <div class="sidebar-footer"><div class="admin-profile"><span class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></span><form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="logout-icon" title="Logout" aria-label="Logout"><i class="bi bi-box-arrow-right"></i></button></form></div></div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
@php
    $ratingNotifications = \App\Models\RatingKunjungan::with('wisata')
        ->where('status', 'disetujui')
        ->latest()
        ->limit(5)
        ->get();
    $newRatingNotificationCount = \App\Models\RatingKunjungan::where('status', 'disetujui')
        ->where('created_at', '>=', now()->subDay())
        ->count();
@endphp
<div class="admin-main">
    <header class="admin-topbar"><button class="sidebar-toggle me-3" id="sidebarToggle" type="button" aria-label="Buka atau tutup sidebar"><i class="bi bi-list fs-5"></i></button><h1 class="topbar-title">@yield('page-title', 'Admin')</h1><div class="ms-auto d-flex align-items-center gap-2"><div class="dropdown"><button class="topbar-action notification-action" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Rating terbaru"><i class="bi bi-bell"></i>@if($newRatingNotificationCount > 0)<span class="notification-badge">{{ $newRatingNotificationCount > 9 ? '9+' : $newRatingNotificationCount }}</span>@endif</button><div class="dropdown-menu dropdown-menu-end notification-menu"><div class="px-2 py-1 small fw-bold text-muted">Rating terbaru</div>@forelse($ratingNotifications as $notification)<a class="notification-item" href="{{ route('admin.rating-kunjungan.show', $notification) }}"><strong>{{ $notification->wisata?->nama_wisata ?? 'Wisata dihapus' }}</strong><small><i class="bi bi-star-fill text-warning"></i> {{ $notification->rating }}/5 · {{ $notification->created_at->diffForHumans() }}</small>@if($notification->ulasan)<small>{{ Str::limit($notification->ulasan, 58) }}</small>@endif</a>@empty<div class="px-2 py-3 small text-muted">Belum ada rating.</div>@endforelse<div class="border-top mt-1 pt-1"><a class="notification-item text-primary fw-bold" href="{{ route('admin.rating-kunjungan.index') }}">Lihat semua rating</a></div></div></div><a class="topbar-action" href="{{ route('wisatawan.home') }}" target="_blank" title="Lihat website"><i class="bi bi-globe2"></i></a></div></header>
    <main class="admin-content"><div class="admin-reveal">
        @if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger"><strong>Periksa kembali data:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('content')
    </div></main>
</div>
@else
    @yield('content')
@endauth
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@auth
<script>
document.addEventListener('DOMContentLoaded',()=>{const body=document.body,toggle=document.getElementById('sidebarToggle'),overlay=document.getElementById('sidebarOverlay');if(window.innerWidth>=992&&localStorage.getItem('adminSidebarCollapsed')==='1')body.classList.add('sidebar-collapsed');const closeMobile=()=>body.classList.remove('sidebar-open');toggle.addEventListener('click',()=>{if(window.innerWidth<992){body.classList.toggle('sidebar-open')}else{body.classList.toggle('sidebar-collapsed');localStorage.setItem('adminSidebarCollapsed',body.classList.contains('sidebar-collapsed')?'1':'0')}});overlay.addEventListener('click',closeMobile);window.addEventListener('resize',()=>{if(window.innerWidth>=992)closeMobile()});});
</script>
@endauth
@stack('scripts')
</body>
</html>
