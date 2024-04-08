@if (session('success'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <i class="ri-check-double-line label-icon"></i>
        <strong> {{ session('success') }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('delete'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-3 align-middle fs-16"></i>
        <strong> {{ session('delete') }} </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
