<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — Admin · Mak'Angga</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body style="background: var(--gray-50);">

{{-- ════════════════════════════════════════
     TOPBAR
════════════════════════════════════════ --}}
<header class="topbar">

  <div class="flex items-center gap-3">
    {{-- Mobile menu --}}
    <button id="mobileMenuBtn" class="btn btn-ghost btn-sm md:hidden" style="padding:8px;" aria-label="Menu">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>

    <a href="{{ route('admin.dashboard') }}" class="topbar-brand">
      <img src="{{ asset('images/logo.jpeg') }}" alt="Dimsum Mak'Angga" style="width:40px;height:40px;object-fit:contain;border-radius:50%;">
      <div>
        <div class="brand-name">Mak'Angga</div>
        <div class="brand-sub" style="color: var(--orange-600); font-weight:700;">ADMIN</div>
      </div>
    </a>
  </div>

  <div class="flex items-center gap-2">

    {{-- Breadcrumb / page title on desktop --}}
    <span class="hidden md:inline text-sm font-semibold text-gray-500">
      @yield('title', 'Dashboard')
    </span>

    {{-- Notification --}}
    <div x-data="notifSystem()" x-init="init()" class="relative">
      <button @click="toggle()" class="btn btn-ghost btn-sm relative" style="padding:8px;" aria-label="Notifikasi">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
        </svg>
        <span x-show="count > 0" x-cloak class="notif-dot" x-text="count > 9 ? '9+' : count"></span>
      </button>

      {{-- Notif dropdown --}}
      <div x-show="open" x-cloak
           x-transition:enter="transition ease-out duration-100"
           x-transition:enter-start="opacity-0 scale-95 translate-y-1"
           x-transition:enter-end="opacity-100 scale-100 translate-y-0"
           @click.outside="open = false"
           class="absolute right-0 top-11 w-72 card z-50"
           style="box-shadow: var(--shadow-lg); padding:0;">

        <div class="flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid var(--border);">
          <span class="text-sm font-bold text-gray-800">Notifikasi</span>
          <span x-text="count + ' baru'" class="badge badge-orange"></span>
        </div>

        <template x-if="notifications.length === 0">
          <div class="empty-state" style="padding: 24px;">
            <p class="text-sm text-gray-400">Belum ada notifikasi</p>
          </div>
        </template>

        <template x-for="n in notifications" :key="n.id">
          <button @click="goToOrder(n.id)"
                  class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition"
                  style="border-bottom: 1px solid var(--border);">
            <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-sm flex-shrink-0">🥟</div>
            <div class="min-w-0">
              <p class="text-sm font-semibold text-gray-800">Pesanan #<span x-text="n.id"></span></p>
              <p class="text-xs text-gray-400">Rp <span x-text="Number(n.total).toLocaleString('id-ID')"></span></p>
            </div>
          </button>
        </template>
      </div>
    </div>

    {{-- User --}}
    <div class="hidden md:flex items-center gap-2 pl-2" style="border-left: 1px solid var(--border);">
      <div class="w-7 h-7 rounded-full bg-orange-100 flex items-center justify-center text-orange-700 text-xs font-bold">
        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
      </div>
      <span class="text-sm font-semibold text-gray-700 max-w-[120px] truncate">
        {{ auth()->user()->name ?? 'Admin' }}
      </span>
    </div>

  </div>
</header>

{{-- ════════════════════════════════════════
     MOBILE OVERLAY
════════════════════════════════════════ --}}
<div id="mobileOverlay"
     class="fixed inset-0 z-40 bg-black/40 hidden md:hidden"
     onclick="closeMobileMenu()"></div>

