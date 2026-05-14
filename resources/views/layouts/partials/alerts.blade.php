@if(session('success'))
    <div class="alert alert-success d-flex align-items-center border-0 border-start border-success border-4 rounded-3 shadow-sm mb-4 animate__animated animate__fadeInDown" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success me-3"></i>
        <div class="fw-medium">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center border-0 border-start border-danger border-4 rounded-3 shadow-sm mb-4 animate__animated animate__fadeInDown" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger me-3"></i>
        <div class="fw-medium">{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning d-flex align-items-center border-0 border-start border-warning border-4 rounded-3 shadow-sm mb-4 animate__animated animate__fadeInDown" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-warning me-3"></i>
        <div class="fw-medium">{{ session('warning') }}</div>
        <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-center border-0 border-start border-danger border-4 rounded-3 shadow-sm mb-4 animate__animated animate__fadeInDown" role="alert">
        <i class="bi bi-x-circle-fill fs-5 text-danger me-3"></i>
        <div>
            <div class="fw-bold">Terjadi kesalahan:</div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
