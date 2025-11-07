@extends('layouts.app')

@section('content')
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mangas.show', $manga) }}">{{ $manga->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bölüm {{ $chapter->number }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">{{ $manga->title }}</h1>
            <p class="text-muted mb-0">Bölüm {{ $chapter->number }} @if ($chapter->title)<span>&mdash; {{ $chapter->title }}</span>@endif</p>
        </div>
        <div class="btn-group" role="group" aria-label="Bölüm navigasyonu">
            <a href="{{ $previousChapter ? route('chapters.show', [$manga, $previousChapter->number]) : '#' }}"
                class="btn btn-outline-dark @if (!$previousChapter) disabled @endif">&larr; Önceki</a>
            <a href="{{ route('mangas.show', $manga) }}" class="btn btn-dark">Bölümlere Dön</a>
            <a href="{{ $nextChapter ? route('chapters.show', [$manga, $nextChapter->number]) : '#' }}"
                class="btn btn-outline-dark @if (!$nextChapter) disabled @endif">Sonraki &rarr;</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body text-center">
            @if (is_array($chapter->pages) && count($chapter->pages))
                <div class="d-flex flex-column gap-4">
                    @foreach ($chapter->pages as $index => $page)
                        @php($pageUrl = \App\Support\MediaUrlGenerator::fromPath($page))
                        <figure class="mb-0">
                            @if ($pageUrl)
                                <img src="{{ $pageUrl }}" alt="{{ $manga->title }} - Bölüm {{ $chapter->number }} Sayfa {{ $index + 1 }}"
                                    class="img-fluid rounded shadow-sm">
                            @endif
                            <figcaption class="text-muted small mt-2">Sayfa {{ $index + 1 }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">Bu bölüm için sayfa eklenmemiş.</p>
            @endif
        </div>
    </div>

    @include('partials.comments', [
        'comments' => $chapter->comments,
        'storeRoute' => route('chapter-comments.store', [$manga, $chapter->number]),
        'formId' => 'chapter-comment-body',
    ])
@endsection
