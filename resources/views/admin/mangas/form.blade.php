@php($isEdit = isset($manga))

<div class="row g-3">
    <div class="col-md-6">
        <label for="title" class="form-label">Başlık</label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
            value="{{ old('title', $manga->title ?? '') }}" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="slug" class="form-label">Slug</label>
        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
            value="{{ old('slug', $manga->slug ?? '') }}" required>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label for="summary" class="form-label">Özet</label>
        <textarea class="form-control @error('summary') is-invalid @enderror" id="summary" name="summary" rows="4">{{ old('summary', $manga->summary ?? '') }}</textarea>
        @error('summary')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label for="genres" class="form-label">Türler</label>
        <input type="text" class="form-control @error('genres') is-invalid @enderror" id="genres" name="genres"
            value="{{ old('genres', isset($manga) ? implode(', ', $manga->genres ?? []) : '') }}"
            placeholder="Örn: Aksiyon, Fantastik, Romantizm">
        <div class="form-text">Türleri virgül ile ayırarak girin.</div>
        @error('genres')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="status" class="form-label">Durum</label>
        <input type="text" class="form-control @error('status') is-invalid @enderror" id="status" name="status"
            value="{{ old('status', $manga->status ?? 'ongoing') }}" required>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="author" class="form-label">Yazar</label>
        <input type="text" class="form-control @error('author') is-invalid @enderror" id="author" name="author"
            value="{{ old('author', $manga->author ?? '') }}">
        @error('author')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="artist" class="form-label">Çizer</label>
        <input type="text" class="form-control @error('artist') is-invalid @enderror" id="artist" name="artist"
            value="{{ old('artist', $manga->artist ?? '') }}">
        @error('artist')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="published_at" class="form-label">Yayın Tarihi</label>
        <input type="date" class="form-control @error('published_at') is-invalid @enderror" id="published_at"
            name="published_at"
            value="{{ old('published_at', optional($manga->published_at ?? null)->format('Y-m-d')) }}">
        @error('published_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="cover_image" class="form-label">Kapak Görseli</label>
        <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image"
            name="cover_image" accept="image/*">
        @error('cover_image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if ($isEdit && $manga->cover_image_path)
            <div class="mt-2">
                <img src="{{ Storage::url($manga->cover_image_path) }}" alt="{{ $manga->title }}" class="img-thumbnail"
                    style="max-height: 150px;">
            </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-success">{{ $isEdit ? 'Güncelle' : 'Oluştur' }}</button>
    <a href="{{ route('admin.mangas.index') }}" class="btn btn-secondary">İptal</a>
</div>
