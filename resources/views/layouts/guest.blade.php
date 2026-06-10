<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>

<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">

<meta name="description"
      content="Dimsum Mak'Angga - Pemesanan Dimsum Online">

<meta name="theme-color"
      content="#f97316">

<title>{{ config('app.name', "Dimsum Mak'Angga") }}</title>

<link rel="icon" href="{{ asset('favicon.ico') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet">

@vite([
    'resources/css/app.css',
    'resources/js/app.js'
])

</head>

<body
style="
background:
linear-gradient(
135deg,
#020617 0%,
#0f172a 50%,
#1e293b 100%
);
min-height:100vh;
"
class="font-sans antialiased">

<div class="min-h-screen flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-md">

        <div class="text-center mb-6">

            <a href="{{ route('menu') }}">

                <div style="font-size:52px;">
                    🥟
                </div>

                <h1
                    style="
                    color:#fbbf24;
                    font-size:28px;
                    font-weight:800;
                    margin-top:8px;
                    ">
                    Dimsum Mak'Angga
                </h1>

                <p
                    style="
                    color:rgba(254,243,199,.65);
                    font-size:14px;
                    ">
                    Fresh • Homemade • Halal
                </p>

            </a>

        </div>

        <div
            style="
            background:rgba(15,23,42,.95);
            border:1px solid rgba(249,115,22,.2);
            border-radius:24px;
            padding:32px;
            box-shadow:0 25px 50px rgba(0,0,0,.35);
            backdrop-filter:blur(12px);
            "
        >

            @yield('content')

        </div>

        <p
            style="
            text-align:center;
            margin-top:20px;
            color:rgba(254,243,199,.45);
            font-size:12px;
            "
        >
            © {{ date('Y') }} Dimsum Mak'Angga
        </p>

    </div>

</div>

</body>
</html>