@extends('layouts.admin')

@section('title', 'Modifica Prodotto')
@section('page_title', 'Modifica: ' . $product->name)

@section('content')
<div style="max-width:900px;">
    <a href="{{ route('admin.products') }}" style="color:var(--green-dark);font-size:14px;display:inline-flex;align-items:center;gap:6px;margin-bottom:24px;">
        <i class="bi bi-arrow-left"></i> Torna ai Prodotti
    </a>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;margin-bottom:24px;">
            <ul style="margin:0;padding-left:20px;color:#dc2626;font-size:14px;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Informazioni di base</h5>

                    {{-- Italian --}}
                    <div style="background:#f9fafb;border:1px solid #eee;border-radius:10px;padding:16px 18px;margin-bottom:16px;">
                        <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#888;margin-bottom:14px;">🇮🇹 Italiano</div>
                        <div class="mb-3">
                            <label class="form-label">Nome prodotto *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->getRawOriginal('name')) }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Descrizione</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $product->getRawOriginal('description')) }}</textarea>
                        </div>
                    </div>

                    {{-- English --}}
                    <div style="background:#f0f7ff;border:1px solid #d0e8ff;border-radius:10px;padding:16px 18px;margin-bottom:16px;">
                        <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#5a8fc4;margin-bottom:14px;">🇬🇧 English</div>
                        <div class="mb-3">
                            <label class="form-label">Product name <span style="font-weight:400;color:#999;">(lascia vuoto per usare il nome italiano)</span></label>
                            <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $product->getRawOriginal('name_en')) }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Description</label>
                            <textarea name="description_en" class="form-control" rows="3">{{ old('description_en', $product->getRawOriginal('description_en')) }}</textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $product->slug) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Prezzo e Giacenza</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Prezzo (€) *</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sconto (%)</label>
                            <input type="number" name="discount_percent" class="form-control" min="0" max="100" value="{{ old('discount_percent', $product->discount_percent) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giacenza *</label>
                            <input type="number" name="stock" class="form-control" min="0" value="{{ old('stock', $product->stock) }}" required>
                        </div>
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:8px;">Foto prodotto</h5>
                    <p style="font-size:13px;color:#888;margin-bottom:20px;">
                        Galleria principale del prodotto (homepage, elenco, scheda). Clicca × per eliminare; salva con «Aggiorna Prodotto».
                    </p>
                    @php
                        $productGalleryImages = is_array($product->images)
                            ? $product->images
                            : (json_decode($product->images ?? '[]', true) ?? []);
                    @endphp

                    @if(!empty($productGalleryImages))
                    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px;">
                        @foreach($productGalleryImages as $gIdx => $galleryPath)
                        <div style="position:relative;" id="productImgCard_{{ $gIdx }}">
                            <img src="{{ asset($galleryPath) }}" alt=""
                                 style="width:96px;height:96px;object-fit:cover;border-radius:8px;border:1px solid #ddd;transition:opacity .2s,filter .2s;">
                            <button type="button"
                                    style="position:absolute;top:-8px;right:-8px;background:#e53935;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;line-height:1;"
                                    onclick="markDeleteProductImg(this, {{ $gIdx }})"
                                    title="Elimina">&times;</button>
                            <input type="checkbox"
                                   name="delete_product_images[]"
                                   value="{{ $galleryPath }}"
                                   style="display:none;"
                                   id="delProductImg_{{ $gIdx }}">
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="font-size:13px;color:#999;margin-bottom:16px;">Nessuna foto caricata.</p>
                    @endif

                    <div id="productNewPreviews" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;"></div>
                    <label class="form-label">Aggiungi foto</label>
                    <input type="file" name="product_images[]" id="productImagesInput"
                           class="form-control" accept="image/*" multiple
                           onchange="previewProductImgs(this)">
                </div>

                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Colors & Sizes</h5>
                    @php
                        $currentColors = is_array($product->available_colors)
                            ? $product->available_colors
                            : (json_decode($product->available_colors ?? '[]', true) ?? []);
                        $currentColorNames = old('colors', array_map(fn($c) => is_array($c) ? $c['name'] : $c, $currentColors));
                        $currentColorMap   = [];
                        foreach ($currentColors as $cc) {
                            if (is_array($cc)) $currentColorMap[$cc['name']] = $cc;
                        }
                        $currentSizes = old('sizes', is_array($product->available_sizes)
                            ? $product->available_sizes
                            : (json_decode($product->available_sizes ?? '[]', true) ?? []));
                        $colorOptions = [
                            'Arancione' => '#E8832A',
                            'Rosa'      => '#F4A7B9',
                            'Verde'     => '#9BC3B1',
                            'Marrone'   => '#8B5E3C',
                        ];
                        $sizeOptions = ['XS', 'S', 'M', 'L'];
                    @endphp

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Colori disponibili</label>
                        <div class="d-flex gap-4 flex-wrap mt-1" id="editColorCheckboxes">
                            @foreach($colorOptions as $colorName => $colorHex)
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                                    <input type="checkbox" name="colors[]" value="{{ $colorName }}"
                                           class="edit-color-cb"
                                           data-color="{{ $colorName }}"
                                           onchange="editToggleColorImg(this)"
                                           {{ in_array($colorName, $currentColorNames) ? 'checked' : '' }}>
                                    <span style="width:18px;height:18px;border-radius:50%;background:{{ $colorHex }};border:1px solid #ddd;display:inline-block;"></span>
                                    {{ $colorName }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Per-color photo management --}}
                    <div id="editColorImgRows">
                        @foreach($colorOptions as $colorName => $colorHex)
                            @php
                                $existing     = $currentColorMap[$colorName] ?? null;
                                $existingImgs = !empty($existing['images'])
                                    ? $existing['images']
                                    : (!empty($existing['image']) ? [$existing['image']] : []);
                                $isChecked    = in_array($colorName, $currentColorNames);
                                $slugName     = Str::slug($colorName);
                            @endphp
                            <div id="editColorRow_{{ $slugName }}"
                                 style="display:{{ $isChecked ? 'block' : 'none' }};background:#f9f9f9;border:1px solid #eee;border-radius:8px;padding:14px;margin-bottom:10px;">
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                                    <span style="width:14px;height:14px;border-radius:50%;background:{{ $colorHex }};display:inline-block;"></span>
                                    <strong style="font-size:13px;">{{ $colorName }}</strong>
                                </div>

                                {{-- Existing images grid --}}
                                @if(!empty($existingImgs))
                                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                                    @foreach($existingImgs as $idx => $existImg)
                                    <div style="position:relative;" id="imgCard_{{ $slugName }}_{{ $idx }}">
                                        <img src="{{ asset($existImg) }}" alt="{{ $colorName }}"
                                             style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd;transition:opacity .2s,filter .2s;">
                                        <button type="button"
                                                style="position:absolute;top:-7px;right:-7px;background:#e53935;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;line-height:1;"
                                                onclick="markDeleteImg(this,'{{ $slugName }}','{{ $idx }}')"
                                                title="Elimina">&times;</button>
                                        <input type="checkbox"
                                               name="delete_color_images[{{ $colorName }}][]"
                                               value="{{ $existImg }}"
                                               style="display:none;"
                                               id="delCheck_{{ $slugName }}_{{ $idx }}">
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Preview newly selected files --}}
                                <div id="editColorNewPreviews_{{ $slugName }}" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;"></div>

                                {{-- Upload more --}}
                                <label style="font-size:12px;color:#555;display:block;margin-bottom:4px;">
                                    {{ empty($existingImgs) ? 'Carica foto' : 'Aggiungi altre foto' }}
                                </label>
                                <input type="file" name="color_images[{{ $colorName }}][]"
                                       class="form-control form-control-sm" accept="image/*" multiple
                                       onchange="previewEditColorImgs(this, '{{ $slugName }}')">
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Taglie disponibili</label>
                        <div class="d-flex gap-4 flex-wrap mt-1">
                            @foreach($sizeOptions as $size)
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                                    <input type="checkbox" name="sizes[]" value="{{ $size }}"
                                           {{ in_array($size, $currentSizes) ? 'checked' : '' }}>
                                    <span style="min-width:32px;height:28px;padding:0 8px;border:1.5px solid #ddd;border-radius:4px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;justify-content:center;">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Organizzazione</h5>
                    <div class="mb-3">
                        <label class="form-label">Categoria *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Seleziona categoria</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id',$product->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Visibilità</h5>
                    <div class="mb-3">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active',$product->is_active)?'checked':'' }}> Attivo
                        </label>
                    </div>
                    <div class="mb-3">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured',$product->is_featured)?'checked':'' }}> In evidenza
                        </label>
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                            <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller',$product->is_best_seller)?'checked':'' }}> Best Seller
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn-admin-primary w-100" style="padding:14px;font-size:15px;justify-content:center;">
                    <i class="bi bi-check-lg"></i> Aggiorna Prodotto
                </button>
                <a href="{{ route('product.show', $product->slug) }}" target="_blank"
                   class="btn-admin-secondary w-100 mt-2" style="padding:12px;justify-content:center;">
                    <i class="bi bi-eye"></i> Anteprima
                </a>
            </div>
        </div>
    </form>
