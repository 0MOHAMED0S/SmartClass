@extends('layouts.main')
@section('modals')
    @include('main.SubjectModals.add')
    @include('main.SubjectModals.code')
    @include('main.SubjectModals.connect')
@endsection
@section('content')
    <div class="mb-3">
        <img src="{{ $room->path }}" alt="Smart Class Logo" class="img-fluid" style="max-height: 40px;">
    </div>
    <h1>
        <strong>{{ $room->name }}</strong> <span style="color: rgba(78, 78, 78, 0.986)"> </span>
    </h1>
    <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
        @can('room-role', [$room, 'admin'])
            <button class="btn btn-danger d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill" data-bs-toggle="modal"
                data-bs-target="#addsubject-{{ $room->id }}">
                <i class="fas fa-plus-circle fa-lg"></i>
                <span>Add Subject</span>
            </button>
            <a href="{{ route('students.index', $room->id) }}"
                class="btn btn-warning d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill">
                <i class="fas fa-plus-circle fa-lg"></i>
                <span>Import DB</span>
            </a>
            <br>
        @endcan

        <button class="btn btn-info d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill" data-bs-toggle="modal"
            data-bs-target="#connect-{{ $room->id }}">
            <i class="fas fa-plus-circle fa-lg"></i>
            <span>Connect</span>
        </button>
        <button class="btn btn-success d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill" data-bs-toggle="modal"
            data-bs-target="#code">
            <i class="fas fa-sign-in-alt fa-lg"></i>
            <span>{{ $room->code }}</span>
        </button>
    </div>
    <div class="text-center mt-4">
        <!-- Joined Rooms -->
        <div class="mt-3">
            <h5 class="text-warning mb-4">Joined Subjects:</h5>
            <div style="max-height: 120px; overflow-y: auto;">
                <ul class="list-group list-group-flush">
                    @foreach ($room->subjects as $subject)
                        <li class="list-group-item d-flex align-items-center gap-3 px-0" style="height: 40px;">
                            <img src="{{ $subject->path }}" alt="Math 101" class="rounded-circle"
                                style="width: 36px; height: 36px; object-fit: cover;">
                            <a href="{{ route('rooms.subjects.show', ['room' => $room->id, 'subject' => $subject->id]) }}"
                                class="text-decoration-none fw-semibold text-dark flex-grow-1">{{ $subject->name }}</a>
                            <i class="fas fa-door-open text-success"></i>
                        </li>
                    @endforeach
                    <!-- Add more items as needed -->
                </ul>
            </div>
        </div>
    </div>
@endsection
