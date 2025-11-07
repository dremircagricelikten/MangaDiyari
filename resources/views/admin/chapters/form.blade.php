@php
    $isEdit = isset($chapter);
    $pageUrls = old('pages', $chapter->pages ?? []);
    if (! is_array($pageUrls) || count($pageUrls) === 0) {
        $pageUrls = [''];
    }
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="manga_id" class="form-label">Manga</label>
        <select name="manga_id" id="manga_id" class="form-select @error('manga_id') is-invalid @enderror" required>
            <option value="">Seçiniz</option>
            @foreach ($mangas as $id => $title)
                <option value="{{ $id }}" @selected(old('manga_id', $chapter->manga_id ?? '') == $id)>{{ $title }}</option>
            @endforeach
        </select>
        @error('manga_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label for="number" class="form-label">Bölüm Numarası</label>
        <input type="number" min="1" class="form-control @error('number') is-invalid @enderror" id="number"
            name="number" value="{{ old('number', $chapter->number ?? '') }}" required>
        @error('number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label for="title" class="form-label">Başlık</label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
            value="{{ old('title', $chapter->title ?? '') }}">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label for="page_files" class="form-label">Sayfa Dosyaları (isteğe bağlı)</label>
        <input type="file" name="page_files[]" id="page_files" class="form-control @error('page_files.*') is-invalid @enderror" multiple accept="image/*">
        <div class="form-text">Birden fazla görsel seçebilirsiniz. Dosya yüklemesi yapılırsa mevcut sayfa listesi otomatik olarak güncellenir.</div>
        @error('page_files')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @error('page_files.*')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label">Sayfa URL'leri</label>
        <div id="pages-wrapper">
            @foreach ($pageUrls as $index => $url)
                <div class="input-group mb-2">
                    <input type="url" name="pages[]" class="form-control @error('pages.' . $index) is-invalid @enderror"
                        value="{{ $url }}" placeholder="https://...">
                    <button class="btn btn-outline-danger remove-page" type="button">Sil</button>
                    @error('pages.' . $index)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach
        </div>
        <button type="button" id="add-page" class="btn btn-outline-primary btn-sm">Yeni Sayfa Ekle</button>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-success">{{ $isEdit ? 'Güncelle' : 'Oluştur' }}</button>
    <a href="{{ route('admin.chapters.index') }}" class="btn btn-secondary">İptal</a>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pagesWrapper = document.getElementById('pages-wrapper');
            const addPageButton = document.getElementById('add-page');

            addPageButton?.addEventListener('click', function () {
                const group = document.createElement('div');
                group.classList.add('input-group', 'mb-2');
                group.innerHTML = `
                    <input type="url" name="pages[]" class="form-control" placeholder="https://...">
                    <button class="btn btn-outline-danger remove-page" type="button">Sil</button>
                `;
                pagesWrapper.appendChild(group);
            });

            pagesWrapper?.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-page')) {
                    const group = event.target.closest('.input-group');
                    group?.remove();
                }
            });
        });
    </script>
@endpush
