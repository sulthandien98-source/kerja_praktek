@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')

@php $total = collect(session('cart', []))->sum(fn($i) => $i['price'] * $i['qty']); @endphp

<div class="max-w-lg mx-auto" x-data="{ loading: false, copied: false }">

  <div class="page-header">
    <div>
      <h1 class="page-title">Pembayaran</h1>
      <p class="page-subtitle">Transfer dan konfirmasi pesanan</p>
    </div>
  </div>

  {{-- Step indicator --}}
  <div class="flex items-center gap-2 mb-5">
    <div class="flex items-center gap-1.5">
      <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white"
           style="background:var(--orange-500);">1</div>
      <span class="text-xs font-semibold text-gray-700">Transfer</span>
    </div>
    <div class="flex-1 h-px" style="background:var(--border);"></div>
    <div class="flex items-center gap-1.5">
      <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
           style="background:var(--gray-100); color:var(--gray-400);">2</div>
      <span class="text-xs font-semibold text-gray-400">Konfirmasi</span>
    </div>
    <div class="flex-1 h-px" style="background:var(--border);"></div>
    <div class="flex items-center gap-1.5">
      <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
           style="background:var(--gray-100); color:var(--gray-400);">3</div>
      <span class="text-xs font-semibold text-gray-400">Upload Bukti</span>
    </div>
  </div>

  {{-- Bank info — tap-to-copy on mobile --}}
  <div class="card card-p mb-4"
       style="border-color:var(--orange-200); background:var(--orange-50);">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white flex-shrink-0"
           style="background:var(--orange-500); font-size:18px;">🏦</div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-gray-900">Transfer BCA</p>
        <p class="text-xs text-gray-500">Bayar tepat nominal</p>
      </div>
      <span class="badge badge-orange flex-shrink-0">Aktif</span>
    </div>

    <div class="rounded-xl p-4 space-y-3" style="background:white; border:1px solid var(--border);">

      {{-- No. Rekening with copy --}}
      <div class="flex items-center justify-between gap-2">
        <div>
          <p class="text-xs text-gray-400 mb-0.5">No. Rekening</p>
          <p class="font-extrabold text-gray-900 tracking-widest"
             style="font-size:16px; font-family:monospace;"
             id="norek">0711982697</p>
        </div>
        <button
          onclick="copyNorek()"
          class="btn btn-secondary btn-sm flex-shrink-0"
          id="copyBtn"
          aria-label="Salin nomor rekening">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2">
            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
          </svg>
          <span id="copyLabel">Salin</span>
        </button>
      </div>

      <div class="flex justify-between items-center">
        <div>
          <p class="text-xs text-gray-400 mb-0.5">Atas Nama</p>
          <p class="font-semibold text-gray-800 text-sm">Endang Triningrum Rizkaw</p>
        </div>
      </div>

      <div class="pt-2.5 mt-0.5" style="border-top:1px solid var(--border);">
        <p class="text-xs text-gray-400 mb-0.5">Nominal Transfer</p>
        <p class="font-extrabold text-2xl leading-none" style="color:var(--orange-600);">
          Rp {{ number_format($total, 0, ',', '.') }}
        </p>
      </div>

    </div>
  </div>

  {{-- Order summary (collapsible on mobile) --}}
  <div class="card mb-4" x-data="{ open: false }">
    <button @click="open = !open"
            class="w-full flex items-center justify-between card-p"
            style="padding-bottom: 0;"
            :style="open ? 'padding-bottom:0;' : 'padding-bottom:16px;'">
      <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">
        Ringkasan Pesanan
      </span>
      <div class="flex items-center gap-2">
        <span class="font-extrabold text-sm" style="color:var(--orange-600);">
          Rp {{ number_format($total, 0, ',', '.') }}
        </span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" class="text-gray-400 transition-transform duration-150"
             :class="open ? 'rotate-180' : ''">
          <path d="M6 9l6 6 6-6"/>
        </svg>
      </div>
    </button>

    <div x-show="open" x-collapse class="card-p" style="padding-top:12px;">
      @foreach(session('cart', []) as $item)
      <div class="flex justify-between text-sm py-1.5">
        <span class="text-gray-600 min-w-0 pr-2 truncate">
          {{ $item['name'] }} <span class="text-gray-400">×{{ $item['qty'] }}</span>
        </span>
        <span class="font-semibold text-gray-800 flex-shrink-0">
          Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
        </span>
      </div>
      @endforeach
    </div>
  </div>

  <div class="alert alert-warning mb-5" style="padding:10px 14px;">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
         style="width:15px;height:15px;flex-shrink:0;">
      <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span class="text-xs">
      Transfer <strong>tepat nominal</strong>. Setelah klik konfirmasi, upload bukti di halaman pesanan.
    </span>
  </div>

  <form method="POST" action="{{ route('payment.auto') }}" @submit="loading = true">
    @csrf
    <button type="submit"
            class="btn btn-primary btn-xl btn-block"
            :class="loading ? 'opacity-60 pointer-events-none' : ''">
      <span x-show="!loading" class="flex items-center gap-2">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
        Konfirmasi Pesanan
      </span>
      <span x-show="loading" x-cloak class="flex items-center gap-2">
        <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" opacity=".2"/>
          <path d="M21 12a9 9 0 01-9 9"/>
        </svg>
        Memproses...
      </span>
    </button>
  </form>

  <p class="text-center text-xs text-gray-400 mt-3">
    Pesanan dibuat → upload bukti → admin verifikasi
  </p>

</div>

<script>
function copyNorek() {
  const norek = document.getElementById('norek').textContent.trim();
  navigator.clipboard.writeText(norek).then(function () {
    const label = document.getElementById('copyLabel');
    label.textContent = 'Tersalin!';
    document.getElementById('copyBtn').style.color = 'var(--green-600)';
    setTimeout(function () {
      label.textContent = 'Salin';
      document.getElementById('copyBtn').style.color = '';
    }, 2000);
  }).catch(function () {
    // Fallback for older browsers
    const ta = document.createElement('textarea');
    ta.value = norek;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
    document.getElementById('copyLabel').textContent = 'Tersalin!';
    setTimeout(function () {
      document.getElementById('copyLabel').textContent = 'Salin';
    }, 2000);
  });
}
</script>

@endsection
