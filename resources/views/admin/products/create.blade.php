@extends('layouts.admin')

@section('title', 'Aggiungi Prodotto')
@section('page_title', 'Nuovo Prodotto')

@section('content')
<div style="max-width:900px;">
    <a href="{{ route('admin.products') }}" style="color:var(--green-dark);font-size:14px;display:inline-flex;align-items:center;gap:6px;margin-bottom:24px;">
        <i class="bi bi-arrow-left"></i> Torna ai Prodotti
    </a>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;margin-bottom:24px;">
            <ul style="margin:0;padding-left:20px;color:#dc2626;font-size:14px;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div class="row g-4">
            {{-- Left --}}
            <div class="col-lg-8">
                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Informazioni di base</h5>

                    {{-- Italian --}}
                    <div style="background:#f9fafb;border:1px solid #eee;border-radius:10px;padding:16px 18px;margin-bottom:16px;">
                        <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#888;margin-bottom:14px;">🇮🇹 Italiano</div>
                        <div class="mb-3">
                            <label class="form-label">Nome prodotto *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Descrizione</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- English --}}
                    <div style="background:#f0f7ff;border:1px solid #d0e8ff;border-radius:10px;padding:16px 18px;margin-bottom:16px;">
                        <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#5a8fc4;margin-bottom:14px;">🇬🇧 English</div>
                        <div class="mb-3">
                            <label class="form-label">Product name <span style="font-weight:400;color:#999;">(lascia vuoto per usare il nome italiano)</span></label>
                            <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Description</label>
                            <textarea name="description_en" class="form-control" rows="3">{{ old('description_en') }}</textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug (generato automaticamente se vuoto)</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="e.g. leather-collar-red">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku') }}">
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Prezzo e Giacenza</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Prezzo (€) *</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sconto (%)</label>
                            <input type="number" name="discount_percent" class="form-control" min="0" max="100" value="{{ old('discount_percent', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Giacenza *</label>
                            <input type="number" name="stock" class="form-control" min="0" value="{{ old('stock', 0) }}" required>
                        </div>
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Colori e taglie</h5>
                    <p style="font-size:13px;color:#888;margin:-12px 0 16px;">
                        I colori si gestiscono in <a href="{{ route('admin.colors') }}">Admin → Colori</a>.
                    </p>

                    @php
                        $sizeOptions = ['XS','S','M','L'];
                        $oldColors = old('colors', []);
                        $oldSizes  = old('sizes', []);
                        $colorOptions = $colorOptions ?? [];
                    @endphp

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Colori disponibili</label>
                        @if(empty($colorOptions))
                            <p style="font-size:13px;color:#888;margin:8px 0 0;">
                                Nessun colore attivo. <a href="{{ route('admin.colors') }}">Aggiungi colori</a> prima di assegnarli al prodotto.
                            </p>
                        @endif
                        <div class="d-flex gap-4 flex-wrap mt-1" id="colorCheckboxes">
                            @foreach($colorOptions as $colorName => $colorHex)
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                                    <input type="checkbox" name="colors[]" value="{{ $colorName }}"
                                           data-color="{{ $colorName }}"
                                           class="color-cb"
                                           onchange="toggleColorImg(this)"
                                           {{ in_array($colorName, $oldColors) ? 'checked' : '' }}>
                                    <span style="width:20px;height:20px;border-radius:50%;background:{{ $colorHex }};border:1px solid #ddd;display:inline-block;flex-shrink:0;"></span>
                                    {{ $colorName }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Per-color image uploads --}}
                    <div id="colorImgUploads">
                        @foreach($colorOptions as $colorName => $colorHex)
                            @php $slugCreate = Str::slug($colorName); @endphp
                            <div id="colorImg_{{ $slugCreate }}"
                                 style="display:{{ in_array($colorName, $oldColors) ? 'block' : 'none' }};background:#f9f9f9;border:1px solid #eee;border-radius:8px;padding:14px;margin-bottom:10px;">
                                <label class="form-label" style="font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                                    <span style="width:14px;height:14px;border-radius:50%;background:{{ $colorHex }};display:inline-block;"></span>
                                    Foto per {{ $colorName }}
                                </label>
                                <div id="createColorPreviews_{{ $slugCreate }}" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;"></div>
                                <input type="file" name="color_images[{{ $colorName }}][]" class="form-control form-control-sm" accept="image/*" multiple
                                       onchange="previewCreateColorImgs(this, '{{ $slugCreate }}')">
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Taglie disponibili</label>
                        <div class="d-flex gap-4 flex-wrap mt-1">
                            @foreach($sizeOptions as $size)
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                                    <input type="checkbox" name="sizes[]" value="{{ $size }}"
                                           {{ in_array($size, $oldSizes) ? 'checked' : '' }}>
                                    <span style="min-width:32px;height:28px;padding:0 8px;border:1.5px solid #ddd;border-radius:4px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;justify-content:center;">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Immagine aggiuntiva</h5>
                    <label class="form-label">Carica immagini extra (facoltativo)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    <p style="font-size:12px;color:#999;margin-top:6px;">Le foto dei colori vengono aggiunte automaticamente. Qui puoi aggiungerne altre.</p>
                </div>
            </div>

            {{-- Right --}}
            <div class="col-lg-4">
                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Organizzazione</h5>
                    <div class="mb-3">
                        <label class="form-label">Categoria *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Seleziona categoria</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;margin-bottom:20px;">
                    <h5 style="font-weight:700;margin-bottom:20px;">Visibilità</h5>
                    <div class="mb-3">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active',1)?'checked':'' }}> Attivo (visibile nel negozio)
                        </label>
                    </div>
                    <div class="mb-3">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured')?'checked':'' }}> Prodotto in evidenza
                        </label>
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                            <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller')?'checked':'' }}> Best seller
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn-admin-primary w-100" style="padding:14px;font-size:15px;justify-content:center;">
                    <i class="bi bi-check-lg"></i> Salva Prodotto
                </button>
            </div>
        </div>
    </form>
