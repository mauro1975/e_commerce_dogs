@php
    $modalId = 'pdpSizeGuideModal';
@endphp

<div class="pdp-size-guide-modal" id="{{ $modalId }}" role="presentation" aria-hidden="true">
    <div class="pdp-size-guide-backdrop" data-pdp-size-guide-close tabindex="-1"></div>
    <div class="pdp-size-guide-dialog" role="dialog" aria-modal="true" aria-labelledby="pdpSizeGuideTitle">
        <button type="button" class="pdp-size-guide-close" data-pdp-size-guide-close aria-label="{{ __('front.pdp_size_guide_close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
        <h2 class="pdp-size-guide-title" id="pdpSizeGuideTitle">{{ __('front.pdp_size_guide_title') }}</h2>
        <p class="pdp-size-guide-intro">{{ __('front.pdp_size_guide_intro') }}</p>

        <div class="pdp-size-guide-body">
            <div class="pdp-size-guide-dog" aria-hidden="true">
                <figure class="pdp-size-guide-dog-figure">
                    <img src="{{ asset('images/size-guide/dog-profile-outline.png') }}" alt="" class="pdp-size-guide-dog-img" width="235" height="176" loading="lazy" decoding="async">
                    <svg viewBox="0 0 235 176" class="pdp-size-guide-dog-overlay" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                        <defs>
                            <marker id="pdpSgArrowEnd" markerWidth="7" markerHeight="7" refX="6" refY="3.5" orient="auto" markerUnits="strokeWidth">
                                <path d="M0,0 L7,3.5 L0,7" fill="none" stroke="#5d9a8f" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </marker>
                            <marker id="pdpSgArrowStart" markerWidth="7" markerHeight="7" refX="1" refY="3.5" orient="auto-start-reverse" markerUnits="strokeWidth">
                                <path d="M0,0 L7,3.5 L0,7" fill="none" stroke="#5d9a8f" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                            </marker>
                        </defs>
                        <path
                            d="M 38 70 C 52 46, 88 46, 102 66"
                            fill="none"
                            stroke="#5d9a8f"
                            stroke-width="2.25"
                            stroke-linecap="round"
                            marker-start="url(#pdpSgArrowStart)"
                            marker-end="url(#pdpSgArrowEnd)"
                        />
                        <path
                            d="M 68 98 C 98 132, 148 122, 176 96"
                            fill="none"
                            stroke="#5d9a8f"
                            stroke-width="2.25"
                            stroke-linecap="round"
                            marker-start="url(#pdpSgArrowStart)"
                            marker-end="url(#pdpSgArrowEnd)"
                        />
                    </svg>
                </figure>
            </div>

            <div class="pdp-size-guide-table-wrap">
                <table class="pdp-size-guide-table">
                    <thead>
                        <tr>
                            <th>{{ __('front.pdp_size_guide_col_size') }}</th>
                            <th>{{ __('front.pdp_size_guide_col_back') }}</th>
                            <th>{{ __('front.pdp_size_guide_col_neck') }}</th>
                            <th>{{ __('front.pdp_size_guide_col_chest') }}</th>
                            <th>{{ __('front.pdp_size_guide_col_weight') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guideRows as $row)
                        <tr>
                            <td class="pdp-size-guide-size">
                                <span>{{ $row['size'] }}</span>
                                @if(!empty($row['size_note']))
                                    <span class="pdp-size-guide-size-note">{{ $row['size_note'] }}</span>
                                @endif
                            </td>
                            <td>{{ $row['back'] ?? '–' }}</td>
                            <td>{{ $row['neck'] ?? '–' }}</td>
                            <td>{{ $row['chest'] ?? '–' }}</td>
                            <td>{{ $row['weight'] ?? '–' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>