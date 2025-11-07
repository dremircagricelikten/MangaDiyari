@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Yeni Bölüm Oluştur</h1>
        <a href="{{ route('admin.chapters.index') }}" class="btn btn-link">&larr; Geri</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.chapters.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.chapters.form', ['mangas' => $mangas])
            </form>
        </div>
    </div>
@endsection
