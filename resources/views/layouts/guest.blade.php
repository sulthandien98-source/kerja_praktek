<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="Dimsum Mak'Angga - Pemesanan Dimsum Online">
<meta name="theme-color" content="#f97316">
<title>{{ config('app.name', "Dimsum Mak'Angga") }}</title>
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:linear-gradient(135deg,#020617 0%,#0f172a 50%,#1e293b 100%);min-height:100vh;" class="font-sans antialiased">

<div class="min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">

        <div class="text-center mb-6">
            <a href="{{ route('menu') }}" class="inline-block">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Dimsum Mak'Angga" style="width:140px;height:140px;object-fit:contain;border-radius:50%;margin:0 auto 8px;display:block;">
            </a>
        </div>

        <div style="background:rgba(15,23,42,.95);border:1px solid rgba(249,115,22,.2);border-radius:24px;padding:28px 32px;box-shadow:0 25px 50px rgba(0,0,0,.4);backdrop-filter:blur(12px);">
            @yield('content')
        </div>

        <p style="text-align:center;margin-top:20px;color:rgba(254,243,199,.35);font-size:12px;">
            &copy; {{ date('Y') }} Dimsum Mak'Angga
        </p>

    </div>
</div>

</body>
</html>