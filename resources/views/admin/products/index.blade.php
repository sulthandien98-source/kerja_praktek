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

@if(session('success'))
<div class="alert alert-success mb-4" x-data x-init="setTimeout(() => $el.remove(), 4000)">
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

    <div class="card card-hover"
         data-product-id="{{ $p->id }}"
         data-product-name="{{ $p->name }}"
         data-product-price="{{ $p->price }}"
         data-product-stock="{{ $p->stock }}"
         data-product-description="{{ $p->description ?? '' }}">

        <div class="card-p">

            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="font-bold text-lg text-slate-800 truncate">{{ $p->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1">ID #{{ $p->id }}</p>
                </div>
                <span class="badge {{ $p->is_available ? 'badge-green' : 'badge-red' }} flex-shrink-0">
                    {{ $p->is_available ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            @if($p->image_url)
            <div class="mt-3 rounded-xl overflow-hidden" style="height:120px;background:var(--gray-50);">
                <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
                     class="w-full h-full object-cover"
                     loading="lazy">
            </div>
            @else
            <div class="mt-3 rounded-xl flex items-center justify-center text-4xl"
                 style="height:80px;background:var(--orange-50);">
                🥟
            </div>
            @endif

            @if($p->description)
            <p class="text-sm text-slate-500 mt-3 line-clamp-2">{{ $p->description }}</p>
            @endif

            <div class="mt-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Harga</p>
                <p class="text-2xl font-extrabold text-orange-600">
                    Rp {{ number_format($p->price, 0, ',', '.') }}
                </p>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs uppercase font-semibold text-slate-500">Stok</span>
                    <span class="font-bold text-lg {{ $p->stock <= 5 ? 'text-red-500' : 'text-slate-800' }}">
                        {{ $p->stock }}
                    </span>
                </div>
                @php $stockPercent = $p->stock > 0 ? min(($p->stock / max($p->stock, 100)) * 100, 100) : 0; @endphp
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300
                        {{ $p->stock <= 5 ? 'bg-red-500' : ($p->stock <= 20 ? 'bg-yellow-400' : 'bg-green-500') }}"
                         style="width: {{ $stockPercent }}%">
                    </div>
                </div>
                @if($p->stock <= 5)
                <p class="text-xs text-red-500 font-semibold mt-1">
                    {{ $p->stock === 0 ? 'Stok habis!' : 'Stok menipis!' }}
                </p>
                @endif
            </div>

            <form action="{{ route('admin.products.addStock', $p->id) }}" method="POST" class="flex gap-2 mt-4">
                @csrf
                <input type="number" name="stock" min="1" max="9999"
                       class="input flex-1" placeholder="Tambah stok" required>
                <button type="submit" class="btn btn-secondary">+ Stok</button>
            </form>

            <div class="grid grid-cols-3 gap-2 mt-4">

                <button class="btn btn-ghost btn-sm js-open-edit" data-id="{{ $p->id }}">
                    Edit
                </button>

                <form action="{{ route('admin.products.toggle', $p->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm w-full"
                            style="background:var(--orange-50);color:var(--orange-700);border:1px solid var(--orange-100);">
                        {{ $p->is_available ? 'Nonaktif' : 'Aktifkan' }}
                    </button>
                </form>

                <form action="{{ route('admin.products.delete', $p->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-full js-confirm"
                            data-message="Hapus produk '{{ addslashes($p->name) }}'? Tindakan ini tidak bisa dibatalkan.">
                        Hapus
                    </button>
                </form>

            </div>
        </div>
    </div>

    @empty
    <div class="col-span-3 empty-state">
        <div class="empty-icon">🥟</div>
        <p class="empty-title">Belum ada produk</p>
        <p class="empty-desc">Tambahkan produk pertama kamu</p>
        <button onclick="openModal('add')" class="btn btn-primary mt-3">Tambah Produk</button>
    </div>
    @endforelse

</div>

{{-- Modal Add --}}
<div id="modal-add"
     class="modal-backdrop hidden"
     onclick="if(event.target===this)closeModal('add')"
     role="dialog" aria-modal="true" aria-label="Tambah Produk">
    <div class="modal" style="max-width:480px;">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-extrabold text-gray-900">Tambah Produk</h2>
            <button onclick="closeModal('add')" type="button" class="btn btn-ghost btn-sm" style="padding:6px;" aria-label="Tutup">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-4" novalidate>
            @csrf

            <div class="form-group">
                <label class="form-label" for="add-name">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" id="add-name" name="name" class="input" required maxlength="100" placeholder="Dimsum Ayam, Siomay, ...">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="form-group">
                    <label class="form-label" for="add-price">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="add-price" name="price" class="input" required min="0" max="99999999" placeholder="15000">
                </div>
                <div class="form-group">
                    <label class="form-label" for="add-stock">Stok <span class="text-red-500">*</span></label>
                    <input type="number" id="add-stock" name="stock" class="input" required min="0" max="99999" placeholder="50">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="add-description">Deskripsi</label>
                <textarea id="add-description" name="description" class="textarea" rows="2" maxlength="500" placeholder="Dimsum lezat..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="add-image">Foto Produk</label>
                <input type="file" id="add-image" name="image" class="input" accept="image/jpeg,image/png,image/webp" style="padding:8px;">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP &middot; Maks 2MB</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('add')" class="btn btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn btn-primary flex-1">Tambah Produk</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit"
     class="modal-backdrop hidden"
     onclick="if(event.target===this)closeModal('edit')"
     role="dialog" aria-modal="true" aria-label="Edit Produk">
    <div class="modal" style="max-width:480px;">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-extrabold text-gray-900">Edit Produk</h2>
            <button onclick="closeModal('edit')" type="button" class="btn btn-ghost btn-sm" style="padding:6px;" aria-label="Tutup">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="edit-form" method="POST" action="" enctype="multipart/form-data" class="space-y-4" novalidate>
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="edit-name">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" id="edit-name" name="name" class="input" required maxlength="100">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="form-group">
                    <label class="form-label" for="edit-price">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="edit-price" name="price" class="input" required min="0" max="99999999">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-stock">Stok <span class="text-red-500">*</span></label>
                    <input type="number" id="edit-stock" name="stock" class="input" required min="0" max="99999">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit-description">Deskripsi</label>
                <textarea id="edit-description" name="description" class="textarea" rows="2" maxlength="500"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit-image">Ganti Foto Produk</label>
                <input type="file" id="edit-image" name="image" class="input" accept="image/jpeg,image/png,image/webp" style="padding:8px;">
                <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti foto</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('edit')" class="btn btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn btn-primary flex-1">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(type) {
    document.getElementById('modal-' + type).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(type) {
    document.getElementById('modal-' + type).classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal('add');
        closeModal('edit');
    }
});

document.querySelectorAll('.js-open-edit').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var card = this.closest('[data-product-id]');
        var id   = card.dataset.productId;
        var name = card.dataset.productName;
        var price = card.dataset.productPrice;
        var stock = card.dataset.productStock;
        var desc  = card.dataset.productDescription;

        document.getElementById('edit-name').value        = name;
        document.getElementById('edit-price').value       = price;
        document.getElementById('edit-stock').value       = stock;
        document.getElementById('edit-description').value = desc;
        document.getElementById('edit-form').action       = '/admin/products/' + id;

        openModal('edit');
    });
});

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