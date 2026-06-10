@extends('layouts.admin')

@section('title', 'Produk')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Produk</h1>
        <p class="page-subtitle">Kelola menu, stok, dan ketersediaan</p>
    </div>
    <button onclick="openModal('add')" class="btn btn-primary btn-sm">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Produk
    </button>
</div>

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

@if($errors->any())
<div class="alert alert-error mb-4">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;"><circle cx="12" cy="12" r="10"/></svg>
    <span>{{ $errors->first() }}</span>
</div>
@endif

<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">

    @forelse($products as $p)

    {{-- FIX: simpan data produk di data-* attribute, bukan di onclick string (aman dari XSS) --}}
    <div class="card card-hover"
         data-product-id="{{ $p->id }}"
         data-product-name="{{ $p->name }}"
         data-product-price="{{ $p->price }}"
         data-product-stock="{{ $p->stock }}"
         data-product-description="{{ $p->description ?? '' }}">

        <div class="card-p">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="font-bold text-lg text-slate-800">
                        {{ $p->name }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">
                        ID #{{ $p->id }}
                    </p>
                </div>
                <span class="badge {{ $p->is_available ? 'badge-green' : 'badge-red' }}">
                    {{ $p->is_available ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            {{-- Deskripsi --}}
            @if($p->description)
            <p class="text-sm text-slate-500 mt-3 line-clamp-2">
                {{ $p->description }}
            </p>
            @endif

            {{-- Harga --}}
            <div class="mt-5">
                <p class="text-xs uppercase tracking-wide text-slate-400">
                    Harga
                </p>
                <p class="text-2xl font-extrabold text-orange-600">
                    Rp {{ number_format($p->price, 0, ',', '.') }}
                </p>
            </div>

            {{-- Stok --}}
            <div class="mt-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs uppercase font-semibold text-slate-500">
                        Stok Tersedia
                    </span>
                    <span class="font-bold text-lg {{ $p->stock <= 5 ? 'text-red-500' : 'text-slate-800' }}">
                        {{ $p->stock }}
                    </span>
                </div>

                {{-- FIX: stock bar pakai threshold 100 maksimum untuk visual, bukan actual max --}}
                @php $stockPercent = $p->stock > 0 ? min(($p->stock / max($p->stock, 100)) * 100, 100) : 0; @endphp
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div
                        class="h-full rounded-full transition-all duration-300
                        {{ $p->stock <= 5 ? 'bg-red-500' : ($p->stock <= 20 ? 'bg-yellow-400' : 'bg-green-500') }}"
                        style="width: {{ $stockPercent }}%">
                    </div>
                </div>
            </div>

            {{-- Tambah Stok --}}
            <form
                action="{{ route('admin.products.addStock', $p->id) }}"
                method="POST"
                class="flex gap-2 mt-5">
                @csrf
                <input
                    type="number"
                    name="stock"
                    min="1"
                    max="9999"
                    class="input flex-1"
                    placeholder="Tambah stok"
                    required>
                <button
                    type="submit"
                    class="btn btn-secondary">
                    + Stok
                </button>
            </form>

            {{-- Aksi --}}
            <div class="grid grid-cols-3 gap-2 mt-5">

                {{-- FIX: gunakan data-attribute, bukan string interpolasi di onclick --}}
                <button
                    class="btn btn-ghost btn-sm js-open-edit"
                    data-id="{{ $p->id }}">
                    Edit
                </button>

                <form
                    action="{{ route('admin.products.toggle', $p->id) }}"
                    method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-sm w-full"
                        style="
                            background:var(--orange-50);
                            color:var(--orange-700);
                            border-color:var(--orange-200);
                        ">
                        {{ $p->is_available ? 'Matikan' : 'Aktifkan' }}
                    </button>
                </form>

                <form
                    action="{{ route('admin.products.delete', $p->id) }}"
                    method="POST"
                    class="js-delete-form">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="btn btn-danger btn-sm w-full">
                        Hapus
                    </button>
                </form>

            </div>

        </div>

    </div>

    @empty

    <div class="col-span-full">
        <div class="empty-state">
            <div class="empty-icon">🍽️</div>
            <h3 class="font-bold text-lg">Belum Ada Produk</h3>
            <p class="text-slate-500">Tambahkan produk pertama untuk mulai berjualan.</p>
            <button
                onclick="openModal('add')"
                class="btn btn-primary mt-4">
                Tambah Produk
            </button>
        </div>
    </div>

    @endforelse

</div>

{{-- Modal: Tambah --}}
<div id="modal-add" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-add-title">
    <div class="modal">
        <div class="flex items-center justify-between mb-4">
            <h2 id="modal-add-title" class="modal-title">Tambah Produk</h2>
            <button onclick="closeModal('add')" class="btn btn-ghost btn-sm" style="padding:4px 8px; color:var(--gray-400);" aria-label="Tutup">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Produk <span style="color:var(--orange-500);">*</span></label>
                <input type="text" name="name" required class="input" placeholder="Contoh: Siomay Ayam" maxlength="255">
            </div>
            <div class="form-group">
                <label class="form-label">Harga (Rp) <span style="color:var(--orange-500);">*</span></label>
                <input type="number" name="price" required min="0" max="99999999" class="input" placeholder="15000">
            </div>
            <div class="form-group">
                <label class="form-label">Stok Awal <span style="color:var(--orange-500);">*</span></label>
                <input type="number" name="stock" required min="0" max="9999" class="input" placeholder="50">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi <span class="text-gray-300">(opsional)</span></label>
                <textarea name="description" class="textarea" rows="2" placeholder="Deskripsi singkat..." maxlength="500"></textarea>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeModal('add')" class="btn btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn btn-primary flex-1">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit --}}
<div id="modal-edit" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-edit-title">
    <div class="modal">
        <div class="flex items-center justify-between mb-4">
            <h2 id="modal-edit-title" class="modal-title">Edit Produk</h2>
            <button onclick="closeModal('edit')" class="btn btn-ghost btn-sm" style="padding:4px 8px; color:var(--gray-400);" aria-label="Tutup">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Produk <span style="color:var(--orange-500);">*</span></label>
                <input type="text" id="edit-name" name="name" required class="input" maxlength="255">
            </div>
            <div class="form-group">
                <label class="form-label">Harga (Rp) <span style="color:var(--orange-500);">*</span></label>
                <input type="number" id="edit-price" name="price" required min="0" max="99999999" class="input">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea id="edit-description" name="description" class="textarea" rows="2" maxlength="500"></textarea>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closeModal('edit')" class="btn btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn btn-primary flex-1">Update</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Fallback: pastikan modal tersembunyi saat load, tidak bergantung Tailwind saja */
.modal-backdrop { display: none !important; }
.modal-backdrop.modal-open { display: flex !important; }
</style>

<script>
// Jalankan langsung (tidak dalam DOMContentLoaded) agar fungsi tersedia saat onclick inline dipanggil
function openModal(type) {
    var el = document.getElementById('modal-' + type);
    if (!el) return;
    el.classList.add('modal-open');
    // Fokus ke input pertama
    var first = el.querySelector('input:not([type="hidden"]), textarea');
    if (first) setTimeout(function() { first.focus(); }, 50);
}

function closeModal(type) {
    var el = document.getElementById('modal-' + type);
    if (!el) return;
    el.classList.remove('modal-open');
}

// Pastikan semua modal tertutup saat halaman pertama load
document.addEventListener('DOMContentLoaded', function() {

    // Paksa tutup semua modal saat load (cegah modal muncul karena browser state restore)
    closeModal('add');
    closeModal('edit');

    // Tombol Edit: baca data dari data-* attribute (aman XSS)
    document.querySelectorAll('.js-open-edit').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var card = this.closest('[data-product-id]');
            if (!card) return;

            document.getElementById('edit-name').value        = card.dataset.productName        || '';
            document.getElementById('edit-price').value       = card.dataset.productPrice       || '';
            document.getElementById('edit-description').value = card.dataset.productDescription || '';
            document.getElementById('edit-form').action       = '/admin/products/' + card.dataset.productId;

            openModal('edit');
        });
    });

    // Konfirmasi hapus
    document.querySelectorAll('.js-delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var card = this.closest('[data-product-id]');
            var name = card ? (card.dataset.productName || 'produk ini') : 'produk ini';
            if (!confirm('Hapus produk "' + name + '"? Tindakan ini tidak bisa dibatalkan.')) {
                e.preventDefault();
            }
        });
    });

    // ESC tutup semua modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeModal('add'); closeModal('edit'); }
    });

    // Klik backdrop (area luar modal) tutup modal
    ['modal-add', 'modal-edit'].forEach(function(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('click', function(e) {
            if (e.target === this) closeModal(id.replace('modal-', ''));
        });
    });

});
</script>

@endsection