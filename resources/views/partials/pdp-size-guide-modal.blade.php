@php
    $modalId = 'pdpSizeGuideModal';
    $dogGuideImg = 'images/size-guide/logo-guida-taglie.png';
    $dogGuidePath = public_path($dogGuideImg);
    $dogGuideVer = is_file($dogGuidePath) ? filemtime($dogGuidePath) : time();
    [$dogGuideW, $dogGuideH] = is_file($dogGuidePath) ? getimagesize($dogGuidePath) : [400, 300];
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
                    <img src="{{ asset($dogGuideImg) }}?v={{ $dogGuideVer }}" alt="" class="pdp-size-guide-dog-img" width="{{ $dogGuideW }}" height="{{ $dogGuideH }}" loading="lazy" decoding="async">
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