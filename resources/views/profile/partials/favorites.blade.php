<div class="card shadow-sm h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Favori Mangalarım</h2>
        <span class="badge text-bg-light text-dark border">{{ $favorites->count() }}</span>
    </div>
    <div class="list-group list-group-flush">
        @forelse ($favorites as $favorite)
            <div class="list-group-item d-flex justify-content-between align-items-start gap-3">
                <div>
                    <h3 class="h6 mb-1">{{ $favorite->title }}</h3>
                    <p class="small text-muted mb-0">{{ $favorite->chapters_count }} bölüm &middot; {{ ucfirst($favorite->status) }}</p>
                </div>
                <form action="{{ route('favorites.destroy', $favorite) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Kaldır</button>
                </form>
            </div>
        @empty
            <div class="list-group-item text-muted">Henüz favori eklemedin.</div>
        @endforelse
    </div>
</div>
