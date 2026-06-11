<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#f97316">
  <title>@yield('title', 'Menu') — Dimsum Mak'Angga</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>

<header class="topbar">

  <a href="{{ route('menu') }}" class="topbar-brand">
    <div class="brand-icon" style="font-size:22px;display:flex;align-items:center;justify-content:center;">🥟</div>
    <div>
      <div class="brand-name">Mak'Angga</div>
      <div class="brand-sub">Dim Sum</div>
    </div>
  </a>

  <div class="hidden md:flex items-center gap-1">
    @auth

    <a href="{{ route('checkout') }}"
       class="btn btn-ghost btn-sm relative"
       id="topbar-cart-btn">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
      </svg>
      <span>Keranjang</span>
      <span id="cart-count-badge"
            class="notif-dot"
            style="display:none; top:-4px; right:-4px; border-color:transparent;"></span>
    </a>

    <div x-data="{ open: false }" class="relative">
      <button @click="open = !open" @click.outside="open = false"
              class="flex items-center gap-2 btn btn-ghost btn-sm">
        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
             style="background:var(--orange-100); color:var(--orange-700);">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <span class="text-sm font-semibold text-gray-700 max-w-28 truncate leading-none">
          {{ auth()->user()->name }}
        </span>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             class="text-gray-400 transition-transform duration-150" :class="open ? 'rotate-180' : ''">
          <path d="M6 9l6 6 6-6"/>
        </svg>
      </button>

      <div x-show="open" x-cloak
           x-transition:enter="transition ease-out duration-100"
           x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
           x-transition:enter-end="opacity-100 scale-100 translate-y-0"
           class="absolute right-0 top-10 z-50 card"
           style="min-width:180px; padding:6px; box-shadow:var(--shadow-lg);">

        <a href="{{ route('orders') }}" class="sidebar-link" style="font-size:13px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 8.414V19a2 2 0 01-2 2z"/>
          </svg>
          Pesanan Saya
        </a>

        <a href="{{ route('profile.edit') }}" class="sidebar-link" style="font-size:13px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
          Profil
        </a>

        @if(auth()->user()->isAdmin())
        <hr class="sidebar-divider" style="margin:4px 0;">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link"
           style="font-size:13px; color:var(--orange-600);">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
          </svg>
          Admin Panel
        </a>
        @endif

        <hr class="sidebar-divider" style="margin:4px 0;">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="sidebar-link w-full text-left"
                  style="font-size:13px; color:var(--red-600);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Keluar
          </button>
        </form>

      </div>
    </div>

    @else

    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>

    @endauth
  </div>

  @auth
  <a href="{{ route('checkout') }}"
     class="relative btn btn-ghost btn-sm md:hidden"
     style="padding:8px;">
    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
      <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
    </svg>
    <span id="cart-count-badge-mobile"
          class="notif-dot"
          style="display:none; border-color:transparent;"></span>
  </a>
  @else
  <a href="{{ route('login') }}" class="btn btn-primary btn-sm md:hidden">Masuk</a>
  @endauth

</header>

@if(session('success'))
<div class="mx-4 mt-3 alert alert-success animate-fade-up" role="alert" x-data x-init="setTimeout(() => $el.remove(), 4000)">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
  </svg>
  {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mx-4 mt-3 alert alert-error animate-fade-up" role="alert" x-data x-init="setTimeout(() => $el.remove(), 5000)">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
  </svg>
  {{ session('error') }}
</div>
@endif

<div class="app-shell">

  <aside class="sidebar hidden md:flex flex-col">
    <div class="sidebar-inner">

      <p class="sidebar-section">Navigasi</p>

      <a href="{{ route('menu') }}"
         class="sidebar-link {{ request()->routeIs('menu') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Menu
      </a>

      @auth
      <a href="{{ route('orders') }}"
         class="sidebar-link {{ request()->routeIs('orders') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 8.414V19a2 2 0 01-2 2z"/>
        </svg>
        Pesanan Saya
      </a>

      <a href="{{ route('profile.edit') }}"
         class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
        Profil
      </a>

      @if(auth()->user()->isAdmin())
      <hr class="sidebar-divider">
      <p class="sidebar-section">Admin</p>
      <a href="{{ route('admin.dashboard') }}"
         class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
         style="{{ request()->routeIs('admin.*') ? '' : 'color:var(--orange-600);' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7" rx="1"/>
          <rect x="14" y="3" width="7" height="7" rx="1"/>
          <rect x="3" y="14" width="7" height="7" rx="1"/>
          <rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        Admin Panel
      </a>
      @endif

      <div class="flex-1"></div>

      <hr class="sidebar-divider">
      <div class="flex items-center gap-2.5 px-2 py-2">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
             style="background:var(--orange-100); color:var(--orange-700);">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="min-w-0">
          <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
          <p class="text-xs text-gray-400">
            {{ auth()->user()->isAdmin() ? 'Admin' : 'Customer' }}
          </p>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sidebar-link w-full" style="color:var(--red-500);">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Keluar
        </button>
      </form>
      @endauth

    </div>
  </aside>

  <main class="main-content">
    <div class="page-body">
      @yield('content')
    </div>
  </main>

</div>

<nav class="bottom-nav md:hidden" aria-label="Navigasi utama">

  <a href="{{ route('menu') }}"
     class="bottom-nav-item {{ request()->routeIs('menu') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Menu
  </a>

  @auth

  <a href="{{ route('checkout') }}"
     class="bottom-nav-item {{ request()->routeIs('checkout') ? 'active' : '' }}"
     style="position:relative;">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
      <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
    </svg>
    Keranjang
    <span id="bottom-cart-badge" style="display:none;position:absolute;top:2px;right:16px;background:var(--orange-500);color:white;font-size:9px;font-weight:800;min-width:16px;height:16px;border-radius:99px;display:none;align-items:center;justify-content:center;padding:0 4px;"></span>
  </a>

  <a href="{{ route('orders') }}"
     class="bottom-nav-item {{ request()->routeIs('orders') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 8.414V19a2 2 0 01-2 2z"/>
    </svg>
    Pesanan
  </a>

  <a href="{{ route('profile.edit') }}"
     class="bottom-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
    </svg>
    Profil
  </a>

  @else

  <a href="{{ route('login') }}" class="bottom-nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
    </svg>
    Masuk
  </a>

  <a href="{{ route('register') }}" class="bottom-nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
      <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
    </svg>
    Daftar
  </a>

  @endauth

</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var lastCount = -1;
  function syncBadges(count) {
    if (count === lastCount) return;
    lastCount = count;
    var ids = ['cart-count-badge', 'cart-count-badge-mobile'];
    ids.forEach(function(id) {
      var el = document.getElementById(id);
      if (!el) return;
      if (count > 0) {
        el.textContent = count > 9 ? '9+' : count;
        el.style.display = 'flex';
      } else {
        el.style.display = 'none';
      }
    });
    var bot = document.getElementById('bottom-cart-badge');
    if (bot) {
      if (count > 0) {
        bot.textContent = count > 9 ? '9+' : count;
        bot.style.display = 'flex';
      } else {
        bot.style.display = 'none';
      }
    }
  }
  setInterval(function () {
    syncBadges(window.__cartCount || 0);
  }, 300);
});
</script>

</body>
</html>