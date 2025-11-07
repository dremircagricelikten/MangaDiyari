<div class="card shadow-sm mt-4">
    <div class="card-header">Yorum Yap</div>
    <div class="card-body">
        @auth
            @php($fieldId = $formId ?? 'comment-body')
            <form action="{{ $storeRoute }}" method="POST" class="d-flex flex-column gap-3">
                @csrf
                <div>
                    <label for="{{ $fieldId }}" class="form-label">Yorumunuz</label>
                    <textarea name="body" id="{{ $fieldId }}" rows="4" class="form-control @error('body') is-invalid @enderror"
                        required maxlength="1000">{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-dark">Gönder</button>
                </div>
            </form>
        @else
            <p class="mb-0">
                Yorum yapabilmek için <a href="{{ route('login') }}">giriş yapın</a> veya <a href="{{ route('register') }}">hesap
                    oluşturun</a>.
            </p>
        @endauth
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Yorumlar</h2>
        <span class="badge text-bg-light text-dark border">{{ $comments->count() }}</span>
    </div>
    <div class="list-group list-group-flush">
        @forelse ($comments as $comment)
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>{{ optional($comment->user)->name ?? 'Silinmiş kullanıcı' }}</strong>
                        <span class="text-muted small ms-2">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    @can('delete', $comment)
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST"
                            onsubmit="return confirm('Bu yorumu silmek istediğinize emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link link-danger p-0">Sil</button>
                        </form>
                    @endcan
                </div>
                <p class="mb-0 mt-2">{{ $comment->body }}</p>
            </div>
        @empty
            <div class="list-group-item text-muted">Henüz yorum yapılmamış. İlk yorumu siz yapın!</div>
        @endforelse
    </div>
</div>
