@extends('layouts.admin')

@section('title', 'Misure collari')
@section('page_title', 'Misure taglie — Collari')

@section('content')
<div style="max-width:760px;">
    <p style="font-size:14px;color:#666;margin-bottom:24px;">
        Queste misure compaiono nel menu taglie dei prodotti nella categoria <strong>Collari</strong>
        e nel selettore sulla scheda prodotto.
    </p>

    @if(session('success'))
        <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:#047857;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;margin-bottom:20px;">
            <ul style="margin:0;padding-left:20px;color:#dc2626;font-size:14px;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.collar-sizes.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div style="background:#fff;border:1px solid #eee;border-radius:16px;padding:24px;margin-bottom:20px;">
            <div style="display:grid;grid-template-columns:72px 1fr 1fr 100px 56px;gap:10px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:12px;">
                <span>Taglia</span>
                <span>Lunghezza min (cm)</span>
                <span>Lunghezza max (cm)</span>
                <span>Altezza (cm)</span>
                <span></span>
            </div>

            <div id="collarSizeRows">
                @foreach(old('rows', $rows) as $i => $row)
                    <div class="collar-size-row" style="display:grid;grid-template-columns:72px 1fr 1fr 100px 56px;gap:10px;margin-bottom:10px;align-items:center;">
                        <input type="text" name="rows[{{ $i }}][size]" class="form-control form-control-sm" value="{{ $row['size'] ?? '' }}" placeholder="25" required>
                        <input type="number" name="rows[{{ $i }}][length_min]" class="form-control form-control-sm" step="0.1" min="0" value="{{ $row['length_min'] ?? '' }}" required>
                        <input type="number" name="rows[{{ $i }}][length_max]" class="form-control form-control-sm" step="0.1" min="0" value="{{ $row['length_max'] ?? '' }}" required>
                        <input type="number" name="rows[{{ $i }}][height]" class="form-control form-control-sm" step="0.1" min="0" value="{{ $row['height'] ?? '' }}" required>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCollarSizeRow(this)" title="Rimuovi">&times;</button>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="addCollarSizeRow()">
                <i class="bi bi-plus-lg"></i> Aggiungi taglia
            </button>
        </div>

        <button type="submit" class="btn-admin-primary" style="padding:12px 24px;">
            <i class="bi bi-check-lg"></i> Salva misure
        </button>
    </form>
</div>

<script>
let collarSizeRowIndex = {{ count(old('rows', $rows)) }};

function addCollarSizeRow() {
    const wrap = document.getElementById('collarSizeRows');
    const row = document.createElement('div');
    row.className = 'collar-size-row';
    row.style.cssText = 'display:grid;grid-template-columns:72px 1fr 1fr 100px 56px;gap:10px;margin-bottom:10px;align-items:center;';
    const i = collarSizeRowIndex++;
    row.innerHTML = `
        <input type="text" name="rows[${i}][size]" class="form-control form-control-sm" placeholder="25" required>
        <input type="number" name="rows[${i}][length_min]" class="form-control form-control-sm" step="0.1" min="0" required>
        <input type="number" name="rows[${i}][length_max]" class="form-control form-control-sm" step="0.1" min="0" required>
        <input type="number" name="rows[${i}][height]" class="form-control form-control-sm" step="0.1" min="0" required>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCollarSizeRow(this)" title="Rimuovi">&times;</button>
    `;
    wrap.appendChild(row);
}

function removeCollarSizeRow(btn) {
    const rows = document.querySelectorAll('#collarSizeRows .collar-size-row');
    if (rows.length <= 1) return;
    btn.closest('.collar-size-row').remove();
}
</script>
@endsection