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
@endsection
@section('content')
    <div class="mb-3">
        <img src="{{ $room->path }}" alt="Smart Class Logo" class="img-fluid" style="max-height: 40px;">
    </div>
    <h1>
        <strong>{{ $room->name }}</strong><br> <span style="color: rgba(78, 78, 78, 0.986)"> {{ $subject->name }}</span>
    </h1>
    <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
        <a href="{{ route('attend.scan.index', ['room' => $room->id, 'subject' => $subject->id, 'attend' => $attendance->id]) }}"
            class="btn btn-warning d-flex align-items-center gap-2 shadow px-4 py-2 rounded-pill">
            <i class="fas fa-plus-circle fa-lg"></i>
            <span>Scan</span>
        </a>
        <br>
    </div>
    <div class="text-center mt-5">
        <h5 class="text-warning mb-4"><i class="fas fa-calendar-alt me-2"></i>Attendances</h5>
        <div style="max-height: 300px; overflow-y: auto;" class="px-2">
            <ul class="list-group list-group-flush" style="max-height: 110px; overflow-y: auto;">
                <li class="list-group-item d-flex align-items-center gap-3 px-0" style="height: 40px;">
                    <i class="fas fa-calendar-check text-primary" style="font-size: 18px;"></i>
                    <span class="flex-grow-1 fw-semibold text-dark">{{ $attendance->name }}</span>
                    <small class="text-muted">{{ $attendance->created_at->format('Y M d') }}</small>
                    {{-- <a href="#" title="Edit" class="text-warning"><i class="fas fa-edit"></i></a>
                    <a href="#" title="Delete" class="text-danger">
                        <i class="fas fa-trash-alt"></i>
                    </a> --}}
                </li>
            </ul>
        </div>
    </div>
@endsection
@section('outside')
    {{-- Students Table --}}
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Students Attendance</h5>
                        <div class="table-responsive">
                            <table id="students_table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Section</th>
                                        <th>Status</th>
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
        $(document).ready(function() {
            $('#students_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('subjects.attend.students', [$room->id, $subject->id, $attend]) }}",
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
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endsection
