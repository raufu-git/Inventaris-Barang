<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(url) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: 'Apakah kamu yakin ingin menghapus data ini?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            // Buat form dinamis untuk delete
            const form = document.createElement('form');
            form.action = url;
            form.method = 'POST';

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    timer: 500,
    showConfirmButton: false
});
</script>
@endif

@if(session('deleted'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Data Terhapus',
    text: '{{ session('deleted') }}',
    timer: 500,
    showConfirmButton: false
});
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: '{{ session('error') }}',
});
</script>
@endif
