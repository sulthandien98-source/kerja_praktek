@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- Primary stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-5">

    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="stat-label">Total Pesanan</span>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background:var(--blue-50);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--blue-500);">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 8.414V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
        <p class="stat-value">{{ number_format($totalOrders) }}</p>
        <p class="text-xs text-gray-400 mt-1">Semua waktu</p>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="stat-label">Total Revenue</span>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background:var(--green-50);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--green-600);">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                </svg>
            </div>
        </div>
        <p class="stat-value" style="font-size:18px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Semua waktu</p>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="stat-label">Pesanan Hari Ini</span>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background:var(--orange-50);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--orange-500);">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
        </div>
        <p class="stat-value">{{ number_format($todayOrders) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ now()->format('d M Y') }}</p>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="stat-label">Revenue Hari Ini</span>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background:#faf5ff;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#9333ea;">
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>
                </svg>
            </div>
        </div>
        <p class="stat-value" style="font-size:18px;">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ now()->format('d M Y') }}</p>
    </div>

</div>

{{-- Secondary stats --}}
<div class="grid grid-cols-3 gap-3 md:gap-4 mb-5">

    <div class="card card-p flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:var(--yellow-50);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--yellow-500);">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="font-extrabold text-xl text-gray-900 leading-none">{{ $pendingOrders }}</p>
            <p class="text-xs text-gray-400 mt-0.5 truncate">Pending</p>
        </div>
    </div>

    <div class="card card-p flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:var(--orange-50);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--orange-500);">
                <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="font-extrabold text-xl text-gray-900 leading-none">{{ $totalProducts }}</p>
            <p class="text-xs text-gray-400 mt-0.5 truncate">Produk</p>
        </div>
    </div>

    <div class="card card-p flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:var(--red-50);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--red-500);">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="font-extrabold text-xl text-gray-900 leading-none">{{ $outOfStock }}</p>
            <p class="text-xs text-gray-400 mt-0.5 truncate">Stok Habis</p>
        </div>
    </div>

</div>

{{-- Status breakdown + Recent orders --}}
<div class="grid lg:grid-cols-2 gap-4">

    {{-- Order status --}}
    <div class="card card-p">
        <h2 class="text-sm font-bold text-gray-900 mb-4">Status Pesanan</h2>
        @php
            $badgeMap = ['yellow'=>'badge-yellow','orange'=>'badge-orange','blue'=>'badge-blue','green'=>'badge-green','gray'=>'badge-gray','red'=>'badge-red'];
            $maxCount = $statusCounts->max() ?: 1;
        @endphp

        @forelse($statusCounts as $status => $count)
        @php
            $label = \App\Models\Order::STATUSES[$status]['label'] ?? ucfirst($status);
            $color = \App\Models\Order::STATUSES[$status]['color'] ?? 'gray';
        @endphp
        <div class="flex items-center gap-3 mb-3">
            <span class="badge {{ $badgeMap[$color] ?? 'badge-gray' }} flex-shrink-0 w-40 justify-center">
                {{ $label }}
            </span>
            <div class="flex-1 bar-track">
                <div class="bar-fill" style="width:{{ ($count / $maxCount) * 100 }}%;"></div>
            </div>
            <span class="text-sm font-bold text-gray-900 w-6 text-right flex-shrink-0">{{ $count }}</span>
        </div>
        @empty
        <div class="empty-state" style="padding:24px;">
            <p class="empty-desc">Belum ada pesanan</p>
        </div>
        @endforelse
    </div>

    {{-- Recent orders --}}
    <div class="card card-p">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-gray-900">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}"
               class="text-xs font-semibold" style="color:var(--orange-500);">
                Lihat semua →
            </a>
        </div>

        @forelse($latestOrders as $o)
        <a href="{{ route('admin.orders.show', $o->id) }}"
           class="flex items-center justify-between py-2.5 hover:opacity-75 transition"
           style="border-bottom:1px solid var(--border);">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $o->customer_name }}</p>
                <p class="text-xs text-gray-400">{{ $o->created_at->diffForHumans() }} · #{{ $o->id }}</p>
            </div>
            <span class="text-sm font-extrabold flex-shrink-0 ml-2" style="color:var(--orange-600);">
                Rp {{ number_format($o->total_price, 0, ',', '.') }}
            </span>
        </a>
        @empty
        <div class="empty-state" style="padding:24px;">
            <p class="empty-desc">Belum ada pesanan</p>
        </div>
        @endforelse
    </div>

</div>

@endsection
