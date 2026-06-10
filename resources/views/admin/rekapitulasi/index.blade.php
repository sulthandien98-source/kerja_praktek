@extends('layouts.admin')

@section('title', 'Rekapitulasi')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Rekapitulasi Penjualan</h1>
        <p class="page-subtitle">Statistik harian, mingguan, dan bulanan</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.rekapitulasi.excel') }}"
           class="btn btn-sm" style="background:var(--green-50); color:var(--green-700); border-color:var(--green-100);">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Excel
        </a>
        <a href="{{ route('admin.rekapitulasi.pdf') }}"
           class="btn btn-sm btn-danger">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF
        </a>
    </div>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">

    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="stat-label">Hari Ini</span>
            <span class="badge badge-orange">Harian</span>
        </div>
        <p class="stat-value" style="color:var(--orange-600);">
            Rp {{ number_format($dailySales, 0, ',', '.') }}
        </p>
        <p class="text-xs text-gray-400 mt-1">{{ $dailyOrders }} pesanan</p>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="stat-label">Minggu Ini</span>
            <span class="badge badge-blue">Mingguan</span>
        </div>
        <p class="stat-value" style="color:var(--blue-500);">
            Rp {{ number_format($weeklySales, 0, ',', '.') }}
        </p>
        <p class="text-xs text-gray-400 mt-1">{{ $weeklyOrders }} pesanan</p>
    </div>

    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="stat-label">Bulan Ini</span>
            <span class="badge badge-green">Bulanan</span>
        </div>
        <p class="stat-value" style="color:var(--green-600);">
            Rp {{ number_format($monthlySales, 0, ',', '.') }}
        </p>
        <p class="text-xs text-gray-400 mt-1">{{ $monthlyOrders }} pesanan</p>
    </div>

</div>

{{-- Chart --}}
<div class="card card-p mb-5">
    <h2 class="text-sm font-bold text-gray-900 mb-4">Penjualan 7 Hari Terakhir</h2>
    @php $maxSales = collect($chartData)->max('sales') ?: 1; @endphp
    <div class="space-y-3">
        @foreach($chartData as $data)
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400 w-16 flex-shrink-0 text-right">{{ $data['date'] }}</span>
            <div class="flex-1 bar-track">
                <div class="bar-fill"
                     style="width:{{ $maxSales > 0 ? ($data['sales'] / $maxSales) * 100 : 0 }}%;
                            transition: width .6s cubic-bezier(.4,0,.2,1);">
                </div>
            </div>
            <span class="text-xs font-bold text-gray-700 w-28 text-right flex-shrink-0">
                Rp {{ number_format($data['sales'], 0, ',', '.') }}
            </span>
        </div>
        @endforeach
    </div>
</div>

{{-- Recent transactions --}}
<div class="table-wrap">
    <div class="px-4 py-3" style="border-bottom:1px solid var(--border);">
        <h2 class="text-sm font-bold text-gray-900">Transaksi Terbaru</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                @php
                    $b = match($order->status) {
                        'selesai'             => 'badge-green',
                        'diproses'            => 'badge-blue',
                        'menunggu_konfirmasi' => 'badge-orange',
                        default               => 'badge-yellow',
                    };
                @endphp
                <tr>
                    <td class="cell-primary">{{ $order->customer_name }}</td>
                    <td><span class="font-bold text-sm" style="color:var(--orange-600);">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></td>
                    <td><span class="badge {{ $b }}">{{ $order->status_label }}</span></td>
                    <td class="text-gray-400 text-xs">{{ $order->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state" style="padding:32px;">
                            <p class="empty-desc">Belum ada transaksi</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
