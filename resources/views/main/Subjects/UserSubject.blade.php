@extends('layouts.main')
@section('styles')
    <style>
        /* Scrollbar styling */
        div[style*="overflow-y: auto"]::-webkit-scrollbar {
            width: 6px;
        }

        div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }
    </style>
    <style>
        .main-content {
            min-height: 50vh !important;
            height: auto;
        }
    </style>
@endsection
@section('modals')
    @include('main.SubjectModals.connect')
    @include('main.SubjectModals.addAttend')
@endsection
@section('content')
    <div class="mb-3">
        <img src="{{ $room->path }}" alt="Smart Class Logo" class="img-fluid" style="max-height: 40px;">
    </div>
    <h1>
        <strong>{{ $room->name }}</strong><br> <span style="color: rgba(78, 78, 78, 0.986)"> {{ $subject->name }}</span>
    </h1>
    <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
        <button class="btn btn-info d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill" data-bs-toggle="modal"
            data-bs-target="#connect-{{ $room->id }}">
            <i class="fas fa-plus-circle fa-lg"></i>
            <span>Connect</span>
        </button>
    </div>
@endsection
@section('outside')
    @if (isset($student))
        <div class="card m-2 shadow-lg border-0 rounded-4 p-4">
            <h5 class="card-title text-center text-success fw-bold mb-3">📋 Your Attendance Record</h5>
            <div class="table-responsive">
                <table class="table table-hover text-center align-middle border">
                    <thead class="table-primary">
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Section</th>
                            @foreach ($room->attendanceCards as $attendance)
                                <th>Session {{ $loop->index + 1 }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">{{ $student->name }}</td>
                            <td class="text-muted">{{ $student->code }}</td>
                            <td>{{ $student->section }}</td>
                            @foreach ($room->attendanceCards as $attendance)
                                @php
                                    $record = $student->attendanceRecords
                                        ->where('attendance_id', $attendance->id)
                                        ->first();
                                @endphp
                                <td>
                                    @if ($record && $record->status == 1)
                                        <span class="badge bg-success">✅ Present</span>
                                    @else
                                        <span class="badge bg-danger">❌ Absent</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
