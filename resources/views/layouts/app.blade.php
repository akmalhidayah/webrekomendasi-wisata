<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#082f49">
    <title>@yield('title', 'Wisata Makassar')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/logo-dinas-pariwisata.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logos/logo-dinas-pariwisata.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logos/logo-dinas-pariwisata.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --navy:#082f49; --ocean:#0369a1; --teal:#0f766e; --sun:#f59e0b; --paper:#f5f8fb; --ink:#132238; }
        html { scroll-behavior:smooth; }
        body { background:var(--paper); color:var(--ink); font-family:'Manrope',sans-serif; overflow-x:hidden; }
        .site-header { position:sticky; top:0; z-index:1030; padding:14px 16px 0; }
        .site-nav { border:1px solid rgba(255,255,255,.12); border-radius:22px; background:rgba(7,31,50,.92)!important; box-shadow:0 18px 45px rgba(8,47,73,.18); backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px); }
        .navbar-brand-mark { width:39px; height:39px; display:grid; place-items:center; border-radius:13px; color:#fff; background:linear-gradient(135deg,#06b6d4,#0f766e); box-shadow:0 8px 18px rgba(6,182,212,.24); }
        .public-logos { display:flex; align-items:center; gap:8px; }.public-logos img { width:auto; height:44px; padding:0; object-fit:contain; border:0; border-radius:0; background:transparent; }
        .site-nav .public-logos img { filter:drop-shadow(0 0 1px #fff) drop-shadow(0 0 1px rgba(255,255,255,.85)); }
        .site-nav .nav-link { color:rgba(255,255,255,.72); font-weight:600; border-radius:12px; padding:.65rem .85rem!important; transition:.2s ease; }
        .site-nav .nav-link:hover,.site-nav .nav-link.active { color:#fff; background:rgba(255,255,255,.1); transform:translateY(-1px); }
        .nav-recommendation { background:linear-gradient(135deg,#fbbf24,#f59e0b)!important; color:#3f2a00!important; }
        .destination-img { height:225px; object-fit:cover; width:100%; }
        .modern-card { border:0; border-radius:24px; overflow:hidden; box-shadow:0 14px 40px rgba(15,23,42,.08); transition:transform .28s ease,box-shadow .28s ease; }
        .modern-card:hover { transform:translateY(-8px); box-shadow:0 24px 55px rgba(15,23,42,.15); }
        .image-zoom { transition:transform .55s ease; }
        .modern-card:hover .image-zoom { transform:scale(1.06); }
        .btn-modern { border:0; border-radius:14px; padding:.8rem 1.15rem; font-weight:700; }
        .floating-recommendation { position:fixed; right:24px; bottom:24px; z-index:1025; width:62px; height:62px; border:0; border-radius:22px; color:#fff; background:linear-gradient(135deg,#f59e0b,#f97316); box-shadow:0 16px 35px rgba(249,115,22,.38); animation:floatCta 3s ease-in-out infinite; }
        .floating-recommendation::before { content:''; position:absolute; inset:-7px; border:2px solid rgba(245,158,11,.35); border-radius:27px; animation:pulseRing 2s infinite; }
        .floating-label { position:absolute; right:72px; top:50%; transform:translateY(-50%); white-space:nowrap; color:#fff; background:#0f172a; padding:.55rem .8rem; border-radius:10px; font-size:.8rem; font-weight:700; opacity:0; pointer-events:none; transition:.2s; }
        .floating-recommendation:hover .floating-label { opacity:1; right:76px; }
        .reveal { opacity:0; transform:translateY(24px); transition:opacity .65s ease,transform .65s ease; }
        .reveal.visible { opacity:1; transform:none; }
        .recommendation-modal .modal-content { border:0; border-radius:28px; overflow:hidden; box-shadow:0 35px 80px rgba(8,47,73,.28); }
        .modal-visual { min-height:240px; background:linear-gradient(135deg,rgba(3,105,161,.88),rgba(15,118,110,.78)),url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=82') center/cover; }
        @keyframes floatCta { 0%,100%{transform:translateY(0) rotate(0)} 50%{transform:translateY(-8px) rotate(3deg)} }
        @keyframes pulseRing { 0%{transform:scale(.9);opacity:1} 75%,100%{transform:scale(1.2);opacity:0} }
        @media (max-width:991.98px) { .site-header{padding:8px 8px 0}.site-nav{border-radius:18px}.navbar-collapse{padding:.7rem 0}.floating-recommendation{right:16px;bottom:16px}.floating-label{display:none} }
        @media (prefers-reduced-motion:reduce) { *,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;scroll-behavior:auto!important;transition-duration:.01ms!important}.reveal{opacity:1;transform:none} }
    </style>
    @stack('styles')
</head>
<body>
<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark site-nav">
        <div class="container px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('wisatawan.home') }}" aria-label="Beranda"><span class="public-logos"><img src="{{ asset('images/logos/logo-sulsel.png') }}" alt="Logo Sulawesi Selatan"><img src="{{ asset('images/logos/logo-dinas-pariwisata.png') }}" alt="Logo Dinas Pariwisata"></span></a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="mainNav"><div class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <a class="nav-link {{ request()->routeIs('wisatawan.home') ? 'active' : '' }}" href="{{ route('wisatawan.home') }}"><i class="bi bi-house-door me-1"></i> Beranda</a>
                <a class="nav-link {{ request()->routeIs('wisatawan.wisata.*') ? 'active' : '' }}" href="{{ route('wisatawan.wisata.index') }}"><i class="bi bi-map me-1"></i> Destinasi</a>
                <a class="nav-link nav-recommendation ms-lg-2" href="{{ route('wisatawan.rekomendasi.index') }}"><i class="bi bi-stars me-1"></i> Rekomendasi</a>
                <a class="nav-link ms-lg-1" href="{{ route('admin.login') }}"><i class="bi bi-person-lock me-1"></i> Admin</a>
            </div></div>
        </div>
    </nav>
</header>
<main>
    @if (session('success'))<div class="container mt-3"><div class="alert alert-success border-0 shadow-sm rounded-4"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div></div>@endif
    @if (session('error'))<div class="container mt-3"><div class="alert alert-danger border-0 shadow-sm rounded-4">{{ session('error') }}</div></div>@endif
    @if ($errors->any())<div class="container mt-3"><div class="alert alert-danger border-0 shadow-sm rounded-4"><strong>Periksa kembali isian:</strong><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
    @yield('content')
</main>
<button class="floating-recommendation" type="button" aria-label="Dapatkan rekomendasi" @if(request()->routeIs('wisatawan.home')) data-bs-toggle="modal" data-bs-target="#recommendationModal" @else onclick="window.location.href='{{ route('wisatawan.rekomendasi.index') }}'" @endif><span class="floating-label">Temukan wisata cocok untukmu</span><i class="bi bi-stars fs-4"></i></button>
<footer class="mt-5 py-4 text-white" style="background:#071f31"><div class="container"><div class="d-flex flex-wrap align-items-center justify-content-between gap-3"><div class="d-flex align-items-center gap-3"><span class="public-logos"><img src="{{ asset('images/logos/logo-sulsel.png') }}" alt="Logo Sulawesi Selatan"><img src="{{ asset('images/logos/logo-dinas-pariwisata.png') }}" alt="Logo Dinas Pariwisata"></span><strong>Jelajah Makassar</strong></div><span class="text-white-50 small">Makassar, Sulawesi Selatan</span></div></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>document.addEventListener('DOMContentLoaded',()=>{const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('visible');observer.unobserve(entry.target)}}),{threshold:.12});document.querySelectorAll('.reveal').forEach(el=>observer.observe(el));});</script>
@stack('scripts')
</body>
</html>
