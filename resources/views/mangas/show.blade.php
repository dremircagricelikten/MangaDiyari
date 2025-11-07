@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Ana Sayfa</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $manga->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                @if ($manga->cover_image_path)
                    <img src="{{ Storage::url($manga->cover_image_path) }}" class="card-img-top" alt="{{ $manga->title }}">
                @endif
                <div class="card-body">
                    <h1 class="h4 mb-3">{{ $manga->title }}</h1>
                    <div class="mb-3">
                        <span class="badge text-bg-dark">{{ ucfirst($manga->status) }}</span>
                        @if ($manga->published_at)
                            <span class="badge text-bg-light text-dark border">{{ $manga->published_at->format('d.m.Y') }}</span>
                        @endif
                    </div>
                    <dl class="row mb-0 small text-muted">
                        @if ($manga->author)
                            <dt class="col-4">Yazar</dt>
                            <dd class="col-8">{{ $manga->author }}</dd>
                        @endif
                        @if ($manga->artist)
                            <dt class="col-4">Çizer</dt>
                            <dd class="col-8">{{ $manga->artist }}</dd>
                        @endif
                        <dt class="col-4">Bölüm</dt>
                        <dd class="col-8">{{ $manga->chapters->count() }}</dd>
                    </dl>
                    @auth
                        <div class="d-grid gap-2 mt-4">
                            @if ($isFavorite)
                                <form action="{{ route('favorites.destroy', $manga) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">Favorilerden Çıkar</button>
                                </form>
                            @else
                                <form action="{{ route('favorites.store', $manga) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">Favorilere Ekle</button>
                                </form>
                            @endif

                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h2 class="h6 mb-3">Okuma Listesi</h2>
                                    @if ($readingListEntry)
                                        <p class="small text-muted mb-3">Son okunan bölüm: <strong>{{ $readingListEntry->pivot->last_read_chapter_number }}</strong></p>
                                        <form action="{{ route('reading-list.update', $manga) }}" method="POST" class="row g-2 align-items-center">
                                            @csrf
                                            @method('PATCH')
                                            <div class="col-7">
                                                <label class="visually-hidden" for="last_read_chapter_number">Son Bölüm</label>
                                                <input type="number" min="0" class="form-control @error('last_read_chapter_number') is-invalid @enderror"
                                                    id="last_read_chapter_number" name="last_read_chapter_number"
                                                    value="{{ old('last_read_chapter_number', $readingListEntry->pivot->last_read_chapter_number) }}">
                                                @error('last_read_chapter_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-5 d-grid">
                                                <button type="submit" class="btn btn-outline-primary">İlerlemeni Güncelle</button>
                                            </div>
                                        </form>
                                        <form action="{{ route('reading-list.destroy', $manga) }}" method="POST" class="mt-3">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0">Okuma listesinden kaldır</button>
                                        </form>
                                    @else
                                        <form action="{{ route('reading-list.store', $manga) }}" method="POST" class="row g-2 align-items-center">
                                            @csrf
                                            <div class="col-7">
                                                <label class="visually-hidden" for="last_read_chapter_number">Son Bölüm</label>
                                                <input type="number" min="0" class="form-control @error('last_read_chapter_number') is-invalid @enderror"
                                                    id="last_read_chapter_number" name="last_read_chapter_number"
                                                    value="{{ old('last_read_chapter_number', 0) }}"
                                                    placeholder="Son okunan bölüm">
                                                @error('last_read_chapter_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-5 d-grid">
                                                <button type="submit" class="btn btn-outline-primary">Okuma Listesine Ekle</button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
            @if ($manga->genres)
                <div class="card shadow-sm mt-4">
                    <div class="card-header">Türler</div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        @foreach ($manga->genres as $genre)
                            <span class="badge rounded-pill text-bg-secondary">{{ $genre }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">Özet</div>
                <div class="card-body">
                    <p class="mb-0">{{ $manga->summary ?: 'Bu manga için henüz özet eklenmemiş.' }}</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0">Bölüm Listesi</h2>
                    <span class="badge text-bg-light text-dark border">{{ $manga->chapters->count() }} bölüm</span>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($manga->chapters as $chapter)
                        <a href="{{ route('chapters.show', [$manga, $chapter->number]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>
                                <strong>Bölüm {{ $chapter->number }}</strong>
                                @if ($chapter->title)
                                    <span class="text-muted">&mdash; {{ $chapter->title }}</span>
                                @endif
                            </span>
                            <span class="badge rounded-pill text-bg-secondary">{{ is_countable($chapter->pages) ? count($chapter->pages) : 0 }} sayfa</span>
                        </a>
                    @empty
                        <div class="list-group-item text-muted">Bu manga için henüz bölüm eklenmemiş.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
