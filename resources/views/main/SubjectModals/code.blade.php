<div class="modal fade" id="code" tabindex="-1" aria-labelledby="joinRoomLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title d-flex align-items-center" id="joinRoomLabel">
                    <i class="fas fa-door-open me-2"></i> Room Code
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Room Code with Copy -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-key me-1 text-muted"></i> Room Code
                    </label>
                    <div class="input-group">
                        <span class="form-control bg-light" id="roomCodeDisplay">{{ $room->code }}</span>
                        <button type="button" class="btn btn-outline-success" id="copyBtn"
                            data-code="{{ $room->code }}" title="Copy">
                            <i class="fas fa-copy" id="copyIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- QR Code Display -->
                <div class="text-center my-3 qr-code-wrapper">
                    {!! $roomQrCode !!}
                </div>

                <!-- Download QR Button -->
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-outline-success" id="downloadBtn">
                        <i class="fas fa-download me-1"></i> Download QR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
