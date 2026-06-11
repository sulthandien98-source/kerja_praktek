@extends('layouts.app')

@section('title', 'Status Pesanan #' . $order->id)

@section('content')

@php
  $payStatus = $order->payment_status ?? 'unpaid';
  $configs = [
    'approved' => [
      'icon'  => '✅',
      'title' => 'Pembayaran Terverifikasi',
      'sub'   => 'Pesanan kamu sedang diproses.',
      'alert' => 'alert-success',
    ],
    'rejected' => [
      'icon'  => '❌',
      'title' => 'Bukti Pembayaran Ditolak',
      'sub'   => 'Silakan upload ulang bukti yang valid.',
      'alert' => 'alert-error',
    ],
    'uploaded' => [
      'icon'  => '🔍',
      'title' => 'Menunggu Verifikasi',
      'sub'   => 'Bukti sedang diperiksa admin.',
      'alert' => 'alert-warning',
    ],
    'unpaid'   => [
      'icon'  => '📤',
      'title' => 'Upload Bukti Transfer',
      'sub'   => 'Segera upload agar pesanan diproses.',
      'alert' => 'alert-warning',
    ],
  ];
  $cfg = $configs[$payStatus] ?? $configs['unpaid'];

  $badgeMap = [
    'yellow' => 'badge-yellow', 'orange' => 'badge-orange',
    'blue'   => 'badge-blue',   'green'  => 'badge-green',
    'red'    => 'badge-red',    'gray'   => 'badge-gray',
  ];
  $payBadge = ['gray'=>'badge-gray','yellow'=>'badge-yellow','green'=>'badge-green','red'=>'badge-red'];
@endphp

