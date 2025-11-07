<div class="card shadow-sm h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">Okuma Listem</h2>
        <span class="badge text-bg-light text-dark border">{{ $readingList->count() }}</span>
    </div>
    <div class="list-group list-group-flush">
        @forelse ($readingList as $item)
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <h3 class="h6 mb-1">{{ $item->title }}</h3>
                        <p class="small text-muted mb-2">
                            {{ $item->chapters_count }} bölüm &middot; Son okunan bölüm: {{ $item->pivot->last_read_chapter_number }}
                            @if ($item->pivot->last_read_at)
                                <br><span class="text-body-secondary">Güncellenme: {{ \Illuminate\Support\Carbon::parse($item->pivot->last_read_at)->format('d.m.Y H:i') }}</span>
                            @endif
                        </p>
                    </div>
                    <form action="{{ route('reading-list.update', $item) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="number" min="0" name="last_read_chapter_number"
                            value="{{ old('last_read_chapter_number', $item->pivot->last_read_chapter_number) }}"
                            class="form-control form-control-sm" style="max-width: 6rem;">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Güncelle</button>
                    </form>
                </div>
                <form action="{{ route('reading-list.destroy', $item) }}" method="POST" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger p-0">Listeden kaldır</button>
                </form>
            </div>
        @empty
            <div class="list-group-item text-muted">Okuma listende manga bulunmuyor.</div>
        @endforelse
    </div>
</div>
