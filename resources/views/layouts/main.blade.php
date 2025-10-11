<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($titlePage ? $titlePage . ' - ' : '') . config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('bootstrap/font/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logoku.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-vh-100 bg-light pb-2">
        @include('layouts.navigation')
        
        @if ($titlePage)
            <header class="bg-white shadow-sm">
                <div class="container py-4">
                    <h2 class="h5 mb-0">
                        {{ $titlePage }}
                    </h2>
                </div>
            </header>
        @endif
        <main class="container my-4">
            <div class="my-5">
                {{ $slot }}
            </div>
        </main>
    </div>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <x-modal-delete />
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-url');
            const deleteForm = deleteModal.querySelector('form')
            deleteForm.setAttribute('action', url)
        }); 
        
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

    @if(session('error'))
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('error') }}',
    });
    </script>
    @endif

</body>
</html>