<div class="max-w-lg mx-auto">

  <div class="text-center py-4 mb-5">
    <div class="text-5xl mb-3" role="img" aria-label="Status">{{ $cfg['icon'] }}</div>
    <h1 class="text-xl font-extrabold text-gray-900 mb-1">{{ $cfg['title'] }}</h1>
    <p class="text-sm text-gray-500">{{ $cfg['sub'] }}</p>
  </div>

  @if(session('success'))
  <div class="alert alert-success mb-4 animate-fade-up" x-data x-init="setTimeout(() => $el.remove(), 4000)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;">
      <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-error mb-4 animate-fade-up">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;">
      <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
    </svg>
    {{ session('error') }}
  </div>
  @endif

  <div class="card card-p mb-4">
    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Detail Pesanan</h2>
    <div class="space-y-0">
      <div class="flex justify-between items-center py-2.5" style="border-bottom:1px solid var(--border);">
        <span class="text-sm text-gray-500">No. Pesanan</span>
        <span class="text-sm font-bold text-gray-900 font-mono">#{{ $order->id }}</span>
      </div>
      <div class="flex justify-between items-center py-2.5" style="border-bottom:1px solid var(--border);">
        <span class="text-sm text-gray-500">Nama</span>
        <span class="text-sm font-semibold text-gray-800">{{ $order->customer_name }}</span>
      </div>
      <div class="flex justify-between items-center py-2.5" style="border-bottom:1px solid var(--border);">
        <span class="text-sm text-gray-500">Tanggal</span>
        <span class="text-sm font-semibold text-gray-800">{{ $order->created_at->format('d M Y, H:i') }}</span>
      </div>
      <div class="flex justify-between items-center py-2.5" style="border-bottom:1px solid var(--border);">
        <span class="text-sm text-gray-500">Total</span>
        <span class="font-extrabold" style="color:var(--orange-600); font-size:16px;">
          Rp {{ number_format($order->total_price, 0, ',', '.') }}
        </span>
      </div>
      <div class="flex justify-between items-center py-2.5" style="border-bottom:1px solid var(--border);">
        <span class="text-sm text-gray-500">Status Order</span>
        <span class="badge {{ $badgeMap[$order->status_color] ?? 'badge-gray' }}">
          {{ $order->status_label }}
        </span>
      </div>
      <div class="flex justify-between items-center py-2.5">
        <span class="text-sm text-gray-500">Status Bayar</span>
        <span class="badge {{ $payBadge[$order->payment_status_color ?? 'gray'] ?? 'badge-gray' }}">
          {{ $order->payment_status_label ?? 'Belum Bayar' }}
        </span>
      </div>
    </div>
  </div>

  @if($order->items && $order->items->count())
  <div class="card card-p mb-4" x-data="{ open: false }">
    <button @click="open = !open" type="button"
            class="w-full flex items-center justify-between">
      <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Item Pesanan</span>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
           class="text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''">
        <path d="M6 9l6 6 6-6"/>
      </svg>
    </button>
    <div x-show="open" x-collapse class="mt-3 space-y-1">
      @foreach($order->items as $item)
      <div class="flex justify-between text-sm py-1">
        <span class="text-gray-600">{{ $item->product->name ?? 'Produk dihapus' }} ×{{ $item->quantity }}</span>
        <span class="font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  @if($payStatus === 'rejected' && $order->payment_note)
  <div class="alert alert-error mb-4">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
         style="flex-shrink:0;width:15px;height:15px;">
      <circle cx="12" cy="12" r="10"/>
      <line x1="12" y1="8" x2="12" y2="12"/>
      <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <div>
      <p class="font-bold text-xs mb-0.5">Alasan Penolakan</p>
      <p class="text-xs">{{ $order->payment_note }}</p>
    </div>
  </div>
  @endif

  @if($order->payment_proof && in_array($payStatus, ['uploaded', 'approved']))
  <div class="card card-p mb-4">
    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">
      Bukti Transfer Terkirim
    </h2>
    @php $ext = strtolower(pathinfo($order->payment_proof, PATHINFO_EXTENSION)); @endphp
    @if(in_array($ext, ['jpg','jpeg','png','webp']))
    <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noopener noreferrer" class="block">
      <img src="{{ $order->payment_proof_url }}" alt="Bukti Transfer"
           class="w-full rounded-xl object-contain border"
           style="max-height:280px; border-color:var(--border);"
           loading="lazy">
    </a>
    @else
    <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noopener noreferrer"
       class="flex items-center gap-2 text-sm font-semibold" style="color:var(--blue-500);">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
        <polyline points="14 2 14 8 20 8"/>
      </svg>
      Lihat File Bukti Transfer
    </a>
    @endif
    @if($order->payment_uploaded_at)
    <p class="text-xs text-gray-400 mt-2">
      Diupload {{ $order->payment_uploaded_at->format('d M Y, H:i') }}
    </p>
    @endif
  </div>
  @endif

  @if($order->canUploadPayment())
  <div class="card card-p mb-4" x-data="uploadZone()">
    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">
      {{ $payStatus === 'rejected' ? 'Upload Ulang Bukti Transfer' : 'Upload Bukti Transfer' }}
    </h2>

    @if($errors->has('payment_proof'))
    <div class="alert alert-error mb-3" style="padding:8px 12px; font-size:12px;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
           style="width:13px;height:13px;flex-shrink:0;">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      {{ $errors->first('payment_proof') }}
    </div>
    @endif

    <form method="POST"
          action="{{ route('order.uploadPayment', $order->id) }}"
          enctype="multipart/form-data"
          @submit="submitting = true">
      @csrf

      <label class="drop-zone block cursor-pointer mb-3"
             :class="{ 'drag-over': dragging, 'has-file': file }"
             @dragover.prevent="dragging = true"
             @dragleave.prevent="dragging = false"
             @drop.prevent="onDrop($event)">

        <input type="file" name="payment_proof" class="sr-only"
               accept="image/jpeg,image/png,image/webp,application/pdf"
               capture="environment"
               @change="onFileChange($event)"
               x-ref="fileInput">

        <template x-if="!preview">
          <div>
            <svg class="mx-auto mb-2 text-gray-300" width="32" height="32"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
              <polyline points="17 8 12 3 7 8"/>
              <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            <p class="text-sm font-semibold text-gray-600 mb-1">Klik atau ambil foto bukti</p>
            <p class="text-xs text-gray-400">JPG, PNG, WEBP, PDF &middot; Maks 2MB</p>
          </div>
        </template>

        <template x-if="preview && previewType === 'image'">
          <div>
            <img :src="preview" class="max-h-44 mx-auto rounded-xl object-contain mb-2" style="max-width:100%;">
            <p class="text-xs font-semibold" style="color:var(--green-600);" x-text="fileName"></p>
          </div>
        </template>

        <template x-if="previewType === 'pdf'">
          <div>
            <svg class="mx-auto mb-2" width="32" height="32" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--red-500);">
              <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
            <p class="text-xs font-semibold" style="color:var(--green-600);" x-text="fileName"></p>
          </div>
        </template>
      </label>

      <template x-if="fileError">
        <p class="field-error mb-3" x-text="fileError"></p>
      </template>

      <button type="submit"
              :disabled="!file || submitting"
              class="btn btn-primary btn-lg btn-block"
              :class="(!file || submitting) ? 'opacity-40' : ''">
        <span x-show="!submitting" class="flex items-center gap-2">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
          </svg>
          Upload Bukti Transfer
        </span>
        <span x-show="submitting" x-cloak class="flex items-center gap-2">
          <svg class="animate-spin" width="15" height="15" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" opacity=".2"/>
            <path d="M21 12a9 9 0 01-9 9"/>
          </svg>
          Mengupload...
        </span>
      </button>
    </form>
  </div>
  @endif

  @if($payStatus === 'approved')
  <div class="alert alert-success mb-4">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;">
      <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    Pembayaran sudah terverifikasi. Pesananmu sedang diproses!
  </div>
  @endif

  <div class="grid grid-cols-2 gap-3">
    <a href="{{ route('orders') }}" class="btn btn-secondary justify-center" style="padding:12px;">
      Semua Pesanan
    </a>
    <a href="{{ route('menu') }}" class="btn btn-ghost justify-center" style="padding:12px;">
      Pesan Lagi
    </a>
  </div>

</div>

<script>
function uploadZone() {
  return {
    file: null, preview: null, previewType: null,
    fileName: '', fileError: null, dragging: false, submitting: false,

    onFileChange(e) { this.process(e.target.files[0]); },

    onDrop(e) {
      this.dragging = false;
      var f = e.dataTransfer.files[0];
      if (!f) return;
      var dt = new DataTransfer();
      dt.items.add(f);
      this.$refs.fileInput.files = dt.files;
      this.process(f);
    },

    process(f) {
      this.fileError = null;
      var allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
      if (!allowed.includes(f.type)) {
        this.fileError = 'Format tidak didukung. Gunakan JPG, PNG, WEBP, atau PDF.';
        this.file = null;
        return;
      }
      if (f.size > 2 * 1024 * 1024) {
        this.fileError = 'Ukuran file melebihi 2MB.';
        this.file = null;
        return;
      }
      this.file = f;
      this.fileName = f.name;
      if (f.type === 'application/pdf') {
        this.previewType = 'pdf';
        this.preview = 'pdf';
        return;
      }
      var reader = new FileReader();
      reader.onload = (e) => {
        this.preview = e.target.result;
        this.previewType = 'image';
      };
      reader.readAsDataURL(f);
    }
  };
}
</script>

@endsection