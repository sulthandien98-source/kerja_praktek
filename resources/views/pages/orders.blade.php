@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Pesanan Saya</h1>
    <p class="page-subtitle">Riwayat dan status semua pesananmu</p>
  </div>
  <a href="{{ route('menu') }}" class="btn btn-primary btn-sm">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2.5">
      <line x1="12" y1="5" x2="12" y2="19"/>
      <line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    Pesan Lagi
  </a>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3500)" x-show="show"
     class="alert alert-success mb-5 animate-fade-up">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
    <polyline points="22 4 12 14.01 9 11.01"/>
  </svg>
  {{ session('success') }}
</div>
@endif

@php
  $badgeMap = [
    'yellow' => 'badge-yellow', 'orange' => 'badge-orange',
    'blue'   => 'badge-blue',   'green'  => 'badge-green',
    'red'    => 'badge-red',    'gray'   => 'badge-gray',
  ];
  $payBadge = [
    'gray'   => 'badge-gray',   'yellow' => 'badge-yellow',
    'green'  => 'badge-green',  'red'    => 'badge-red',
  ];
@endphp

@forelse($orders as $o)
@php
  $payStatus  = $o->payment_status ?? 'unpaid';
  $isRejected = $payStatus === 'rejected';
  $needUpload = $o->canUploadPayment();
@endphp

<div class="card card-hover mb-3"
     style="{{ $isRejected ? 'border-color:var(--red-200);' : ($payStatus === 'uploaded' ? 'border-color:var(--yellow-200);' : '') }}">
  <div class="card-p">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-3 mb-3">
      <div class="min-w-0">
        <p class="font-bold text-gray-900">{{ $o->customer_name }}</p>
        <p class="text-xs text-gray-400 mt-0.5">
          #{{ $o->id }} · {{ $o->created_at->format('d M Y, H:i') }}
        </p>
      </div>
      <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
        <span class="badge {{ $badgeMap[$o->status_color] ?? 'badge-gray' }}">
          {{ $o->status_label }}
        </span>
        <span class="badge {{ $payBadge[$o->payment_status_color ?? 'gray'] ?? 'badge-gray' }}">
          {{ $o->payment_status_label ?? 'Belum Bayar' }}
        </span>
      </div>
    </div>

    {{-- Contact info --}}
    <div class="flex flex-wrap gap-x-4 gap-y-1 mb-3">
      <p class="text-xs text-gray-500 flex items-center gap-1">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2">
          <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.63a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.72 6.72l1.28-1.28a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
        </svg>
        {{ $o->phone }}
      </p>
      @if($o->address)
      <p class="text-xs text-gray-500 flex items-center gap-1 min-w-0">
        <svg width="11" height="11" class="flex-shrink-0" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
        <span class="truncate">{{ $o->address }}</span>
      </p>
      @endif
    </div>

    {{-- Items --}}
    @if($o->items && $o->items->count())
    <div class="mb-3 py-2.5 space-y-1"
         style="border-top:1px solid var(--border); border-bottom:1px solid var(--border);">
      @foreach($o->items as $item)
      <div class="flex justify-between text-xs">
        <span class="text-gray-500 min-w-0 pr-2 truncate">
          {{ $item->product->name ?? 'Produk dihapus' }} ×{{ $item->quantity }}
        </span>
        <span class="text-gray-700 font-medium flex-shrink-0">
          Rp {{ number_format($item->subtotal, 0, ',', '.') }}
        </span>
      </div>
      @endforeach
    </div>
    @endif

    {{-- Rejection note --}}
    @if($isRejected && $o->payment_note)
    <div class="alert alert-error mb-3" style="padding:8px 12px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
           style="width:13px;height:13px;flex-shrink:0;">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <span class="text-xs">
        <strong>Ditolak:</strong> {{ $o->payment_note }}
      </span>
    </div>
    @endif

    {{-- Footer: total + CTA --}}
    <div class="flex items-center justify-between gap-3">
      <span class="font-extrabold" style="color:var(--orange-600); font-size:16px;">
        Rp {{ number_format($o->total_price, 0, ',', '.') }}
      </span>

      {{-- CTA — full-tap-area on mobile --}}
      @if($isRejected)
      <a href="{{ route('order.waiting', $o->id) }}"
         class="btn btn-danger btn-sm"
         style="min-width:110px; justify-content:center;">
        ⚠️ Upload Ulang
      </a>
      @elseif($needUpload)
      <a href="{{ route('order.waiting', $o->id) }}"
         class="btn btn-primary btn-sm"
         style="min-width:110px; justify-content:center;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5">
          <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
          <polyline points="17 8 12 3 7 8"/>
          <line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
        Upload Bukti
      </a>
      @else
      <a href="{{ route('order.waiting', $o->id) }}"
         class="btn btn-secondary btn-sm"
         style="min-width:80px; justify-content:center;">
        Detail
      </a>
      @endif
    </div>

  </div>
</div>

@empty

<div class="empty-state">
  <div class="empty-icon" style="font-size:28px; background:var(--orange-50);">📦</div>
  <p class="empty-title">Belum ada pesanan</p>
  <p class="empty-desc">Yuk mulai pesan dim sum favoritmu!</p>
  <a href="{{ route('menu') }}" class="btn btn-primary" style="margin-top:8px;">
    Lihat Menu
  </a>
</div>

@endforelse

@endsection
