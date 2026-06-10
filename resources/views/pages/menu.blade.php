@extends('layouts.app')

@section('title', 'Menu')

@section('content')

<div x-data="cartApp()" x-init="init()">

  {{-- Page header --}}
  <div class="page-header">
    <div>
      <h1 class="page-title">Menu Kami</h1>
      <p class="page-subtitle">{{ $products->count() }} item tersedia</p>
    </div>
  </div>

  <div class="flex flex-col lg:flex-row gap-5">

    {{-- ════════════════════════════════
         PRODUCT GRID
    ════════════════════════════════ --}}
    <div class="flex-1 min-w-0">

      {{-- Cart error (auth only) --}}
      @auth
      <div x-show="cartError" x-cloak class="alert alert-error mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        <span x-text="cartError"></span>
      </div>
      @endauth

      {{-- Grid --}}
      <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">

        @forelse($products as $p)
        @php $available = $p->is_available && $p->stock > 0; @endphp

        <div class="product-card {{ !$available ? 'unavailable' : '' }}">

          {{-- Image / emoji area --}}
          <div class="product-img">
            <span role="img" aria-label="Dim Sum">🥟</span>

            @if(!$available)
              <div class="absolute inset-0 flex items-center justify-center"
                   style="background:rgba(15,23,42,.55);">
                <span class="badge badge-red" style="font-size:10px;">HABIS</span>
              </div>
            @elseif($p->stock <= 5)
              <span class="badge badge-orange absolute top-2 right-2"
                    style="font-size:10px;">
                Sisa {{ $p->stock }}
              </span>
            @endif
          </div>

          {{-- Info --}}
          <div class="p-3 md:p-4">
            <h3 class="font-bold text-sm text-gray-900 leading-tight truncate mb-1">
              {{ $p->name }}
            </h3>

            <p class="text-xs text-gray-400 mb-3 truncate-2" style="min-height:2.4em;">
              {{ $p->description ?: "Dimsum spesial pilihan Mak'Angga" }}
            </p>

            <div class="flex items-center justify-between gap-2">
              <span class="font-extrabold text-sm leading-none"
                    style="color:var(--orange-600);">
                Rp&nbsp;{{ number_format($p->price, 0, ',', '.') }}
              </span>

              @if($available)
              {{-- Tombol + tampil untuk SEMUA user (termasuk guest) --}}
              {{-- Guest: klik → modal login | Auth: langsung tambah ke keranjang --}}
              <button
                @click="add({{ $p->id }})"
                :disabled="adding === {{ $p->id }}"
                class="btn btn-primary btn-sm flex-shrink-0"
                style="padding:7px 10px; min-width:34px; border-radius:8px;">
                <template x-if="adding !== {{ $p->id }}">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                  </svg>
                </template>
                <template x-if="adding === {{ $p->id }}">
                  <svg class="animate-spin" width="14" height="14" viewBox="0 0 24 24"
                       fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" opacity=".2"/>
                    <path d="M21 12a9 9 0 01-9 9"/>
                  </svg>
                </template>
              </button>
              @else
              <span class="text-xs text-gray-300 font-semibold">Habis</span>
              @endif
            </div>
          </div>

        </div>

        @empty
        <div class="col-span-2 md:col-span-3 empty-state">
          <div class="empty-icon" style="background:var(--orange-50); font-size:26px;">🥟</div>
          <p class="empty-title">Menu belum tersedia</p>
          <p class="empty-desc">Tunggu update menu dari kami ya!</p>
        </div>
        @endforelse

      </div>
    </div>

    {{-- ════════════════════════════════
         CART PANEL — desktop sticky
    ════════════════════════════════ --}}
    <div class="hidden lg:block w-72 xl:w-80 flex-shrink-0">
      <div class="cart-panel">

        {{-- Header --}}
        <div class="cart-panel-header">
          <div class="flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" class="text-gray-400">
              <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
              <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
            </svg>
            <span class="text-sm font-bold text-gray-900">Keranjang</span>
          </div>
          @auth
          <span x-show="items.length > 0" x-cloak
                class="badge badge-orange text-xs"
                x-text="items.length + ' item'"></span>
          @endauth
        </div>

        {{-- Body --}}
        <div class="cart-panel-body">

          @guest
          <div class="text-center py-4">
            <div class="empty-icon mx-auto mb-3"
                 style="width:44px;height:44px;font-size:20px;background:var(--orange-50);">
              🛒
            </div>
            <p class="text-sm text-gray-500 mb-4">Pilih menu favoritmu, lalu masuk untuk memesan</p>
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm btn-block mb-2">
              Masuk
            </a>
            <a href="{{ route('register') }}" class="btn btn-secondary btn-sm btn-block">
              Daftar Gratis
            </a>
          </div>
          @endguest

          @auth

          {{-- Error --}}
          <div x-show="cartError" x-cloak
               class="alert alert-error mb-3" style="padding:8px 12px; font-size:12px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 style="width:13px;height:13px;flex-shrink:0;">
              <circle cx="12" cy="12" r="10"/>
              <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            <span x-text="cartError"></span>
          </div>

          {{-- Empty --}}
          <template x-if="items.length === 0">
            <div class="text-center py-6">
              <p class="text-sm text-gray-400">Keranjang kosong</p>
            </div>
          </template>

          {{-- Items --}}
          <template x-for="item in items" :key="item.id">
            <div class="flex items-center gap-2 py-2.5"
                 style="border-bottom:1px solid var(--border);">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate"
                   x-text="item.name"></p>
                <p class="text-xs font-bold mt-0.5"
                   style="color:var(--orange-600);"
                   x-text="'Rp ' + item.price.toLocaleString('id-ID')"></p>
              </div>
              <div class="flex items-center gap-1 flex-shrink-0">
                <button @click="update(item.id, 'minus')"
                        class="w-7 h-7 rounded-lg flex items-center justify-center
                               text-sm font-bold transition-colors"
                        style="background:var(--gray-100); color:var(--gray-600);"
                        onmouseover="this.style.background='var(--gray-200)'"
                        onmouseout="this.style.background='var(--gray-100)'">−</button>
                <span class="w-6 text-center text-sm font-extrabold text-gray-900"
                      x-text="item.qty"></span>
                <button @click="update(item.id, 'plus')"
                        class="w-7 h-7 rounded-lg flex items-center justify-center
                               text-sm font-bold transition-colors"
                        style="background:var(--orange-500); color:white;"
                        onmouseover="this.style.background='var(--orange-600)'"
                        onmouseout="this.style.background='var(--orange-500)'">+</button>
              </div>
            </div>
          </template>

          {{-- Footer --}}
          <template x-if="items.length > 0">
            <div class="pt-3 mt-1">
              <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-semibold text-gray-600">Total</span>
                <span class="font-extrabold text-base"
                      style="color:var(--orange-600);"
                      x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
              </div>
              <a href="{{ route('checkout') }}"
                 class="btn btn-primary btn-block"
                 style="border-radius:10px; padding:12px 16px; font-size:14px;">
                Checkout
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5">
                  <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
              </a>
              <button @click="clearCart()"
                      class="btn btn-ghost btn-sm btn-block mt-2"
                      style="color:var(--gray-400); font-size:12px;">
                Kosongkan keranjang
              </button>
            </div>
          </template>

          @endauth
        </div>
      </div>
    </div>

  </div>

