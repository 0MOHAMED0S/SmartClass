@if ($errors->any())
    @php
        $allErrors = $errors->all();
        $totalErrors = count($allErrors);
        $displayLimit = 5;
    @endphp

    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow"
        role="alert" style="z-index: 1055; width: 90%; max-width: 500px;">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>Validation Error!</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach (array_slice($allErrors, 0, $displayLimit) as $error)
                <li>{{ $error }}</li>
            @endforeach
            @if ($totalErrors > $displayLimit)
                <li><em>+{{ $totalErrors - $displayLimit }} more</em></li>
            @endif
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


{{-- Fixed Success Message --}}
{{-- @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow"
        role="alert" style="z-index: 1055; width: 90%; max-width: 500px;">
        <strong><i class="fas fa-check-circle me-2"></i>Success!</strong>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif --}}
{{-- Fixed info Message --}}
@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow"
        role="alert" style="z-index: 1055; width: 90%; max-width: 500px;">
        <strong><i class="fas fa-check-circle me-2"></i>info!</strong>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
{{-- Fixed Error Message --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow"
        role="alert" style="z-index: 1055; width: 90%; max-width: 500px;">
        <strong><i class="fas fa-times-circle me-2"></i>Error!</strong>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
