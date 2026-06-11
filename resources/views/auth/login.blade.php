@extends('layouts.guest')

@section('content')

<h2 class="text-center font-black mb-1" style="color:#fbbf24;font-size:20px;">Masuk</h2>
<p class="text-center text-sm mb-6" style="color:rgba(254,243,199,.5);">Silakan login untuk mulai memesan</p>

<x-auth-session-status class="mb-4" :status="session('status')" />

<form method="POST" action="{{ route('login') }}" novalidate>
@csrf

    <div class="mb-4">
        <label class="block text-sm font-semibold mb-1.5" style="color:#fef3c7;" for="email">
            Email
        </label>
        <input type="email" name="email" id="email"
               value="{{ old('email') }}"
               required autofocus autocomplete="username"
               class="w-full px-4 py-3 rounded-xl text-sm outline-none transition"
               style="background:rgba(255,255,255,.07);border:1px solid {{ $errors->has('email') ? '#f87171' : 'rgba(255,180,60,.2)' }};color:#fef3c7;font-family:inherit;"
               placeholder="nama@email.com">
        @error('email')
            <p class="text-xs mt-1.5" style="color:#fca5a5;">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <div class="flex items-center justify-between mb-1.5">
            <label class="text-sm font-semibold" style="color:#fef3c7;" for="password">Password</label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="color:#f97316;font-size:12px;" class="hover:underline">
                    Lupa password?
                </a>
            @endif
        </div>
        <input type="password" name="password" id="password"
               required autocomplete="current-password"
               class="w-full px-4 py-3 rounded-xl text-sm outline-none transition"
               style="background:rgba(255,255,255,.07);border:1px solid {{ $errors->has('password') ? '#f87171' : 'rgba(255,180,60,.2)' }};color:#fef3c7;font-family:inherit;"
               placeholder="••••••••">
        @error('password')
            <p class="text-xs mt-1.5" style="color:#fca5a5;">{{ $message }}</p>
        @enderror
    </div>

    <label class="flex items-center gap-2.5 mb-5 cursor-pointer" style="color:rgba(254,243,199,.7);font-size:13px;">
        <input type="checkbox" name="remember" class="rounded"
               style="accent-color:#f97316;">
        Ingat saya
    </label>

    <button type="submit"
            class="w-full py-3 rounded-xl font-black text-sm text-white transition hover:opacity-90 active:scale-[.98]"
            style="background:linear-gradient(135deg,#f97316,#ea580c);box-shadow:0 8px 20px rgba(249,115,22,.3);">
        Masuk
    </button>

    <p class="text-center text-sm mt-5" style="color:rgba(254,243,199,.5);">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold hover:underline" style="color:#f97316;">
            Daftar gratis
        </a>
    </p>

</form>

@endsection