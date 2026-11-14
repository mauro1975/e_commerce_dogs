@php
    $defaults = trans('front.pdp_sweater_size_defaults');
    if (!is_array($defaults)) {
        $defaults = [];
    }

    $custom = $product->size_chart ?? null;
    if (is_string($custom)) {
        $custom = json_decode($custom, true);
    }

    $rows = [];
    if (is_array($custom) && !empty($custom['rows']) && is_array($custom['rows'])) {
        foreach ($custom['rows'] as $row) {
            if (!empty($row['size'])) {
                $rows[] = $row;
            }
        }
    }

    if (empty($rows)) {
        $order = !empty($sizes) && is_array($sizes) ? $sizes : array_keys($defaults);
        foreach ($order as $sz) {
            $sz = is_string($sz) ? $sz : (string) $sz;
            if (!isset($defaults[$sz])) {
                continue;
            }
            $m = $defaults[$sz];
            $rows[] = [
                'size'  => $sz,
                'back'  => $m['back'] ?? null,
                'neck'  => $m['neck'] ?? null,
                'chest' => $m['chest'] ?? null,
            ];
        }
    }

    $maxChest = max(1, ...array_map(fn ($r) => (float) ($r['chest'] ?? 0), $rows ?: [['chest' => 1]]));
@endphp

@if(count($rows) > 0)
<aside class="pdp-size-chart" aria-label="{{ __('front.pdp_size_chart_title') }}">
    <div class="pdp-size-chart-head">
        <h3 class="pdp-size-chart-title">{{ __('front.pdp_size_chart_title') }}</h3>
        <p class="pdp-size-chart-sub">{{ __('front.pdp_size_chart_sub') }}</p>
    </div>

    <div class="pdp-size-chart-body">
        <div class="pdp-size-chart-diagram" aria-hidden="true">
            <svg viewBox="0 0 130 158" class="pdp-size-chart-svg">
                <path d="M42 30h40c6 0 10 4 10 10v11c0 8 16 11 20 18v56c0 6-4 10-10 10H50c-6 0-10-4-10-10V69c4-7 20-10 20-18V40c0-6 4-10 10-10z"
                      fill="#f3f6f4" stroke="#9bc3b1" stroke-width="2"/>
                {{-- A: schiena (lunghezza dorsale) --}}
                <line x1="62" y1="38" x2="62" y2="72" stroke="#6fa398" stroke-width="1.4" stroke-dasharray="3 2"/>
                <line x1="54" y1="38" x2="70" y2="38" stroke="#6fa398" stroke-width="1"/>
                <line x1="54" y1="72" x2="70" y2="72" stroke="#6fa398" stroke-width="1"/>
                <circle cx="48" cy="55" r="8" fill="#e8f2ed"/>
                <text x="48" y="58" text-anchor="middle" font-size="9" font-weight="700" fill="#6fa398">A</text>
                {{-- B: petto --}}
                <line x1="88" y1="78" x2="118" y2="78" stroke="#6fa398" stroke-width="1.4"/>
                <circle cx="122" cy="78" r="8" fill="#e8f2ed"/>
                <text x="122" y="81" text-anchor="middle" font-size="9" font-weight="700" fill="#6fa398">B</text>
                {{-- C: collo --}}
                <line x1="10" y1="46" x2="36" y2="46" stroke="#6fa398" stroke-width="1.4"/>
                <circle cx="6" cy="46" r="8" fill="#e8f2ed"/>
                <text x="6" y="49" text-anchor="middle" font-size="9" font-weight="700" fill="#6fa398">C</text>
            </svg>
            <ul class="pdp-size-chart-legend">
                <li><span>A</span> {{ __('front.pdp_size_chart_col_back') }}</li>
                <li><span>B</span> {{ __('front.pdp_size_chart_col_chest') }}</li>
                <li><span>C</span> {{ __('front.pdp_size_chart_col_neck') }}</li>
            </ul>
        </div>

        <div class="pdp-size-chart-table-wrap">
            <table class="pdp-size-chart-table">
                <thead>
                    <tr>
                        <th>{{ __('front.pdp_size_chart_col_size') }}</th>
                        <th>{{ __('front.pdp_size_chart_col_back') }}</th>
                        <th>{{ __('front.pdp_size_chart_col_neck') }}</th>
                        <th>{{ __('front.pdp_size_chart_col_chest') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    @php $chest = (float) ($row['chest'] ?? 0); @endphp
                    <tr>
                        <td class="pdp-size-chart-size">{{ $row['size'] }}</td>
                        <td>{{ $row['back'] ?? '–' }}</td>
                        <td>{{ $row['neck'] ?? '–' }}</td>
                        <td>
                            <span class="pdp-size-chart-bar" style="--pct: {{ $maxChest > 0 ? round(($chest / $maxChest) * 100) : 0 }}%;">{{ $row['chest'] ?? '–' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</aside>
@endif