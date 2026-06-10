@extends('layouts.admin')

@section('title', 'Pesanan')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Pesanan</h1>
        <p class="page-subtitle">{{ $orders->total() }} total pesanan</p>
    </div>
</div>

{{-- Pending payment alert --}}
{{-- FIX: $pendingPaymentCount harus dikirim dari controller, bukan dihitung dari paginated collection --}}
@if(isset($pendingPaymentCount) && $pendingPaymentCount > 0)
<div class="alert alert-warning mb-4">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span><strong>{{ $pendingPaymentCount }} pesanan</strong> menunggu verifikasi pembayaran.</span>
</div>
@endif

{{-- Session flash messages --}}
@if(session('success'))
<div class="alert alert-success mb-4">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="alert alert-error mb-4">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;"><circle cx="12" cy="12" r="10"/></svg>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- Filter & Search --}}
<div class="card card-p mb-4">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="form-group mb-0 flex-1 min-w-40">
            <label class="form-label">Cari Customer</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="input" placeholder="Nama atau no. HP...">
        </div>
        <div class="form-group mb-0">
            <label class="form-label">Status Order</label>
            <select name="status" class="select-field">
                <option value="">Semua Status</option>
                @foreach(\App\Models\Order::STATUSES as $val => $info)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                    {{ $info['label'] }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-0">
            <label class="form-label">Status Bayar</label>
            <select name="payment_status" class="select-field">
                <option value="">Semua</option>
                <option value="unpaid"   {{ request('payment_status') === 'unpaid'   ? 'selected' : '' }}>Belum Bayar</option>
                <option value="uploaded" {{ request('payment_status') === 'uploaded' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="approved" {{ request('payment_status') === 'approved' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if(request()->hasAny(['search','status','payment_status']))
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="table-wrap">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status Order</th>
                    <th>Status Bayar</th>
                    <th class="hidden md:table-cell">Tanggal</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                @php
                    $badgeMap = ['yellow'=>'badge-yellow','orange'=>'badge-orange','blue'=>'badge-blue','green'=>'badge-green','red'=>'badge-red','gray'=>'badge-gray'];
                @endphp
                <tr>
                    <td><span class="cell-primary cell-mono">#{{ $order->id }}</span></td>
                    <td>
                        <p class="cell-primary">{{ $order->customer_name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->phone ?? '—' }}</p>
                    </td>
                    <td>
                        <span class="font-bold text-sm" style="color:var(--orange-600);">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $badgeMap[$order->status_color] ?? 'badge-gray' }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td>
                        @php $payColor = $badgeMap[$order->payment_status_color ?? 'gray'] ?? 'badge-gray'; @endphp
                        <span class="badge {{ $payColor }}">
                            {{ $order->payment_status_label ?? 'Belum Bayar' }}
                        </span>
                        @if(($order->payment_status ?? '') === 'uploaded')
                        <span class="block text-xs font-bold mt-0.5 animate-pulse" style="color:var(--orange-500);">● Perlu aksi</span>
                        @endif
                    </td>
                    <td class="hidden md:table-cell">
                        <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }}</p>
                    </td>
                    <td class="text-right">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary btn-sm">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">📦</div>
                            <p class="empty-title">Belum ada pesanan</p>
                            @if(request()->hasAny(['search','status','payment_status']))
                            <p class="empty-desc">Coba ubah filter pencarian</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $orders->withQueryString()->links() }}</div>

@endsection
