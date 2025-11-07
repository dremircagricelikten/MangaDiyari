@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Hello, {{ $user->name }}!</h2>
                <p class="card-text text-muted">You're logged in and ready to build Manga Diyari.</p>
            </div>
        </div>
    </div>
</div>
@endsection
