@extends('layouts.app')

@section('title', __('front.shipping_returns_title') . ' – .rosmarino')
@section('meta_description', __('front.shipping_returns_meta'))

@section('content')
<section style="padding:80px 40px;">
    <div style="max-width:820px;margin:0 auto;">
        <h1 class="legal-page-title">{{ __('front.shipping_returns_heading') }}</h1>
        <div class="legal-page-body">
            {!! __('front.shipping_returns_body') !!}
        </div>
    </div>
</section>

<style>
    .legal-page-title {
        font-size: clamp(28px, 3.5vw, 38px);
        margin-bottom: 28px;
        color: var(--black);
        font-family: var(--font-serif);
        font-weight: 400;
    }
    .legal-page-body {
        font-size: 16px;
        line-height: 1.85;
        color: #444;
    }
    .legal-page-body h2 {
        font-family: var(--font-serif);
        font-size: 24px;
        color: var(--black);
        margin: 2.25em 0 0.75em;
        font-weight: 400;
    }
    .legal-page-body h2:first-child { margin-top: 0; }
    .legal-page-body p { margin: 0 0 1em; }
    .legal-page-body ul { margin: 0 0 1.25em; padding-left: 1.25em; }
    .legal-page-body li { margin-bottom: 0.65em; }
    .legal-page-body strong { color: var(--black); font-weight: 600; }
</style>
@endsection