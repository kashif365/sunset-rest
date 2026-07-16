@if(session('success') || session('error'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        @if(session('success'))
            <div class="toast auto-show text-bg-success border-0" role="status" aria-live="polite" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast auto-show text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>
@endif
