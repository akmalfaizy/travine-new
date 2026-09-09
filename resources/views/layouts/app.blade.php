<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="travine.id">
    <meta name="keywords" content="travine, wisata, tourism, travel, konsultasi wisata, ekosistem wisata, destinasi, Indonesia">
    <meta name="author" content="travine.id">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>travine.id</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon_travine.svg') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Early Theme Detection --}}
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    {{-- Main CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.partner_float')

    {{-- Main JS --}}

    {{-- Main JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
