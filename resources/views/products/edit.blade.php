@extends('adminlte::page')

@section('title', 'Edit Produk')

@include('partials.premium-ui-styles')

@section('content_header')
    <div class="premium-shell">
        <h1>Edit Produk</h1>
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="premium-shell">
        <div class="card premium-form-card card-outline card-warning">
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body row">
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}"
                            required>
                    </div>
                    <div class="form-group col-12 col-md-8">
                        <label class="premium-form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}"
                            required>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">-</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Harga Modal</label>
                        <input type="number" step="0.01" name="cost_price" class="form-control"
                            value="{{ old('cost_price', (float) $product->cost_price) }}" required>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Harga Jual</label>
                        <input type="number" step="0.01" name="sell_price" class="form-control"
                            value="{{ old('sell_price', (float) $product->sell_price) }}" required>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Stok</label>
                        <input type="number" name="stock" class="form-control"
                            value="{{ old('stock', $product->stock) }}" required>
                    </div>
                    <div class="form-group col-12 col-md-8">
                        <label class="premium-form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- Image Upload --}}
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Gambar Produk</label>

                        {{-- Current image --}}
                        @if ($product->image)
                            <div id="img-current-wrap" class="mb-2">
                                <img src="{{ Storage::disk('public')->url($product->image) }}"
                                    id="img-preview"
                                    alt="{{ $product->name }}"
                                    style="max-width:100%;max-height:200px;border-radius:8px;border:1.5px solid #dee2e6;object-fit:contain;">
                                <div class="mt-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="remove_image"
                                            name="remove_image" value="1"
                                            onchange="toggleRemoveImage(this)">
                                        <label class="custom-control-label text-danger small" for="remove_image">
                                            Hapus gambar ini
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div id="img-preview-wrap" class="mb-2" style="display:none">
                                <img id="img-preview" src="" alt="Preview"
                                    style="max-width:100%;max-height:200px;border-radius:8px;border:1.5px solid #dee2e6;object-fit:contain;">
                            </div>
                        @endif

                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="product_image" name="image"
                                accept="image/jpg,image/jpeg,image/png,image/webp">
                            <label class="custom-file-label" for="product_image">Ganti gambar...</label>
                        </div>
                        <small class="text-muted">JPG, PNG, WebP. Maks 2 MB.</small>
                        @error('image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <div class="premium-switch-wrap custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                value="1" @checked(old('is_active', $product->is_active))>
                            <label class="custom-control-label" for="is_active">Produk aktif</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn premium-btn-warning"><i class="fas fa-save mr-1"></i>Perbarui</button>
                    <a href="{{ route('products.index') }}" class="btn premium-btn-secondary"><i
                            class="fas fa-arrow-left mr-1"></i>Kembali</a>
                </div>
            </form>
        </div>
    </div>
@stop

@push('js')
<script>
    document.getElementById('product_image').addEventListener('change', function () {
        const file = this.files[0];
        const label = this.nextElementSibling;
        const preview = document.getElementById('img-preview');
        const newWrap = document.getElementById('img-preview-wrap');
        if (file) {
            label.textContent = file.name;
            const reader = new FileReader();
            reader.onload = e => {
                if (preview) {
                    preview.src = e.target.result;
                    if (newWrap) newWrap.style.display = 'block';
                }
            };
            reader.readAsDataURL(file);
        } else {
            label.textContent = 'Ganti gambar...';
        }
    });

    function toggleRemoveImage(cb) {
        const fileInput = document.getElementById('product_image');
        const imgPreview = document.getElementById('img-preview');
        if (cb.checked) {
            if (imgPreview) imgPreview.style.opacity = '0.3';
            fileInput.disabled = true;
        } else {
            if (imgPreview) imgPreview.style.opacity = '1';
            fileInput.disabled = false;
        }
    }
</script>
@endpush