</div>

{{-- MODAL: guest prompt — muncul saat guest klik tombol + --}}
<div id="guestModal"
     class="modal-backdrop hidden"
     onclick="if(event.target===this)closeGuestModal()">
  <div class="modal text-center">
    <div style="font-size:40px; margin-bottom:12px;">🛒</div>
    <h3 class="text-base font-extrabold text-gray-900 mb-1">Mau pesan?</h3>
    <p class="text-sm text-gray-500 mb-5">
      Masuk dulu untuk menambahkan item ke keranjang.
    </p>
    <a href="{{ route('login') }}" class="btn btn-primary btn-lg btn-block mb-2">
      Masuk Sekarang
    </a>
    <a href="{{ route('register') }}" class="btn btn-secondary btn-block mb-4">
      Daftar Gratis
    </a>
    <button onclick="closeGuestModal()"
            class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
      Tutup
    </button>
  </div>
</div>

<script>
const IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};

function closeGuestModal() {
    const modal = document.getElementById('guestModal');
    if (modal) modal.classList.add('hidden');
}

function cartApp() {
  return {
    cart: {}, adding: null, cartError: null,

    async init() {
      if (!IS_AUTH) return;
      try {
        const r = await fetch('/cart');
        if (r.ok) this.cart = await r.json();
      } catch(e) {}
      this.$watch('cart', (val) => {
        const count = Object.values(val).reduce((s,i) => s + (i.qty||0), 0);
        window.__cartCount = count;
      });
    },

    get items() {
      return Object.entries(this.cart).map(([id, item]) => ({
        ...item, id: Number(id)
      }));
    },

    get total() {
      return this.items.reduce((t, i) => t + i.qty * i.price, 0);
    },

    async add(id) {
      // Guest: tampilkan modal login
      if (!IS_AUTH) {
        document.getElementById('guestModal').classList.remove('hidden');
        return;
      }
      this.adding = id;
      this.cartError = null;
      try {
        const r = await fetch('/cart/add', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ id })
        });
        if (r.status === 401) { window.location.href = '{{ route("login") }}'; return; }
        if (!r.ok) {
          const d = await r.json();
          this.cartError = d.error || 'Gagal menambahkan item';
          setTimeout(() => this.cartError = null, 3000);
          return;
        }
        this.cart = await r.json();
      } catch(e) {}
      finally { this.adding = null; }
    },

    async update(id, action) {
      if (!IS_AUTH) return;
      try {
        const r = await fetch('/cart/update', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ id, action })
        });
        if (r.ok) this.cart = await r.json();
      } catch(e) {}
    },

    async clearCart() {
      if (!IS_AUTH) return;
      try {
        const r = await fetch('/cart/clear', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        if (r.ok) this.cart = await r.json();
      } catch(e) {}
    }
  };
}
</script>

@endsection
