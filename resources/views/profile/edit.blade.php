@extends('layouts.app')

@section('title', 'Profil')

@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Profil Saya</h1>
    <p class="page-subtitle">Kelola informasi akun dan keamanan Anda</p>
  </div>
</div>

<div class="max-w-2xl" style="display:flex; flex-direction:column; gap:20px;">

  @if(session('success'))
  <div class="alert alert-success animate-fade-up" x-data x-init="setTimeout(() => $el.remove(), 4000)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;">
      <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ session('success') }}
  </div>
  @endif

  @if(session('status') === 'password-updated')
  <div class="alert alert-success animate-fade-up" x-data x-init="setTimeout(() => $el.remove(), 4000)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;">
      <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    Password berhasil diperbarui.
  </div>
  @endif

  <!-- INFORMASI PROFIL -->
  <div class="card card-p">
    <div class="flex items-center gap-3 mb-5">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--orange-50);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--orange-600)" stroke-width="2">
          <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
      </div>
      <div>
        <h2 class="font-bold text-gray-900" style="font-size:15px;">Informasi Profil</h2>
        <p class="page-subtitle" style="margin-top:0;">Perbarui nama dan alamat email akun Anda</p>
      </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}">
      @csrf
      @method('PATCH')

      <div class="form-group">
        <label class="form-label" for="name">Nama Lengkap</label>
        <input id="name" type="text" name="name"
               value="{{ old('name', $user->name) }}"
               class="input @error('name') error @enderror"
               required autocomplete="name" autofocus>
        @error('name')
          <p class="field-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-group" style="margin-bottom:20px;">
        <label class="form-label" for="email">Alamat Email</label>
        <input id="email" type="email" name="email"
               value="{{ old('email', $user->email) }}"
               class="input @error('email') error @enderror"
               required autocomplete="username">
        @error('email')
          <p class="field-error">{{ $message }}</p>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
          <div class="alert alert-warning mt-3">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;">
              <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span>
              Email Anda belum terverifikasi.
              <button form="send-verification" class="underline font-semibold" style="color:var(--yellow-700);">
                Kirim ulang email verifikasi
              </button>
            </span>
          </div>

          @if (session('status') === 'verification-link-sent')
            <p class="text-sm font-medium mt-2" style="color:var(--green-600);">
              Link verifikasi baru telah dikirim ke alamat email Anda.
            </p>
          @endif
        @endif
      </div>

      <button type="submit" class="btn btn-primary btn-block">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
          <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
        </svg>
        Simpan Perubahan
      </button>
    </form>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
      @csrf
    </form>
  </div>

  <!-- UBAH PASSWORD -->
  <div class="card card-p">
    <div class="flex items-center gap-3 mb-5">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--blue-50);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2">
          <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
        </svg>
      </div>
      <div>
        <h2 class="font-bold text-gray-900" style="font-size:15px;">Ubah Password</h2>
        <p class="page-subtitle" style="margin-top:0;">Gunakan password yang kuat dan unik</p>
      </div>
    </div>

    <form method="post" action="{{ route('password.update') }}">
      @csrf
      @method('put')

      <div class="form-group">
        <label class="form-label" for="update_password_current_password">Password Saat Ini</label>
        <input id="update_password_current_password" type="password" name="current_password"
               class="input @error('current_password', 'updatePassword') error @enderror"
               autocomplete="current-password">
        @error('current_password', 'updatePassword')
          <p class="field-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="update_password_password">Password Baru</label>
        <input id="update_password_password" type="password" name="password"
               class="input @error('password', 'updatePassword') error @enderror"
               autocomplete="new-password">
        @error('password', 'updatePassword')
          <p class="field-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-group" style="margin-bottom:20px;">
        <label class="form-label" for="update_password_password_confirmation">Konfirmasi Password Baru</label>
        <input id="update_password_password_confirmation" type="password" name="password_confirmation"
               class="input @error('password_confirmation', 'updatePassword') error @enderror"
               autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
          <p class="field-error">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit" class="btn btn-secondary btn-block">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
        </svg>
        Perbarui Password
      </button>
    </form>
  </div>

  <!-- HAPUS AKUN -->
  <div class="card card-p" style="border-color:var(--red-100);">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--red-50);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--red-600)" stroke-width="2">
          <path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0l-1 14a2 2 0 01-2 2H7a2 2 0 01-2-2L4 6h16z"/>
        </svg>
      </div>
      <div>
        <h2 class="font-bold" style="font-size:15px; color:var(--red-700);">Hapus Akun</h2>
        <p class="page-subtitle" style="margin-top:0;">Tindakan ini bersifat permanen dan tidak dapat dibatalkan</p>
      </div>
    </div>

    <p class="text-sm text-gray-600 mb-4" style="line-height:1.6;">
      Setelah akun dihapus, seluruh data dan riwayat pesanan Anda akan dihapus secara permanen.
      Pastikan Anda telah menyimpan informasi yang masih diperlukan sebelum melanjutkan.
    </p>

    <form method="POST" action="{{ route('profile.destroy') }}">
      @csrf
      @method('DELETE')

      <div class="form-group" style="margin-bottom:14px;">
        <input type="password"
               name="password"
               placeholder="Masukkan password untuk konfirmasi"
               class="input @error('password', 'userDeletion') error @enderror"
               required>
        @error('password', 'userDeletion')
          <p class="field-error">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit" class="btn btn-danger btn-block">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m3 0l-1 14a2 2 0 01-2 2H7a2 2 0 01-2-2L4 6h16z"/>
        </svg>
        Hapus Akun Saya
      </button>
    </form>
  </div>

</div>

@endsection