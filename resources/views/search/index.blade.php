@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Manga Arama</h1>
            <p class="text-muted mb-0">Anahtar kelime, durum veya türlere göre filtrele.</p>
        </div>
    </div>

    <form action="{{ route('search') }}" method="GET" class="card shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-12 col-lg-4">
                <label for="q" class="form-label">Anahtar Kelime</label>
                <input type="search" class="form-control" id="q" name="q" value="{{ $query }}" placeholder="Manga veya yazar adı">
            </div>
            <div class="col-6 col-lg-3">
                <label for="status" class="form-label">Durum</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Tümü</option>
                    @foreach ($availableStatuses as $option)
                        <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-lg-3">
                <label for="genre" class="form-label">Tür</label>
                <select name="genre" id="genre" class="form-select">
                    <option value="">Tümü</option>
                    @foreach ($availableGenres as $option)
                        <option value="{{ $option }}" @selected($genre === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-lg-2">
                <label for="sort" class="form-label">Sırala</label>
                <select name="sort" id="sort" class="form-select">
                    <option value="">Yeni Eklenenler</option>
                    <option value="popular" @selected($sort === 'popular')>Popüler</option>
                </select>
            </div>
            <div class="col-12 d-flex justify-content-between">
                <a href="{{ route('search') }}" class="btn btn-link">Filtreleri Temizle</a>
                <button type="submit" class="btn btn-dark">Ara</button>
            </div>
        </div>
    </form>

    <div class="row g-4">
        @forelse ($results as $manga)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    @php($coverUrl = \App\Support\MediaUrlGenerator::fromPath($manga->cover_image_path))
                    @if ($coverUrl)
                        <img src="{{ $coverUrl }}" class="card-img-top" alt="{{ $manga->title }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5">
                            <a href="{{ route('mangas.show', $manga) }}" class="text-decoration-none text-dark">{{ $manga->title }}</a>
                        </h2>
                        <p class="text-muted small flex-grow-1">{{ \Illuminate\Support\Str::limit($manga->summary, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="badge text-bg-light text-dark border">{{ ucfirst($manga->status) }}</span>
                            <span class="badge bg-dark">{{ $manga->chapters_count }} bölüm</span>
                        </div>
                        @if ($manga->genres)
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                @foreach ($manga->genres as $tag)
                                    <span class="badge rounded-pill text-bg-secondary">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info mb-0">Aramanızla eşleşen manga bulunamadı.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $results->links() }}
    </div>
@endsection
