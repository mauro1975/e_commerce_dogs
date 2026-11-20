@php
    $catalogSizeRows = $catalogSizeRows ?? $collarSizeRows ?? [];
@endphp
@if(!empty($catalogSizeRows))
<div class="pdp-collar-size" id="pdpCollarSize">
    <div class="pdp-collar-size-label">{{ __('front.pdp_size_label') }}:</div>
    <button type="button" class="pdp-collar-size-trigger" id="pdpCollarSizeTrigger" aria-haspopup="listbox" aria-expanded="false">
        <span class="pdp-collar-size-trigger-text" id="pdpCollarSizeTriggerText">{{ __('front.pdp_collar_size_placeholder') }}</span>
        <span class="pdp-collar-size-chevron" aria-hidden="true"></span>
    </button>
    <div class="pdp-collar-size-panel" id="pdpCollarSizePanel" role="listbox" hidden>
        @foreach($catalogSizeRows as $row)
            <button type="button"
                    class="pdp-collar-size-option"
                    role="option"
                    data-size="{{ $row['size'] }}"
                    data-label="{{ $row['label'] }}">
                <span class="pdp-collar-size-option-text">{{ $row['label'] }}</span>
                <span class="pdp-collar-size-status" aria-hidden="true">{{ __('front.pdp_size_available') }}</span>
            </button>
        @endforeach
    </div>
</div>
@endif