</div>

<script>
function editToggleColorImg(cb) {
    const slug = cb.dataset.color.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
    const row = document.getElementById('editColorRow_' + slug);
    if (row) row.style.display = cb.checked ? 'block' : 'none';
}
const _editFilesMap = {}; // slug -> { files: File[], input: HTMLInputElement }

function previewEditColorImgs(input, slug) {
    if (!_editFilesMap[slug]) _editFilesMap[slug] = { files: [], input };
    Array.from(input.files).forEach(f => _editFilesMap[slug].files.push(f));
    _rebuildEditPreview(slug);
}

function _rebuildEditPreview(slug) {
    const state = _editFilesMap[slug];
    if (!state) return;
    const { files, input } = state;
    // Sync input.files via DataTransfer so the form submits all accumulated files
    const dt = new DataTransfer();
    files.forEach(f => dt.items.add(f));
    input.files = dt.files;
    // Rebuild preview UI
    const container = document.getElementById('editColorNewPreviews_' + slug);
    if (!container) return;
    container.innerHTML = '';
    files.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;display:inline-block;';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:72px;height:72px;object-fit:cover;border-radius:6px;border:2px solid #9BC3B1;';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = '&times;';
            btn.style.cssText = 'position:absolute;top:-7px;right:-7px;background:#e53935;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:11px;cursor:pointer;line-height:1;padding:0;';
            btn.onclick = () => { _editFilesMap[slug].files.splice(idx, 1); _rebuildEditPreview(slug); };
            wrap.appendChild(img);
            wrap.appendChild(btn);
            container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}
