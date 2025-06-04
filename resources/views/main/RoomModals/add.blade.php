<div class="modal fade" id="addroom" tabindex="-1" aria-labelledby="addRoomLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title d-flex align-items-center" id="addRoomLabel">
                    <i class="fas fa-plus-circle me-2"></i> Add New Room
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="{{ route('rooms.store') }}" method="POST" >
                @csrf
                <div class="modal-body">
                    <!-- Room Name -->
                    <div class="mb-3">
                        <label for="roomName" class="form-label">
                            <i class="fas fa-tag me-1 text-muted"></i> Room Name
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="roomName"
                            name="name" placeholder="Enter room name">
                        @error('name')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Image Selection -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-images me-1 text-muted"></i> Choose an Icon
                        </label>
                        <div class="d-grid gap-2 overflow-auto"
                            style="max-height: 170px; grid-template-columns: repeat(6, 1fr);">
                            @php
                                $icons = [
                                    'https://cdn-icons-png.flaticon.com/512/3593/3593422.png',
                                    'https://cdn-icons-png.flaticon.com/512/3593/3593441.png',
                                    'https://cdn-icons-png.flaticon.com/512/3593/3593462.png',
                                    'https://cdn-icons-png.flaticon.com/512/9072/9072569.png',
                                    'https://cdn-icons-png.flaticon.com/512/2354/2354280.png',
                                    'https://cdn-icons-png.flaticon.com/512/3449/3449632.png',
                                    'https://cdn-icons-png.flaticon.com/512/185/185570.png'  ,
                                    'https://cdn-icons-png.flaticon.com/512/6660/6660279.png',
                                    'https://cdn-icons-png.flaticon.com/512/5608/5608848.png',
                                    'https://cdn-icons-png.flaticon.com/512/3874/3874104.png',
                                    'https://cdn-icons-png.flaticon.com/512/167/167707.png'  ,
                                    'https://cdn-icons-png.flaticon.com/512/3103/3103446.png',
                                    'https://cdn-icons-png.flaticon.com/512/1995/1995581.png',
                                    'https://cdn-icons-png.flaticon.com/512/906/906175.png'  ,
                                    'https://cdn-icons-png.flaticon.com/512/942/942748.png'  ,
                                    'https://cdn-icons-png.flaticon.com/512/3515/3515781.png',
                                    'https://cdn-icons-png.flaticon.com/512/3095/3095583.png',
                                    'https://cdn-icons-png.flaticon.com/512/6816/6816631.png',
                                    'https://cdn-icons-png.flaticon.com/512/747/747310.png'  ,
                                    'https://cdn-icons-png.flaticon.com/512/10714/10714533.png',
                                    'https://cdn-icons-png.flaticon.com/512/609/609803.png',
                                    'https://cdn-icons-png.flaticon.com/512/545/545705.png',
                                    'https://cdn-icons-png.flaticon.com/512/2985/2985168.png',
                                    'https://cdn-icons-png.flaticon.com/512/2991/2991108.png',
                                    'https://cdn-icons-png.flaticon.com/512/3062/3062634.png',
                                    'https://cdn-icons-png.flaticon.com/512/2474/2474530.png',
                                    'https://cdn-icons-png.flaticon.com/512/4954/4954819.png',
                                    'https://cdn-icons-png.flaticon.com/512/1828/1828645.png',
                                ];

                            @endphp
                            @foreach ($icons as $index => $icon)
                                <label class="position-relative">
                                    <input type="radio" name="path" value="{{ $icon }}"
                                        class="d-none icon-radio" required>
                                    <img src="{{ $icon }}"
                                        class="selectable-icon img-thumbnail rounded-3 border border-2 border-transparent"
                                        width="50" alt="Icon {{ $index }}">
                                    <span
                                        class="position-absolute top-0 end-0 badge bg-success rounded-circle d-none checkmark">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('path')
                            <div class="text-danger mt-2">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-check-circle me-1"></i> Create Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
