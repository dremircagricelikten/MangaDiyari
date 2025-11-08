@extends('layouts.app')

@section('content')
<section class="py-5">
    <div class="row g-4 align-items-center">
        <div class="col-lg-7">
            @if ($featuredMangas->isNotEmpty())
                <div id="heroSlider" class="carousel slide hero-slider shadow" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach ($featuredMangas as $index => $manga)
                            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="{{ $index }}"
                                class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                aria-label="{{ $manga->title }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach ($featuredMangas as $manga)
                            @php($coverUrl = \App\Support\MediaUrlGenerator::fromPath($manga->cover_image_path))
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                @if ($coverUrl)
                                    <img src="{{ $coverUrl }}" class="d-block w-100 rounded-4" alt="{{ $manga->title }}">
                                @endif
                                <div class="carousel-caption text-start d-none d-md-block">
                                    <span class="badge text-bg-dark">{{ $manga->chapters_count }} bölüm</span>
                                    <h2 class="h3 fw-bold mt-2">
                                        <a href="{{ route('mangas.show', $manga) }}" class="link-light text-decoration-none">
                                            {{ $manga->title }}
                                        </a>
                                    </h2>
                                    <p class="small text-white-50 mb-0">{{ \Illuminate\Support\Str::limit($manga->summary, 120) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Önceki</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Sonraki</span>
                    </button>
                </div>
            @endif
        </div>
        <div class="col-lg-5 text-center text-lg-start">
            <h1 class="display-5 fw-bold mb-3">{{ config('app.name', 'MangaDiyari') }}'ye Hoş Geldin</h1>
            <p class="lead text-muted">En sevdiğin mangaları keşfet, oku ve toplulukla paylaş.</p>
            <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-2 mt-4">
                <a href="{{ route('search') }}" class="btn btn-dark btn-lg">Manga Keşfet</a>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-dark btn-lg">Hemen Katıl</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark btn-lg">Panelime Git</a>
                @endguest
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Popüler Mangalar</h2>
        <a href="{{ route('search', ['sort' => 'popular']) }}" class="btn btn-sm btn-outline-dark">Tümünü Gör</a>
    </div>
    <div class="row g-4">
        @forelse ($popularMangas as $manga)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    @php($coverUrl = \App\Support\MediaUrlGenerator::fromPath($manga->cover_image_path))
                    @if ($coverUrl)
                        <img src="{{ $coverUrl }}" class="card-img-top" alt="{{ $manga->title }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h3 class="h5 mb-0">
                                <a href="{{ route('mangas.show', $manga) }}" class="text-decoration-none text-dark">
                                    {{ $manga->title }}
                                </a>
                            </h3>
                            <span class="badge bg-dark">{{ $manga->chapters_count }} bölüm</span>
                        </div>
                        <p class="text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($manga->summary, 110) }}</p>
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            @foreach (($manga->genres ?? []) as $genre)
                                <span class="badge rounded-pill text-bg-secondary">{{ $genre }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Henüz popüler manga bulunmuyor.</p>
        @endforelse
    </div>
</section>

<section class="py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Yeni Eklenenler</h2>
        <a href="{{ route('search') }}" class="btn btn-sm btn-outline-dark">Yeni Mangaları Ara</a>
    </div>
    <div class="row g-4">
        @forelse ($latestMangas as $manga)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    @php($coverUrl = \App\Support\MediaUrlGenerator::fromPath($manga->cover_image_path))
                    @if ($coverUrl)
                        <img src="{{ $coverUrl }}" class="card-img-top" alt="{{ $manga->title }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5">
                            <a href="{{ route('mangas.show', $manga) }}" class="text-decoration-none text-dark">{{ $manga->title }}</a>
                        </h3>
                        <p class="text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($manga->summary, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="badge text-bg-light text-dark border">{{ $manga->status }}</span>
                            <small class="text-muted">{{ optional($manga->created_at)->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Henüz manga eklenmemiş.</p>
        @endforelse
    </div>
</section>
@endsection
