<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var swalTheme = {
        background: '#23262b',
        color: '#d9e1ea',
        confirmButtonColor: '#d5ac42',
        customClass: {
            popup: 'dash-swal-popup',
            confirmButton: 'dash-swal-confirm',
            cancelButton: 'dash-swal-cancel',
        }
    };

    document.querySelectorAll('form[data-swal-delete]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var title = form.getAttribute('data-swal-title') || @json(__('Confirm deletion'));
            var text = form.getAttribute('data-swal-text') || '';
            Swal.fire({
                ...swalTheme,
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c62828',
                cancelButtonColor: '#6c757d',
                confirmButtonText: @json(__('Yes, delete')),
                cancelButtonText: @json(__('Cancel')),
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.removeAttribute('data-swal-delete');
                    HTMLFormElement.prototype.submit.call(form);
                }
            });
        });
    });

    document.querySelectorAll('form[data-swal-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var title = form.getAttribute('data-swal-title') || @json(__('Are you sure?'));
            var text = form.getAttribute('data-swal-text') || '';
            var confirmText = form.getAttribute('data-swal-confirm-text') || @json(__('Yes, continue'));
            Swal.fire({
                ...swalTheme,
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#122223',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmText,
                cancelButtonText: @json(__('Cancel')),
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.removeAttribute('data-swal-confirm');
                    HTMLFormElement.prototype.submit.call(form);
                }
            });
        });
    });

    @if (session('status'))
    Swal.fire({
        ...swalTheme,
        icon: 'success',
        title: @json(__('Success')),
        text: @json(session('status')),
        timer: 3800,
        showConfirmButton: true,
    });
    @endif

    @if (session('error'))
    Swal.fire({
        ...swalTheme,
        icon: 'error',
        title: @json(__('Something went wrong')),
        text: @json(session('error')),
    });
    @endif

    @if (isset($errors) && $errors->any())
    @php
        $errHtml = '<ul style="text-align:left;margin:0;padding-left:1.25rem;">'
            . collect($errors->all())->map(fn ($m) => '<li>'.e($m).'</li>')->implode('')
            . '</ul>';
    @endphp
    Swal.fire({
        ...swalTheme,
        icon: 'error',
        title: @json(__('Please fix the errors')),
        html: {!! \Illuminate\Support\Js::from($errHtml) !!},
    });
    @endif
});
</script>
<style>
    .dash-swal-popup {
        border: 1px solid rgba(213, 172, 66, 0.18) !important;
        box-shadow: 0 18px 44px rgba(0, 0, 0, 0.28) !important;
    }
    .dash-swal-confirm {
        color: #111827 !important;
        font-weight: 700 !important;
    }
    .dash-swal-cancel {
        background: #374151 !important;
    }
</style>
