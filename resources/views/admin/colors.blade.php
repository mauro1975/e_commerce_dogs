@extends('layouts.admin')

@section('title', 'Colori')
@section('page_title', 'Colori prodotto')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:28px;position:sticky;top:80px;">
            <h5 style="font-weight:700;margin-bottom:20px;" id="formTitle">Aggiungi colore</h5>
            <form action="{{ route('admin.colors.store') }}" method="POST" id="colorForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                @if($errors->any())
                <div style="background:#fef2f2;border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px;color:#dc2626;">
                    {{ $errors->first() }}
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Nome (IT) *</label>
                    <input type="text" name="name" id="colorName" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Name (EN)</label>
                    <input type="text" name="name_en" id="colorNameEn" class="form-control" value="{{ old('name_en') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Codice colore *</label>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <input type="color" id="colorPicker" value="{{ old('hex', '#9BC3B1') }}" style="width:48px;height:42px;border:none;padding:0;cursor:pointer;">
                        <input type="text" name="hex" id="colorHex" class="form-control" value="{{ old('hex', '#9BC3B1') }}"
                               pattern="^#[0-9A-Fa-f]{6}$" placeholder="#9BC3B1" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ordinamento</label>
                    <input type="number" name="sort_order" id="colorSort" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                </div>
                <div class="mb-4">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                        <input type="checkbox" name="is_active" id="colorActive" value="1" checked> Attivo
                    </label>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn-admin-primary flex-fill" style="justify-content:center;padding:12px;">
                        <i class="bi bi-check-lg"></i> <span id="submitLabel">Aggiungi colore</span>
                    </button>
                    <button type="button" onclick="resetColorForm()" class="btn-admin-secondary" style="padding:12px 16px;">Reimposta</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <p style="font-size:14px;color:#666;margin-bottom:16px;">
            I colori attivi compaiono nella creazione e modifica prodotti. Nome e codice esadecimale vengono salvati su ogni prodotto.
        </p>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Colore</th>
                    <th>Hex</th>
                    <th>EN</th>
                    <th>Stato</th>
                    <th>Ordine</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse($colors as $color)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span style="width:28px;height:28px;border-radius:50%;background:{{ $color->hex }};border:1px solid #ddd;flex-shrink:0;"></span>
                            <strong>{{ $color->name }}</strong>
                        </div>
                    </td>
                    <td><code>{{ $color->hex }}</code></td>
                    <td style="color:#888;">{{ $color->name_en ?: '—' }}</td>
                    <td>
                        @if($color->is_active)
                            <span class="badge-status" style="background:#f0fdf4;color:#15803d;">Attivo</span>
                        @else
                            <span class="badge-status" style="background:#f3f4f6;color:#666;">Disattivo</span>
                        @endif
                    </td>
                    <td>{{ $color->sort_order }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button type="button" class="btn-admin-secondary" style="padding:6px 10px;font-size:12px;"
                                    onclick="editColor({{ $color->id }}, '{{ addslashes($color->name) }}', '{{ addslashes($color->name_en ?? '') }}', '{{ $color->hex }}', {{ $color->sort_order }}, {{ $color->is_active ? 1 : 0 }}, '{{ route('admin.colors.update', $color) }}')">
                                Modifica
                            </button>
                            <form action="{{ route('admin.colors.delete', $color) }}" method="POST" onsubmit="return confirm('Eliminare {{ addslashes($color->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-admin-danger" style="padding:6px 10px;font-size:12px;">Elimina</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:#999;">
                        Nessun colore. Esegui <code>php artisan db:seed --class=ColorSeeder</code> o aggiungine uno.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
const picker = document.getElementById('colorPicker');
const hexInput = document.getElementById('colorHex');
picker.addEventListener('input', () => { hexInput.value = picker.value.toUpperCase(); });
hexInput.addEventListener('input', () => {
    if (/^#[0-9A-Fa-f]{6}$/.test(hexInput.value)) picker.value = hexInput.value;
});

function resetColorForm() {
    document.getElementById('colorForm').action = '{{ route('admin.colors.store') }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formTitle').textContent = 'Aggiungi colore';
    document.getElementById('submitLabel').textContent = 'Aggiungi colore';
    document.getElementById('colorName').value = '';
    document.getElementById('colorNameEn').value = '';
    hexInput.value = '#9BC3B1';
    picker.value = '#9BC3B1';
    document.getElementById('colorSort').value = '0';
    document.getElementById('colorActive').checked = true;
}

function editColor(id, name, nameEn, hex, sort, active, updateUrl) {
    document.getElementById('colorForm').action = updateUrl;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formTitle').textContent = 'Modifica colore';
    document.getElementById('submitLabel').textContent = 'Salva modifiche';
    document.getElementById('colorName').value = name;
    document.getElementById('colorNameEn').value = nameEn;
    hexInput.value = hex;
    picker.value = hex;
    document.getElementById('colorSort').value = sort;
    document.getElementById('colorActive').checked = !!active;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endsection
