@extends('layouts.guest')

@section('content')

<h2 class="text-center font-black mb-1" style="color:#fbbf24;font-size:20px;">Buat Akun</h2>
<p class="text-center text-sm mb-6" style="color:rgba(254,243,199,.5);">Daftar untuk mulai memesan dimsum</p>

<form method="POST" action="{{ route('register') }}" novalidate>
@csrf

    <div class="mb-4">
        <label class="block text-sm font-semibold mb-1.5" style="color:#fef3c7;" for="name">Nama Lengkap</label>
        <input type="text" name="name" id="name"
               value="{{ old('name') }}"
               required autocomplete="name"
               class="w-full px-4 py-3 rounded-xl text-sm outline-none transition"
               style="background:rgba(255,255,255,.07);border:1px solid {{ $errors->has('name') ? '#f87171' : 'rgba(255,180,60,.2)' }};color:#fef3c7;font-family:inherit;"
               placeholder="Nama kamu">
        @error('name')
            <p class="text-xs mt-1.5" style="color:#fca5a5;">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-semibold mb-1.5" style="color:#fef3c7;" for="email">Email</label>
        <input type="email" name="email" id="email"
               value="{{ old('email') }}"
               required autocomplete="username"
               class="w-full px-4 py-3 rounded-xl text-sm outline-none transition"
               style="background:rgba(255,255,255,.07);border:1px solid {{ $errors->has('email') ? '#f87171' : 'rgba(255,180,60,.2)' }};color:#fef3c7;font-family:inherit;"
               placeholder="nama@email.com">
        @error('email')
            <p class="text-xs mt-1.5" style="color:#fca5a5;">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-semibold mb-1.5" style="color:#fef3c7;" for="password">Password</label>
        <input type="password" name="password" id="password"
               required autocomplete="new-password"
               class="w-full px-4 py-3 rounded-xl text-sm outline-none transition"
               style="background:rgba(255,255,255,.07);border:1px solid {{ $errors->has('password') ? '#f87171' : 'rgba(255,180,60,.2)' }};color:#fef3c7;font-family:inherit;"
               placeholder="Minimal 8 karakter">
        @error('password')
            <p class="text-xs mt-1.5" style="color:#fca5a5;">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-5">
        <label class="block text-sm font-semibold mb-1.5" style="color:#fef3c7;" for="password_confirmation">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               required autocomplete="new-password"
               class="w-full px-4 py-3 rounded-xl text-sm outline-none transition"
               style="background:rgba(255,255,255,.07);border:1px solid rgba(255,180,60,.2);color:#fef3c7;font-family:inherit;"
               placeholder="Ulangi password">
    </div>

    <button type="submit"
            class="w-full py-3 rounded-xl font-black text-sm text-white transition hover:opacity-90 active:scale-[.98]"
            style="background:linear-gradient(135deg,#f97316,#ea580c);box-shadow:0 8px 20px rgba(249,115,22,.3);">
        Buat Akun
    </button>

    <p class="text-center text-sm mt-5" style="color:rgba(254,243,199,.5);">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-bold hover:underline" style="color:#f97316;">
            Masuk
        </a>
    </p>

</form>

@endsection