@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Mangalar</h1>
        <a href="{{ route('admin.mangas.create') }}" class="btn btn-primary">Yeni Manga</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Kapak</th>
                            <th>Başlık</th>
                            <th>Durum</th>
                            <th>Yazar</th>
                            <th>Yayın Tarihi</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mangas as $manga)
                            <tr>
                                <td style="width: 80px;">
                                    @php($coverUrl = \App\Support\MediaUrlGenerator::fromPath($manga->cover_image_path))
                                    @if ($coverUrl)
                                        <img src="{{ $coverUrl }}" alt="{{ $manga->title }}" class="img-fluid rounded" />
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $manga->title }}</td>
                                <td>{{ ucfirst($manga->status) }}</td>
                                <td>{{ $manga->author ?? '-' }}</td>
                                <td>{{ optional($manga->published_at)->format('d.m.Y') ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.mangas.edit', $manga) }}" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                                    <form action="{{ route('admin.mangas.destroy', $manga) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Bu mangayı silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Henüz manga eklenmedi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($mangas->hasPages())
            <div class="card-footer">
                {{ $mangas->links() }}
            </div>
        @endif
    </div>
@endsection
