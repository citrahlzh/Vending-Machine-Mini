@vite("resources/js/app.js")

<script src="{{ asset("assets/js/jquery.js") }}"></script>
<script src="{{ asset("assets/js/jquery.datatables.min.js") }}"></script>
@include("dashboard.partials.datatables-script")

{{-- Sweetalert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    let successMessage = @json(session('success'));
    let errorMessage = @json(session('error'));

    if (successMessage) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: successMessage,
            showConfirmButton: false,
            timer: 3000
        });
    }

    if (errorMessage) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: errorMessage,
            showConfirmButton: false,
            timer: 3000
        });
    }
</script>
