@extends('layouts.admin')

@section('title', 'Kelola Orders')

@section('content')

{{--
    CATATAN: File ini adalah versi lama (legacy).
    Gunakan admin/orders/index.blade.php untuk fitur lengkap.
--}}

<div class="space-y-4">

    @forelse($orders as $o)
    @php
        $badgeMap = ['yellow'=>'badge-yellow','orange'=>'badge-orange','blue'=>'badge-blue','green'=>'badge-green','red'=>'badge-red','gray'=>'badge-gray'];
        $payColor = $badgeMap[$o->payment_status_color ?? 'gray'] ?? 'badge-gray';
    @endphp
    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-5
                shadow-xl flex justify-between items-center gap-4">

        <div class="min-w-0">
            <p class="font-bold text-white truncate">{{ $o->customer_name }}</p>
            <p class="text-sm text-gray-300">📞 {{ $o->phone ?? '—' }}</p>
            @if($o->address)
            <p class="text-sm text-gray-400 truncate">📍 {{ $o->address }}</p>
            @endif
            <p class="text-xs text-gray-500 mt-1">
                {{ $o->created_at->format('d M Y H:i') }}
            </p>
        </div>

        <div class="text-right flex-shrink-0">
            <p class="text-orange-400 font-bold text-xl">
                Rp {{ number_format($o->total_price, 0, ',', '.') }}
            </p>
            {{-- FIX: status bayar sebenarnya dari model, bukan hardcode "Paid" --}}
            <span class="badge {{ $payColor }} mt-1 inline-block">
                {{ $o->payment_status_label ?? 'Belum Bayar' }}
            </span>
            <a href="{{ route('admin.orders.show', $o->id) }}"
               class="block text-xs text-blue-300 hover:text-blue-100 mt-1 underline">
                Lihat Detail →
            </a>
        </div>

    </div>
    @empty
    <div class="text-center py-16 text-gray-400">
        <p class="text-4xl mb-3">📦</p>
        <p>Belum ada order masuk</p>
    </div>
    @endforelse

</div>

@if(method_exists($orders, 'links'))
<div class="mt-6">{{ $orders->withQueryString()->links() }}</div>
@endif

@endsection
