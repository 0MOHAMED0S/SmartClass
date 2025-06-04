<div class="modal fade" id="connect-{{ $room->id }}" tabindex="-1" aria-labelledby="joinRoomLabel-{{ $room->id }}"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title d-flex align-items-center" id="joinRoomLabel-{{ $room->id }}">
                    <i class="fas fa-door-open me-2"></i> Connect to Room
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="border-0 shadow-sm rounded-4 p-4 mb-3">
                    <h4 class="text-center text-warning fw-bold mb-4">🔍 Enter Your Code</h4>
                    <form action="{{ route('rooms.subjects.connect', $room->id) }}" method="POST"
                        class="needs-validation" novalidate>
                        @csrf
                        <div class="input-group has-validation">
                            <input type="text" name="code" value="{{ $student->code ?? '' }}"
                                class="form-control rounded-start" placeholder="Enter your code" required>
                            <button type="submit" class="btn btn-success rounded-end px-4">
                                <i class="fas fa-sign-in-alt me-1"></i> Connect
                            </button>
                        </div>
                        <div class="invalid-feedback mt-2">
                            Please enter your attendance code.
                        </div>
                    </form>
                </div>
                @if (isset($student))
                    <div class="text-center mt-4">
                        <h4>{{ $student->name }}</h4>
                        <h6 class="text-muted mb-2">Your QR Code:</h6>
                        @if (isset($studentQrCode))
                            {!! $studentQrCode !!}
                        @else
                            <p>No QR code available.</p>
                        @endif
                        <br>
                        <br>
                        <button id="downloadBtn-{{ $room->id }}" class="btn btn-outline-success">
                            <i class="fas fa-download me-1"></i> Download QR
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


