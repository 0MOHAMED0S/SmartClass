<div class="modal fade" id="joinroom" tabindex="-1" aria-labelledby="joinRoomLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title d-flex align-items-center" id="joinRoomLabel">
                    <i class="fas fa-door-open me-2"></i> Join a Room
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="{{ route('rooms.join') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roomCode" class="form-label">
                            <i class="fas fa-key me-1 text-muted"></i> Room Code
                        </label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="roomCode"
                            name="code" placeholder="Enter the room code">
                        @error('code')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-sign-in-alt me-1"></i> Join Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
