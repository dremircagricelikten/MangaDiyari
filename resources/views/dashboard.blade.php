@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-dark text-white">
            <div class="card-body">
                <h2 class="card-title mb-2">Hoş geldin, {{ $user->name }}!</h2>
                <p class="card-text text-white-50 mb-0">Favori mangalarını yönet, okuma listeni takip et.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        @include('profile.partials.favorites', ['favorites' => $favorites])
    </div>
    <div class="col-lg-6">
        @include('profile.partials.reading-list', ['readingList' => $readingList])
    </div>
</div>
@endsection