function markDeleteImg(btn, slug, idx) {
    const cb  = document.getElementById('delCheck_' + slug + '_' + idx);
    const img = btn.previousElementSibling;
    if (cb.checked) {
        cb.checked = false;
        btn.style.background = '#e53935';
        img.style.opacity = '1';
        img.style.filter  = '';
    } else {
        cb.checked = true;
        btn.style.background = '#aaa';
        img.style.opacity = '0.35';
        img.style.filter  = 'grayscale(1)';
    }
}

const _productFilesState = { files: [], input: null };

function previewProductImgs(input) {
    _productFilesState.input = input;
    Array.from(input.files).forEach(f => _productFilesState.files.push(f));
    rebuildProductPreview();
}

function rebuildProductPreview() {
    const { files, input } = _productFilesState;
    if (input) {
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
    }
    const container = document.getElementById('productNewPreviews');
    if (!container) return;
    container.innerHTML = '';
    files.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;display:inline-block;';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:72px;height:72px;object-fit:cover;border-radius:6px;border:2px solid #9BC3B1;';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = '&times;';
            btn.style.cssText = 'position:absolute;top:-7px;right:-7px;background:#e53935;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:11px;cursor:pointer;line-height:1;padding:0;';
            btn.onclick = () => { _productFilesState.files.splice(idx, 1); rebuildProductPreview(); };
            wrap.appendChild(img);
            wrap.appendChild(btn);
            container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}

function markDeleteProductImg(btn, idx) {
    const cb  = document.getElementById('delProductImg_' + idx);
    const img = btn.previousElementSibling;
    if (!cb || !img) return;
    if (cb.checked) {
        cb.checked = false;
        btn.style.background = '#e53935';
        img.style.opacity = '1';
        img.style.filter  = '';
    } else {
        cb.checked = true;
        btn.style.background = '#aaa';
        img.style.opacity = '0.35';
        img.style.filter  = 'grayscale(1)';
    }
}
</script>
@endsection

