@extends('layouts.main')
@section('styles')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom CSS + JS to Highlight Selected Icon -->
<style>
    .icon-radio:checked+img {
        border-color: #198754 !important;
        box-shadow: 0 0 10px rgba(25, 135, 84, 0.6);
    }

    .icon-radio:checked~.checkmark {
        display: block !important;
    }

    .selectable-icon {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .selectable-icon:hover {
        transform: scale(1.05);
    }
</style>
<style>
    .d-grid::-webkit-scrollbar {
        width: 6px;
    }

    .d-grid::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }
</style>
@endsection
@section('modals')
    @can('room-role', [$room, 'admin'])
        @include('main.SubjectModals.add')
    @endcan
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
            <a href="{{ route('rooms.students.index', $room->id) }}"
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
        <h5 class="text-warning mb-4">The Subjects:</h5>
        <div style="max-height: 120px; overflow-y: auto;">
            <ul class="list-group list-group-flush">
                @forelse ($room->subjects as $subject)
                    <li class="list-group-item d-flex align-items-center gap-3 px-0" style="height: 40px;">
                        <img src="{{ $subject->path }}" alt="{{ $subject->name }}" class="rounded-circle"
                            style="width: 36px; height: 36px; object-fit: cover;">
                        <a href="{{ route('rooms.subjects.show', ['room' => $room->id, 'subject' => $subject->id]) }}"
                            class="text-decoration-none fw-semibold text-dark flex-grow-1">{{ $subject->name }}</a>
                        <i class="fas fa-door-open text-success"></i>
                    </li>
                @empty
                    <li class="list-group-item text-muted text-center">No subjects yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
            // === Copy Student Code ===
    document.addEventListener('DOMContentLoaded', function () {
        const downloadBtn = document.getElementById('downloadBtn-{{ $room->id }}');

        if (downloadBtn) {
            downloadBtn.addEventListener('click', function () {
                const modal = document.getElementById('connect-{{ $room->id }}');
                const svg = modal.querySelector('.qr-code-wrapper svg') || modal.querySelector('svg');

                if (!svg) {
                    alert("QR code not found!");
                    return;
                }

                const svgData = new XMLSerializer().serializeToString(svg);
                const svgBlob = new Blob([svgData], { type: "image/svg+xml;charset=utf-8" });
                const DOMURL = window.URL || window.webkitURL || window;
                const url = DOMURL.createObjectURL(svgBlob);
                const img = new Image();

                img.onload = function () {
                    const padding = 20; // extra space around QR code
                    const scale = 2; // increase resolution
                    const width = img.width * scale + padding * 2;
                    const height = img.height * scale + padding * 2;

                    const canvas = document.createElement("canvas");
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext("2d");

                    // White background
                    ctx.fillStyle = "#ffffff";
                    ctx.fillRect(0, 0, width, height);

                    // Draw QR in center
                    ctx.drawImage(img, padding, padding, img.width * scale, img.height * scale);

                    DOMURL.revokeObjectURL(url);

                    const imgURI = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");

                    const a = document.createElement("a");
                    a.setAttribute("download", "student-qr.png");
                    a.setAttribute("href", imgURI);
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                };

                img.src = url;
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // === Copy Room Code ===
        const copyBtn = document.getElementById('copyBtn');
        const copyIcon = document.getElementById('copyIcon');

        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                const code = this.getAttribute('data-code');

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(code).then(showCopiedIcon).catch(fallbackCopy);
                } else {
                    fallbackCopy();
                }

                function fallbackCopy() {
                    const textarea = document.createElement('textarea');
                    textarea.value = code;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    showCopiedIcon();
                }

                function showCopiedIcon() {
                    copyIcon.classList.replace('fa-copy', 'fa-check');
                    setTimeout(() => {
                        copyIcon.classList.replace('fa-check', 'fa-copy');
                    }, 1500);
                }
            });
        }

        // === Professional QR Code Download ===
        const downloadBtn = document.getElementById('downloadBtn');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function () {
                const svg = document.querySelector('.qr-code-wrapper svg');

                if (!svg) {
                    alert("QR code not found!");
                    return;
                }

                const svgData = new XMLSerializer().serializeToString(svg);
                const svgBlob = new Blob([svgData], { type: "image/svg+xml;charset=utf-8" });
                const DOMURL = window.URL || window.webkitURL || window;
                const url = DOMURL.createObjectURL(svgBlob);
                const img = new Image();

                img.onload = function () {
                    const padding = 20;
                    const scale = 2;
                    const width = img.width * scale + padding * 2;
                    const height = img.height * scale + padding * 2;

                    const canvas = document.createElement("canvas");
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext("2d");

                    ctx.fillStyle = "#ffffff"; // white background
                    ctx.fillRect(0, 0, width, height);

                    ctx.drawImage(img, padding, padding, img.width * scale, img.height * scale);

                    DOMURL.revokeObjectURL(url);

                    const imgURI = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");

                    const a = document.createElement("a");
                    a.setAttribute("download", "room-qr.png");
                    a.setAttribute("href", imgURI);
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                };

                img.src = url;
            });
        }
    });
</script>
@endsection
