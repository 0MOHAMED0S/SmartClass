<div class="modal fade" id="addattend-{{ $room->id }}" tabindex="-1" aria-labelledby="addAttendLabel-{{ $room->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <!-- Modal Header -->
            <div class="modal-header bg-success text-white rounded-top-4">
                <h5 class="modal-title d-flex align-items-center" id="addAttendLabel-{{ $room->id }}">
                    <i class="fas fa-calendar-plus me-2"></i> Create New Attendance
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Form -->
            <form action="{{ route('rooms.subjects.attendance.store', ['room' => $room->id, 'subject' => $subject->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label for="attendanceName-{{ $room->id }}" class="form-label fw-semibold">Attendance Name</label>
                    <div class="input-group">
                        <input type="text" id="attendanceName-{{ $room->id }}" name="name" class="form-control" placeholder="Enter attendance title..." >
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-between px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
