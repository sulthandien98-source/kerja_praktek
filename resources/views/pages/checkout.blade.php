@extends('layouts.app')

@section('title', 'Checkout')

@section('content')

<div class="max-w-2xl mx-auto">

  <div class="page-header">
    <div>
      <h1 class="page-title">Checkout</h1>
      <p class="page-subtitle">Konfirmasi data pengiriman</p>
    </div>
    <a href="{{ route('menu') }}" class="btn btn-secondary btn-sm">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
      </svg>
      Kembali
    </a>
  </div>

  @if($errors->any())
  <div class="alert alert-error mb-4">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;">
      <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
    </svg>
    {{ $errors->first() }}
  </div>
  @endif

  @php $total = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']); @endphp

  <div class="flex items-center justify-between mb-4 px-4 py-3 rounded-xl md:hidden"
       style="background:var(--orange-50); border:1px solid var(--orange-200);">
    <span class="text-sm font-semibold text-gray-700">
      {{ collect($cart)->sum('qty') }} item
    </span>
    <span class="font-extrabold text-base" style="color:var(--orange-600);">
      Rp {{ number_format($total, 0, ',', '.') }}
    </span>
  </div>

  <div class="flex flex-col md:flex-row gap-4">

    <div class="flex-1 min-w-0">
      <div class="card card-p">
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">
          Data Penerima
        </h2>

        <form method="POST" action="{{ route('checkout.process') }}"
              id="checkoutForm" class="space-y-4" novalidate>
          @csrf

          <div class="form-group">
            <label class="form-label" for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name', auth()->user()->name ?? '') }}"
                   class="input {{ $errors->has('name') ? 'error' : '' }}"
                   placeholder="Masukkan nama penerima"
                   autocomplete="name"
                   maxlength="100"
                   required>
            @error('name')
            <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="phone">Nomor HP</label>
            <input type="tel" id="phone" name="phone"
                   value="{{ old('phone') }}"
                   class="input {{ $errors->has('phone') ? 'error' : '' }}"
                   placeholder="08xxxxxxxxxx"
                   autocomplete="tel"
                   inputmode="tel"
                   maxlength="20"
                   required>
            @error('phone')
            <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="address">Alamat Lengkap</label>
            <textarea id="address" name="address"
                      class="textarea {{ $errors->has('address') ? 'error' : '' }}"
                      placeholder="Jl. Contoh No. 1, RT/RW, Kelurahan, Kota"
                      autocomplete="street-address"
                      rows="3"
                      maxlength="500"
                      required>{{ old('address') }}</textarea>
            @error('address')
            <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="alert alert-orange" style="padding:10px 12px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 style="width:14px;height:14px;flex-shrink:0;">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span class="text-xs">Pastikan data sudah benar sebelum melanjutkan.</span>
          </div>

          <button type="submit" id="submitBtn" class="btn btn-primary btn-xl btn-block">
            <span id="submitText">Lanjut ke Pembayaran</span>
            <svg id="submitArrow" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            <svg id="submitSpinner" class="animate-spin hidden" width="16" height="16"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" opacity=".2"/>
              <path d="M21 12a9 9 0 01-9 9"/>
            </svg>
          </button>
        </form>
      </div>
    </div>

    <div class="hidden md:block w-72 flex-shrink-0">
      <div class="card card-p" style="position:sticky; top:calc(var(--topbar-h) + 20px);">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">
          Ringkasan Pesanan
        </h2>
        @foreach($cart as $item)
        <div class="flex items-start justify-between py-2.5"
             style="border-bottom:1px solid var(--border);">
          <div class="min-w-0 pr-2">
            <p class="text-sm font-semibold text-gray-900 truncate">{{ $item['name'] }}</p>
            <p class="text-xs text-gray-400">×{{ $item['qty'] }}</p>
          </div>
          <span class="text-sm font-bold flex-shrink-0" style="color:var(--orange-600);">
            Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
          </span>
        </div>
        @endforeach
        <div class="flex items-center justify-between pt-3 mt-1">
          <span class="font-bold text-gray-900">Total</span>
          <span class="text-lg font-extrabold" style="color:var(--orange-600);">
            Rp {{ number_format($total, 0, ',', '.') }}
          </span>
        </div>
      </div>
    </div>

  </div>

</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
  var name = document.getElementById('name').value.trim();
  var phone = document.getElementById('phone').value.trim();
  var address = document.getElementById('address').value.trim();

  if (!name || !phone || !address) return;

  document.getElementById('submitBtn').disabled = true;
  document.getElementById('submitText').textContent = 'Memproses...';
  document.getElementById('submitArrow').classList.add('hidden');
  document.getElementById('submitSpinner').classList.remove('hidden');
});
</script>

@endsection