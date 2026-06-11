<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dimsum Mak'Angga – Dimsum enak, segar, dan halal. Pesan sekarang!">
    <title>Dimsum Mak'Angga</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#292524 100%);min-height:100vh;font-family:'Plus Jakarta Sans',sans-serif;">

<div class="min-h-screen flex flex-col items-center justify-center px-4 py-12 text-white">

    <div class="text-center max-w-lg w-full">

        <div style="font-size:64px;line-height:1;margin-bottom:16px;">🥟</div>

        <h1 style="font-size:clamp(28px,6vw,48px);font-weight:900;letter-spacing:-1px;margin-bottom:8px;background:linear-gradient(90deg,#fb923c,#fbbf24);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
            Dimsum Mak'Angga
        </h1>

        <p style="color:rgba(255,255,255,.65);font-size:16px;margin-bottom:10px;">
            Fresh &bull; Homemade &bull; Halal
        </p>

        <p style="color:rgba(255,255,255,.45);font-size:13px;margin-bottom:36px;">
            Dimsum spesial dari dapur Mak'Angga. Enak, segar, dan terjangkau.
        </p>

        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-bottom:48px;">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       style="background:linear-gradient(135deg,#f97316,#ea580c);color:white;padding:14px 28px;border-radius:14px;font-weight:700;font-size:15px;text-decoration:none;box-shadow:0 8px 24px rgba(249,115,22,.35);">
                        👑 Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('menu') }}"
                       style="background:linear-gradient(135deg,#f97316,#ea580c);color:white;padding:14px 28px;border-radius:14px;font-weight:700;font-size:15px;text-decoration:none;box-shadow:0 8px 24px rgba(249,115,22,.35);">
                        🍽️ Pesan Sekarang
                    </a>
                    <a href="{{ route('orders') }}"
                       style="background:rgba(255,255,255,.1);color:white;padding:14px 28px;border-radius:14px;font-weight:600;font-size:15px;text-decoration:none;border:1px solid rgba(255,255,255,.2);">
                        📦 Pesanan Saya
                    </a>
                @endif
            @else
                <a href="{{ route('menu') }}"
                   style="background:linear-gradient(135deg,#f97316,#ea580c);color:white;padding:14px 28px;border-radius:14px;font-weight:700;font-size:15px;text-decoration:none;box-shadow:0 8px 24px rgba(249,115,22,.35);">
                    🍽️ Lihat Menu
                </a>
                <a href="{{ route('login') }}"
                   style="background:rgba(255,255,255,.1);color:white;padding:14px 28px;border-radius:14px;font-weight:600;font-size:15px;text-decoration:none;border:1px solid rgba(255,255,255,.2);">
                    🔐 Masuk
                </a>
                <a href="{{ route('register') }}"
                   style="background:rgba(255,255,255,.07);color:rgba(255,255,255,.7);padding:14px 28px;border-radius:14px;font-weight:600;font-size:15px;text-decoration:none;border:1px solid rgba(255,255,255,.12);">
                    ✍️ Daftar Gratis
                </a>
            @endauth
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;max-width:420px;margin:0 auto;">
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:16px 12px;text-align:center;">
                <div style="font-size:24px;margin-bottom:6px;">🥟</div>
                <p style="font-size:12px;font-weight:700;color:rgba(255,255,255,.8);">Dimsum Segar</p>
                <p style="font-size:11px;color:rgba(255,255,255,.4);margin-top:2px;">Dibuat harian</p>
            </div>
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:16px 12px;text-align:center;">
                <div style="font-size:24px;margin-bottom:6px;">🚀</div>
                <p style="font-size:12px;font-weight:700;color:rgba(255,255,255,.8);">Order Mudah</p>
                <p style="font-size:11px;color:rgba(255,255,255,.4);margin-top:2px;">Langsung online</p>
            </div>
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:16px 12px;text-align:center;">
                <div style="font-size:24px;margin-bottom:6px;">✅</div>
                <p style="font-size:12px;font-weight:700;color:rgba(255,255,255,.8);">Halal & Higienis</p>
                <p style="font-size:11px;color:rgba(255,255,255,.4);margin-top:2px;">Terjamin kualitas</p>
            </div>
        </div>

    </div>

</div>

</body>
</html>