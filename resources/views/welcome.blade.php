@extends('layouts.main')
@section('modals')
    @auth
        @if (auth()->user()->role == 'admin')
            @include('main.RoomModals.add')
        @endif
        @include('main.RoomModals.join')
    @endauth
@endsection
@section('content')
    <div class="mb-3">
        <img src="{{ asset('assets/MainFiles/logo.png') }}" alt="Smart Class Logo" class="img-fluid" style="max-height: 80px;">
    </div>
    <h1><strong>Smart Class</strong></h1>

    @guest
        <a href="{{ route('login') }}" class="btn btn-login">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo"
                style="width: 20px; height: 20px; margin-right: 8px;" />
            Sign in with Google
        </a>
    @endguest

    @auth
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            @if (auth()->user()->role == 'admin')
                <button class="btn btn-danger d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill"
                    data-bs-toggle="modal" data-bs-target="#addroom">
                    <i class="fas fa-plus-circle fa-lg"></i>
                    <span>Add Room</span>
                </button>
            @endif
            <button class="btn btn-success d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill" data-bs-toggle="modal"
                data-bs-target="#joinroom">
                <i class="fas fa-sign-in-alt fa-lg"></i>
                <span>Join Room</span>
            </button>
        </div>
        <div class="text-center mt-4">
            <!-- Joined Rooms -->
            <div class="mt-3">
                <h5 class="text-warning mb-4">Joined Rooms:</h5>
                <div style="max-height: 120px; overflow-y: auto;">
                    <ul class="list-group list-group-flush">
                        @forelse ($sidebarRooms as $room)
                            <li class="list-group-item d-flex align-items-center gap-3 px-0" style="height: 40px;">
                                <img src="{{ $room->path }}" alt="{{ $room->name }}" class="rounded-circle"
                                    style="width: 36px; height: 36px; object-fit: cover;">
                                <a href="{{ route('subjects.index', $room->id) }}"
                                    class="text-decoration-none fw-semibold text-dark flex-grow-1">{{ $room->name }}</a>
                                <i class="fas fa-door-open text-success"></i>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center">No rooms joined yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    @endauth
@endsection
