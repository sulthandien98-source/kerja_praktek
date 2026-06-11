@extends('layouts.admin')

@section('title', 'Pesanan #' . $order->id)

@section('content')

@php
    $badgeMap = ['yellow'=>'badge-yellow','orange'=>'badge-orange','blue'=>'badge-blue','green'=>'badge-green','red'=>'badge-red','gray'=>'badge-gray'];
    $payStatus = $order->payment_status ?? 'unpaid';
@endphp

@if(session('success'))
<div class="alert alert-success mb-4" x-data x-init="setTimeout(() => $el.remove(), 4000)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="alert alert-error mb-4">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    <span>{{ session('error') }}</span>
</div>
@endif

<div class="page-header">
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm" style="padding:6px 10px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="page-title">Pesanan #{{ $order->id }}</h1>
            <p class="page-subtitle">{{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <span class="badge {{ $badgeMap[$order->status_color] ?? 'badge-gray' }}">
                {{ $order->status_label }}
            </span>
            <span class="badge {{ $badgeMap[$order->payment_status_color ?? 'gray'] ?? 'badge-gray' }}">
                {{ $order->payment_status_label ?? 'Belum Bayar' }}
            </span>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-4">

    <div class="lg:col-span-2 space-y-4">

        <div class="card card-p">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Customer</h2>
            <p class="font-bold text-gray-900 text-base">{{ $order->customer_name }}</p>
            <div class="mt-2 space-y-1">
                @if($order->phone)
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.63a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.72 6.72l1.28-1.28a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    <a href="tel:{{ $order->phone }}" style="color:inherit;">{{ $order->phone }}</a>
                </p>
                @endif
                @if($order->address)
                <p class="text-sm text-gray-500 flex items-start gap-2">
                    <svg class="flex-shrink-0 mt-0.5" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $order->address }}
                </p>
                @endif
                @if($order->user)
                <p class="text-xs text-gray-400">Akun: {{ $order->user->email }}</p>
                @endif
            </div>
        </div>

        <div class="card card-p">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Detail Pesanan</h2>
            @forelse($order->items as $item)
            <div class="flex justify-between items-center py-2.5" style="border-bottom:1px solid var(--border);">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $item->product->name ?? 'Produk dihapus' }}</p>
                    <p class="text-xs text-gray-400">{{ $item->quantity }} &times; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                </div>
                <span class="font-bold text-sm" style="color:var(--orange-600);">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Tidak ada item</p>
            @endforelse
            <div class="flex justify-between items-center pt-3 mt-1">
                <span class="font-bold text-gray-900">Total</span>
                <span class="text-lg font-extrabold" style="color:var(--orange-600);">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        @if($order->payment_proof)
        <div class="card card-p">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Bukti Transfer</h2>
            @php $ext = strtolower(pathinfo($order->payment_proof, PATHINFO_EXTENSION)); @endphp
            @if(in_array($ext, ['jpg','jpeg','png','webp','gif']))
            <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noopener noreferrer">
                <img src="{{ $order->payment_proof_url }}" alt="Bukti Transfer"
                     class="w-full max-h-72 object-contain rounded-xl border cursor-zoom-in hover:opacity-90 transition"
                     style="border-color:var(--border);"
                     loading="lazy">
            </a>
            @else
            <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noopener noreferrer"
               class="flex items-center gap-2 text-sm font-semibold" style="color:var(--blue-500);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Buka File Bukti
            </a>
            @endif
            @if($order->payment_uploaded_at)
            <p class="text-xs text-gray-400 mt-2">Diupload {{ $order->payment_uploaded_at->format('d M Y, H:i') }}</p>
            @endif

            @if($payStatus === 'rejected' && $order->payment_note)
            <div class="alert alert-error mt-3" style="padding:8px 12px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;flex-shrink:0;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span class="text-xs"><strong>Alasan penolakan:</strong> {{ $order->payment_note }}</span>
            </div>
            @endif
        </div>
        @endif

    </div>

    <div class="space-y-4">

        @if($payStatus === 'uploaded')
        <div class="card card-p" style="border-color:var(--yellow-200); background:var(--yellow-50);">
            <h2 class="text-xs font-bold uppercase tracking-wide mb-3" style="color:var(--yellow-700);">
                🔍 Verifikasi Pembayaran
            </h2>

            <form method="POST" action="{{ route('admin.orders.approvePayment', $order->id) }}" class="mb-2">
                @csrf
                <button type="submit"
                        class="btn btn-success btn-lg btn-block js-confirm"
                        data-message="Setujui pembayaran pesanan #{{ $order->id }}?">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                    Setujui Pembayaran
                </button>
            </form>

            <div x-data="{ open: false }">
                <button @click="open = !open" type="button" class="btn btn-danger btn-block" style="padding:9px 16px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Tolak Pembayaran
                </button>

                <div x-show="open" x-cloak x-transition class="mt-3">
                    <form method="POST" action="{{ route('admin.orders.rejectPayment', $order->id) }}" class="space-y-2">
                        @csrf
                        <textarea name="payment_note" required rows="3"
                                  class="textarea"
                                  placeholder="Alasan penolakan (wajib diisi)..."
                                  maxlength="255"></textarea>
                        <button type="submit" class="btn btn-danger btn-block">
                            Kirim Penolakan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @elseif($payStatus === 'approved')
        <div class="card card-p text-center" style="border-color:var(--green-200); background:var(--green-50);">
            <svg class="mx-auto mb-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--green-600);"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <p class="text-sm font-bold" style="color:var(--green-700);">Pembayaran Terverifikasi</p>
        </div>

        @elseif($payStatus === 'rejected')
        <div class="card card-p" style="border-color:var(--red-200); background:var(--red-50);">
            <p class="text-sm font-bold text-center" style="color:var(--red-600);">❌ Pembayaran Ditolak</p>
            @if($order->payment_note)
            <p class="text-xs text-gray-500 mt-2 text-center">{{ $order->payment_note }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-2 text-center">Menunggu customer upload ulang</p>
        </div>

        @else
        <div class="card card-p text-center" style="border-color:var(--border);">
            <p class="text-sm text-gray-400">⏳ Menunggu upload bukti</p>
        </div>
        @endif

        <div class="card card-p">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Update Status Order</h2>
            <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                @csrf @method('PUT')
                <div class="form-group mb-3">
                    <select name="status" id="status-select" class="select-field">
                        @foreach(\App\Models\Order::STATUSES as $val => $info)
                        <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>
                            {{ $info['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    Simpan Status
                </button>
            </form>
        </div>

        <div class="card card-p">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Info</h2>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-400">ID</span>
                    <span class="font-semibold text-gray-900 font-mono">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-400">Dibuat</span>
                    <span class="font-semibold text-gray-900">{{ $order->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-400">Diupdate</span>
                    <span class="font-semibold text-gray-900">{{ $order->updated_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-400">Total Item</span>
                    <span class="font-semibold text-gray-900">{{ $order->items->sum('quantity') }} pcs</span>
                </div>
            </div>
        </div>

        <div class="card card-p" style="border-color:var(--red-200);">
            <h2 class="text-xs font-bold uppercase tracking-wide mb-3" style="color:var(--red-500);">Hapus Pesanan</h2>
            <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="btn btn-danger btn-sm btn-block js-confirm"
                        data-message="Hapus pesanan #{{ $order->id }} secara permanen? Tindakan ini tidak bisa dibatalkan.">
                    Hapus Pesanan
                </button>
            </form>
        </div>

    </div>
</div>

<script>
document.querySelectorAll('.js-confirm').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        var message = this.dataset.message || 'Anda yakin?';
        if (!window.confirm(message)) {
            e.preventDefault();
        }
    });
});
</script>

@endsection