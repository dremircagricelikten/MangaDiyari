@extends('layouts.app')

@section('content')
<div class="text-center py-5">
    <h1 class="display-4 fw-bold">Welcome to {{ config('app.name', 'MangaDiyari') }}</h1>
    <p class="lead text-muted">Discover and share your favourite manga adventures.</p>
    @guest
        <a href="{{ route('register') }}" class="btn btn-dark btn-lg mt-3">Join Now</a>
    @else
        <a href="{{ route('dashboard') }}" class="btn btn-outline-dark btn-lg mt-3">Go to Dashboard</a>
    @endguest
</div>
@endsection
