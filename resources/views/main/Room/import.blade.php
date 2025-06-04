@extends('layouts.main')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        label:hover {
            background-color: #f0f0f0;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="mb-3">
        <img src="{{ $room->path }}" alt="Smart Class Logo" class="img-fluid" style="max-height: 40px;">
    </div>

    <a href="{{ route('subjects.index', $room->id) }}" style="text-decoration: none;">
        <h1>
            <strong>
                {{ $room->name }}
            </strong>
        </h1>
    </a>
    @auth
        <div class="col-md-4 p-0 mx-auto">
            <div class="p-4 rounded shadow-sm bg-white text-center">
                <h6 class="mb-3">Import Students</h6>

                <form action="{{ route('rooms.students.import', ['room' => $room->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div id="file-name" class="text-muted small mb-2" style="min-height: 1.5em;">No file selected</div>
                    <label class="d-inline-flex align-items-center justify-content-center mb-3 bg-light border rounded-circle"
                        style="width: 60px; height: 60px; cursor: pointer;">
                        <i class="bi bi-upload fs-4 text-primary"></i>
                        <input type="file" name="file" class="d-none" required accept=".xlsx, .xls, .csv"
                            onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'No file selected';">

                    </label>

                    <button type="submit" class="btn btn-info btn-sm w-100">Upload</button>
                </form>
            </div>
        </div>

    @endauth
@endsection
@section('outside')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Students Attendance</h5>
                        <div class="table-responsive">
                            <table id="studentsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Section</th>
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
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    @if (session('importsuccess'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                html: `{!! session('importsuccess') !!}`,
                confirmButtonText: 'OK',
                timer: 5000,
                timerProgressBar: true
            });

            setTimeout(() => {
                location.reload(); // Refresh after 30 seconds
            }, 30000);
        </script>
    @endif


    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('rooms.students', $room->id) }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
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
                    }
                ],
                pageLength: 10
            });
        });
    </script>
@endsection
