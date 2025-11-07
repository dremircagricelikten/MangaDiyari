@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Yeni Manga Oluştur</h1>
        <a href="{{ route('admin.mangas.index') }}" class="btn btn-link">&larr; Geri</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.mangas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.mangas.form')
            </form>
        </div>
    </div>
@endsection
