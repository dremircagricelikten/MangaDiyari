@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Bölümler</h1>
        <a href="{{ route('admin.chapters.create') }}" class="btn btn-primary">Yeni Bölüm</a>
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
                            <th>Manga</th>
                            <th>Bölüm</th>
                            <th>Başlık</th>
                            <th>Sayfa Sayısı</th>
                            <th class="text-end">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($chapters as $chapter)
                            <tr>
                                <td>{{ $chapter->manga->title }}</td>
                                <td>#{{ $chapter->number }}</td>
                                <td>{{ $chapter->title ?? '-' }}</td>
                                <td>{{ is_array($chapter->pages) ? count($chapter->pages) : 0 }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.chapters.edit', $chapter) }}" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                                    <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Bu bölümü silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Henüz bölüm eklenmedi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($chapters->hasPages())
            <div class="card-footer">
                {{ $chapters->links() }}
            </div>
        @endif
    </div>
@endsection