{{-- ════════════════════════════════════════
     LAYOUT SHELL
════════════════════════════════════════ --}}
<div class="app-shell">

  {{-- SIDEBAR --}}
  <aside id="adminSidebar"
         class="sidebar hidden md:flex flex-col
                fixed md:static inset-y-0 left-0 z-41 w-60"
         style="top: var(--topbar-h); height: calc(100dvh - var(--topbar-h));">

    <div class="sidebar-inner">

      <p class="sidebar-section">Overview</p>

      <a href="{{ route('admin.dashboard') }}"
         class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
          <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
          <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        Dashboard
      </a>

      <p class="sidebar-section">Kelola</p>

      <a href="{{ route('admin.orders.index') }}"
         class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
          <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 8.414V19a2 2 0 01-2 2z"/>
        </svg>
        Pesanan
        @php $pendingPayment = \App\Models\Order::where('payment_status','uploaded')->count(); @endphp
        @if($pendingPayment > 0)
        <span class="ml-auto badge badge-orange" style="font-size:10px; padding: 2px 6px;">{{ $pendingPayment }}</span>
        @endif
      </a>

      <a href="{{ route('admin.products.index') }}"
         class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
          <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
          <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
        </svg>
        Produk
      </a>

      <a href="{{ route('admin.rekapitulasi.index') }}"
         class="sidebar-link {{ request()->routeIs('admin.rekapitulasi.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
          <line x1="18" y1="20" x2="18" y2="10"/>
          <line x1="12" y1="20" x2="12" y2="4"/>
          <line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
        Rekapitulasi
      </a>

      <hr class="sidebar-divider">

      <a href="{{ route('menu') }}" class="sidebar-link" target="_blank">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
          <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        Lihat Toko
      </a>

      {{-- Spacer --}}
      <div class="flex-1"></div>

      {{-- Logout --}}
      <hr class="sidebar-divider">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sidebar-link w-full" style="color: var(--red-500);">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Keluar
        </button>
      </form>

    </div>
  </aside>

  {{-- MAIN --}}
  <main class="main-content">

    {{-- Flash --}}
    @if(session('success'))
    <div class="mx-4 mt-3 alert alert-success animate-fade-up">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-4 mt-3 alert alert-error animate-fade-up">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      {{ session('error') }}
    </div>
    @endif

    <div class="page-body">
      @yield('content')
    </div>

  </main>

</div>

{{-- ════════════════════════════════════════
     BOTTOM NAV ADMIN (MOBILE)
════════════════════════════════════════ --}}
<nav class="bottom-nav md:hidden">
  <a href="{{ route('admin.dashboard') }}"
     class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
      <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
    </svg>
    Dashboard
  </a>
  <a href="{{ route('admin.orders.index') }}"
     class="bottom-nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 8.414V19a2 2 0 01-2 2z"/>
    </svg>
    Pesanan
  </a>
  <a href="{{ route('admin.products.index') }}"
     class="bottom-nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
      <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
    </svg>
    Produk
  </a>
  <a href="{{ route('admin.rekapitulasi.index') }}"
     class="bottom-nav-item {{ request()->routeIs('admin.rekapitulasi.*') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="18" y1="20" x2="18" y2="10"/>
      <line x1="12" y1="20" x2="12" y2="4"/>
      <line x1="6" y1="20" x2="6" y2="14"/>
    </svg>
    Rekap
  </a>
</nav>

{{-- Toast container --}}
<div id="toastContainer" style="position:fixed; top:16px; right:16px; z-index:999; display:flex; flex-direction:column; gap:8px;"></div>

<script>
// Mobile sidebar
function openMobileMenu() {
  const s = document.getElementById('adminSidebar');
  const o = document.getElementById('mobileOverlay');
  s.classList.remove('hidden');
  s.classList.add('flex');
  o.classList.remove('hidden');
}
function closeMobileMenu() {
  const s = document.getElementById('adminSidebar');
  const o = document.getElementById('mobileOverlay');
  s.classList.add('hidden');
  s.classList.remove('flex');
  o.classList.add('hidden');
}
const menuBtn = document.getElementById('mobileMenuBtn');
if (menuBtn) menuBtn.addEventListener('click', openMobileMenu);

// Toast
function showToast(msg, type = 'default') {
  const t = document.createElement('div');
  t.className = 'toast' + (type !== 'default' ? ' toast-' + type : '');
  t.textContent = msg;
  document.getElementById('toastContainer').appendChild(t);
  setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; }, 2800);
  setTimeout(() => t.remove(), 3200);
}

// Notification system
function notifSystem() {
  return {
    open: false, notifications: [], count: 0,
    toggle() { this.open = !this.open; if (this.open) this.count = 0; },
    init() {
      const iv = setInterval(() => { if (window.Echo) { clearInterval(iv); this.listen(); } }, 300);
    },
    listen() {
      window.Echo.channel('orders').listen('.order.created', (e) => {
        if (!e.order) return;
        this.notifications.unshift({ id: e.order.id, total: e.order.total_price });
        this.count++;
        try { new Audio('/notif.mp3').play().catch(()=>{}); } catch {}
        showToast('Pesanan baru #' + e.order.id, 'warning');
      });
    },
    goToOrder(id) { window.location.href = '/admin/orders/' + id; }
  };
}
</script>

</body>
</html>
