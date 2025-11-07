@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manga Düzenle</h1>
        <a href="{{ route('admin.mangas.index') }}" class="btn btn-link">&larr; Geri</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.mangas.update', $manga) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.mangas.form', ['manga' => $manga])
            </form>
        </div>
    </div>
@endsection
