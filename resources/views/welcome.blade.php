<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('bootstrap/font/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/ikon.png?v=3') }}">


</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    @include('welcome-partials.navbar')
    @include('welcome-partials.section-hero')
    @include('welcome-partials.section-fitur')
    @include('welcome-partials.footer')

    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>