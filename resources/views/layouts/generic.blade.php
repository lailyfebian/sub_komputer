<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>" type="text/css">

    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon.ico') }}">
    <link rel="icon" sizes="192x192" href="{{ asset('images/favicon.ico') }}">
    <meta name="msapplication-TileImage" content="{{ asset('images/favicon.ico') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">

    <meta name="google-site-verification" content="KD2PryStGCXzHM3hmQStdIQDQeQedKNhA0P5-w0jy1U" />
</head>

<body>
    <div id="app">
        <nav>
            @include('layouts.parts.header')
        </nav>
        <main class="pb-4">
            @yield('content')
        </main>

        @if ($page->title ?? '')
            <div class="button-wa">
                <a href="https://wa.privatnusantara.com/?kebutuhan={{ $page->title ?? '' }}" rel="nofollow"
                    aria-label="Tautan Hubungi Admin" class="text-decoration-none text-dark button-cta">
                    Konsultasi Sekarang
                    <img src="{{ asset('images/logo-wa.webp') }}" alt="Logo WhatsApp">
                </a>
            </div>
        @else
            <div class="button-wa">
                <a href="https://wa.privatnusantara.com/" rel="nofollow" aria-label="Tautan Hubungi Admin"
                    class="text-decoration-none text-dark button-cta">
                    Konsultasi Sekarang
                    <img src="{{ asset('images/logo-wa.webp') }}" alt="Logo WhatsApp">
                </a>
            </div>
        @endif

        <footer>
            @include('layouts.parts.footer')
        </footer>
    </div>
    @stack('scripts')
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous" defer>
</script>

</html>
