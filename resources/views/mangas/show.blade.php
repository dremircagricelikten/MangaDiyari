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
                @php($coverUrl = \App\Support\MediaUrlGenerator::fromPath($manga->cover_image_path))
                @if ($coverUrl)
                    <img src="{{ $coverUrl }}" class="card-img-top" alt="{{ $manga->title }}">
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
                        <form method="POST"
                            action="{{ $isSubscribed ? route('mangas.unsubscribe', $manga) : route('mangas.subscribe', $manga) }}"
                            class="mt-3">
                            @csrf
                            @if ($isSubscribed)
                                @method('DELETE')
                            @endif
                            <button type="submit"
                                class="btn {{ $isSubscribed ? 'btn-outline-danger' : 'btn-dark' }} w-100">
                                {{ $isSubscribed ? 'Aboneliği Sonlandır' : 'Yeni Bölümleri Maille Al' }}
                            </button>
                        </form>
                    @else
                        <p class="text-muted small mt-3">
                            Yeni bölümler yayınlandığında bildirim almak için <a href="{{ route('login') }}">giriş yapın</a>.
                        </p>
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

            @include('partials.comments', [
                'comments' => $manga->comments,
                'storeRoute' => route('manga-comments.store', $manga),
                'formId' => 'manga-comment-body',
            ])
        </div>
    </div>
@endsection
