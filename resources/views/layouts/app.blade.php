<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MangaDiyari') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @php($theme = session('theme', auth()->user()->theme_preference ?? 'light'))
    <style>
        :root {
            --hero-overlay: linear-gradient(135deg, rgba(13, 110, 253, 0.75), rgba(32, 201, 151, 0.65));
        }

        body {
            font-family: 'Figtree', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        body[data-bs-theme="light"] {
            background: #f8f9fb;
        }

        body[data-bs-theme="dark"] {
            background: radial-gradient(circle at top, #1f2933, #0b0f19 65%);
            color: #e9ecef;
        }

        body[data-bs-theme="midnight"] {
            background: radial-gradient(circle at 15% 20%, rgba(91, 33, 182, 0.35), transparent 55%),
                radial-gradient(circle at 85% 10%, rgba(32, 201, 151, 0.35), transparent 45%),
                #020617;
            color: #f8fafc;
        }

        body[data-bs-theme="midnight"] .card,
        body[data-bs-theme="dark"] .card {
            background-color: rgba(15, 23, 42, 0.8);
            color: inherit;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .hero-slider {
            border-radius: 1.5rem;
            overflow: hidden;
        }

        .hero-slider img {
            aspect-ratio: 16 / 9;
            object-fit: cover;
        }

        .hero-slider .carousel-caption {
            background: var(--hero-overlay);
            border-radius: 1rem;
            padding: 1.5rem;
        }

        .theme-select {
            min-width: 140px;
        }
    </style>
</head>
<body data-bs-theme="{{ $theme }}" class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name', 'MangaDiyari') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="d-flex ms-lg-auto my-3 my-lg-0" role="search" action="{{ route('search') }}" method="GET">
                <input class="form-control me-2" type="search" name="q" placeholder="Manga ara"
                    value="{{ request('q') }}" aria-label="Search">
                <button class="btn btn-outline-light" type="submit">Ara</button>
            </form>
            <ul class="navbar-nav ms-lg-3 ms-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
            <form action="{{ route('theme.toggle') }}" method="POST" class="ms-lg-3 ms-0 mt-3 mt-lg-0">
                @csrf
                <select name="theme" class="form-select form-select-sm theme-select" onchange="this.form.submit()">
                    <option value="light" @selected($theme === 'light')>Aydınlık</option>
                    <option value="dark" @selected($theme === 'dark')>Gece</option>
                    <option value="midnight" @selected($theme === 'midnight')>Midnight Neon</option>
                </select>
            </form>
        </div>
    </div>
</nav>
<main class="flex-grow-1 py-5">
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>İşlem sırasında hatalar oluştu:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>
<footer class="bg-dark text-white py-3 mt-auto">
    <div class="container text-center">
        &copy; {{ date('Y') }} {{ config('app.name', 'MangaDiyari') }}. All rights reserved.
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
