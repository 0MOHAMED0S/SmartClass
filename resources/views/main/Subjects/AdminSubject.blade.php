@extends('layouts.main')
@section('styles')
<style>
    /* Icon Selection Visuals */
    .icon-radio:checked + img {
        border: 2px solid #198754;
        box-shadow: 0 0 10px rgba(25, 135, 84, 0.6);
        border-radius: 0.5rem;
    }

    .icon-radio:checked ~ .checkmark {
        display: block !important;
    }

    .selectable-icon {
        cursor: pointer;
        transition: transform 0.2s ease-in-out;
    }

    .selectable-icon:hover {
        transform: scale(1.05);
    }

    /* Scrollbar Styling */
    .d-grid::-webkit-scrollbar {
        width: 6px;
    }

    .d-grid::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }
</style>

    <style>
        div[style*="overflow-y: auto"]::-webkit-scrollbar {
            width: 6px;
        }

        div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }
    </style>
@endsection
@section('modals')
    @include('main.SubjectModals.doctor')
    @include('main.SubjectModals.addAttend')
@endsection
@section('content')
    <div class="mb-3">
        <img src="{{ $room->path }}" alt="Smart Class Logo" class="img-fluid" style="max-height: 40px;">
    </div>
    <a href="{{ route('subjects.index', $room->id) }}" style="text-decoration: none;">
        <h1>
            <strong>
                {{ $room->name }}
                <br>
                <span style="color: rgba(78, 78, 78, 0.986)"> {{ $subject->name }}</span>
            </strong>
        </h1>
    </a>
    <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
        <button class="btn btn-danger d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill" data-bs-toggle="modal"
            data-bs-target="#addattend-{{ $room->id }}">
            <i class="fas fa-plus-circle fa-lg"></i>
            <span>Add Attend</span>
        </button>
        <br>
        @can('room-role', [$room, 'admin'])
            <button class="btn btn-info d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill" data-bs-toggle="modal"
                data-bs-target="#doctor-{{ $room->id }}">
                <i class="fas fa-plus-circle fa-lg"></i>
                <span>Doctor</span>
            </button>
        @endcan
    </div>
    <div class="text-center mt-5">
        <h5 class="text-warning mb-4"><i class="fas fa-calendar-alt me-2"></i>Attendances</h5>
        <div style="max-height: 300px; overflow-y: auto;" class="px-2">
            @if ($room->attendanceCards->isEmpty())
                <p class="text-muted">No attendance records yet.</p>
            @else
                <ul class="list-group list-group-flush" style="max-height: 110px; overflow-y: auto;">
                    @foreach ($subject->attendanceCards as $card)
                        <li class="list-group-item d-flex align-items-center gap-3 px-0" style="height: 40px;">
                            <i class="fas fa-calendar-check text-primary" style="font-size: 18px;"></i>
                            <a style="text-decoration: none" href="{{ route('subjects.attend', ['room' => $room->id, 'subject' => $subject->id, 'attend' => $card->id]) }}"
                            <span class="flex-grow-1 fw-semibold text-dark">{{ $card->name }}</span>
                            </a>
                            <small class="text-muted">{{ $card->created_at->format('M d') }}</small>
                            <a href="{{ route('subjects.attend', ['room' => $room->id, 'subject' => $subject->id, 'attend' => $card->id]) }}"
                                title="View" class="text-info"><i class="fas fa-eye"></i></a>
                            {{-- <a href="#" title="Edit" class="text-warning"><i class="fas fa-edit"></i></a>
                            <a href="#" title="Delete" class="text-danger">
                            <i class="fas fa-trash-alt"></i>
                            </a> --}}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
@section('outside')
<div class="card d-flex justify-content-center align-items-center" style="height: 150px;">
  <form method="GET" action="{{ route('attendance.export',['room' => $room->id, 'subject' => $subject->id]) }}" class="d-flex gap-2 flex-wrap align-items-center justify-content-center">
    <select name="month" class="form-select w-auto">
      <option value="">All Months</option>
      @foreach(range(1, 12) as $m)
        <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
      @endforeach
    </select>

    <select name="section" class="form-select w-auto">
      <option value="">All Sections</option>
      @foreach($room->students->pluck('section')->unique() as $section)
        <option value="{{ $section }}">{{ $section }}</option>
      @endforeach
    </select>

    <button type="submit" class="btn btn-success">
      <i class="fas fa-file-excel"></i> Export Excel
    </button>
  </form>
</div>

    {{-- Students Table --}}
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div id="card" class="card">
                    <div class="card-body">
                        <h5 class="card-title">Students Attendance</h5>
                        <div class="table-responsive">
                            <table id="students_table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Section</th>
                                        @foreach ($room->attendanceCards->where('subject_id', $subject->id)->values() as $index => $attendance)
                                            <th>{{ $index + 1 }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Include DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $('#students_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('attendance.students', ['room' => $room->id, 'subject' => $subject->id]) }}",
                data: {
                    subject_id: "{{ $subject->id }}"
                }
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'section',
                    name: 'section'
                },
                @foreach ($room->attendanceCards->where('subject_id', $subject->id)->values() as $index => $attendance)
                    {
                        data: 'attendance.{{ $index }}',
                        name: 'attendance_{{ $index }}',
                        orderable: false,
                        searchable: false
                    },
                @endforeach

            ]
        });
    </script>
@endsection
