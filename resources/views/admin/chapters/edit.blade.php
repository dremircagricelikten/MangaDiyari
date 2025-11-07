@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Bölüm Düzenle</h1>
        <a href="{{ route('admin.chapters.index') }}" class="btn btn-link">&larr; Geri</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.chapters.update', $chapter) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.chapters.form', ['chapter' => $chapter, 'mangas' => $mangas])
            </form>
        </div>
    </div>
@endsection