</div>

<script>
const _createFilesMap = {}; // slug -> { files: File[], input: HTMLInputElement }

function toggleColorImg(cb) {
    const slug = cb.dataset.color
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');
    const box = document.getElementById('colorImg_' + slug);
    if (box) box.style.display = cb.checked ? 'block' : 'none';
    if (!cb.checked && box) {
        const fi = box.querySelector('input[type=file]');
        if (fi) fi.value = '';
        const previews = document.getElementById('createColorPreviews_' + slug);
        if (previews) previews.innerHTML = '';
        delete _createFilesMap[slug];
    }
}

function previewCreateColorImgs(input, slug) {
    if (!_createFilesMap[slug]) _createFilesMap[slug] = { files: [], input };
    Array.from(input.files).forEach(f => _createFilesMap[slug].files.push(f));
    _rebuildCreatePreview(slug);
}

function _rebuildCreatePreview(slug) {
    const state = _createFilesMap[slug];
    if (!state) return;
    const { files, input } = state;
    // Sync input.files via DataTransfer so the form submits all accumulated files
    const dt = new DataTransfer();
    files.forEach(f => dt.items.add(f));
    input.files = dt.files;
    // Rebuild preview UI
    const container = document.getElementById('createColorPreviews_' + slug);
    if (!container) return;
    container.innerHTML = '';
    files.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;display:inline-block;';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:72px;height:72px;object-fit:cover;border-radius:6px;border:1px solid #ddd;';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = '&times;';
            btn.style.cssText = 'position:absolute;top:-7px;right:-7px;background:#e53935;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:11px;cursor:pointer;line-height:1;padding:0;';
            btn.onclick = () => { _createFilesMap[slug].files.splice(idx, 1); _rebuildCreatePreview(slug); };
            wrap.appendChild(img);
            wrap.appendChild(btn);
            container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endsection

