@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Çoklu Bölüm Yükleme</h1>
        <a href="{{ route('admin.chapters.index') }}" class="btn btn-link">&larr; Geri</a>
    </div>

    <div class="alert alert-info">
        <p class="mb-1"><strong>İpucu:</strong> Arşiv dosyanız her bölüm için ayrı klasörler içermelidir.</p>
        <ul class="mb-0 small">
            <li>Klasör adları bölüm numarasını içermelidir (örn. <code>chapter-01</code>, <code>12</code>, <code>bolum_003</code>).</li>
            <li>Her klasörde yalnızca sıralamak istediğiniz görseller yer almalıdır.</li>
            <li>Aynı numaraya sahip mevcut bölümler atlanır ve silinmez.</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.chapters.upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="manga_id" class="form-label">Manga</label>
                        <select name="manga_id" id="manga_id" class="form-select @error('manga_id') is-invalid @enderror" required>
                            <option value="">Seçiniz</option>
                            @foreach ($mangas as $id => $title)
                                <option value="{{ $id }}" @selected(old('manga_id') == $id)>{{ $title }}</option>
                            @endforeach
                        </select>
                        @error('manga_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="archive" class="form-label">ZIP Arşivi</label>
                        <input type="file" name="archive" id="archive" class="form-control @error('archive') is-invalid @enderror" accept=".zip" required>
                        @error('archive')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Yüklemeyi Başlat</button>
                    <a href="{{ route('admin.chapters.index') }}" class="btn btn-secondary">İptal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
