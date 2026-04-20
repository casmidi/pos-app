@extends('adminlte::page')

@section('title', 'Tambah Produk')

@include('partials.premium-ui-styles')

@section('content_header')
    <div class="premium-shell">
        <h1>Tambah Produk</h1>
    </div>
@stop

@section('content')
    @include('partials.flash')

    <div class="premium-shell">
        <div class="card premium-form-card card-outline card-primary">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body row">
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" required>
                    </div>
                    <div class="form-group col-12 col-md-8">
                        <label class="premium-form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">-</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Harga Modal</label>
                        <input type="number" step="0.01" name="cost_price" class="form-control"
                            value="{{ old('cost_price', 0) }}" required>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Harga Jual</label>
                        <input type="number" step="0.01" name="sell_price" class="form-control"
                            value="{{ old('sell_price', 0) }}" required>
                    </div>
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Stok</label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" required>
                    </div>
                    <div class="form-group col-12 col-md-8">
                        <label class="premium-form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>

                    {{-- Image Upload --}}
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Gambar Produk</label>
                        <div class="product-img-wrap mb-2" id="img-preview-wrap" style="display:none">
                            <img id="img-preview" src="" alt="Preview"
                                style="max-width:100%;max-height:200px;border-radius:8px;border:1.5px solid #dee2e6;object-fit:contain;">
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="product_image" name="image"
                                accept="image/jpg,image/jpeg,image/png,image/webp">
                            <label class="custom-file-label" for="product_image">Pilih gambar...</label>
                        </div>
                        <small class="text-muted">JPG, PNG, WebP. Maks 2 MB.</small>
                        @error('image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Image Upload --}}
                    <div class="form-group col-12 col-md-4">
                        <label class="premium-form-label">Gambar Produk</label>
                        <div class="product-img-wrap mb-2" id="img-preview-wrap" style="display:none">
                            <img id="img-preview" src="" alt="Preview"
                                style="max-width:100%;max-height:200px;border-radius:8px;border:1.5px solid #dee2e6;object-fit:contain;">
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="product_image" name="image"
                                accept="image/jpg,image/jpeg,image/png,image/webp">
                            <label class="custom-file-label" for="product_image">Pilih gambar...</label>
                        </div>
                        <small class="text-muted">JPG, PNG, WebP. Maks 2 MB.</small>
                        @error('image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-12">
                        <div class="premium-switch-wrap custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                value="1" @checked(old('is_active', true))>
                            <label class="custom-control-label" for="is_active">Produk aktif</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn premium-btn-primary"><i class="fas fa-floppy-disk mr-1"></i>Simpan</button>
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
        const wrap = document.getElementById('img-preview-wrap');
        const preview = document.getElementById('img-preview');
        const label = this.nextElementSibling;
        if (file) {
            label.textContent = file.name;
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                wrap.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            wrap.style.display = 'none';
            label.textContent = 'Pilih gambar...';
        }
    });
</script>
@endpush

@push('js')
<script>
    document.getElementById('product_image').addEventListener('change', function () {
        const file = this.files[0];
        const wrap = document.getElementById('img-preview-wrap');
        const preview = document.getElementById('img-preview');
        const label = this.nextElementSibling;
        if (file) {
            label.textContent = file.name;
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                wrap.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            wrap.style.display = 'none';
            label.textContent = 'Pilih gambar...';
        }
    });
</script>
@endpush
