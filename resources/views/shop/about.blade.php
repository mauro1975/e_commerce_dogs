@extends('layouts.app')

@section('title', 'About Us – .rosmarino')
@section('meta_description', 'Learn about .rosmarino – premium dog accessories made with love.')

@section('content')

{{-- Hero --}}
<section style="padding:80px 40px;background:#fff;">
    <div style="max-width:820px;margin:0 auto;">
        <h1 class="about-hero-title">{!! __('front.about_hero_title') !!}</h1>
        <div class="about-hero-text">
            {!! __('front.about_hero_text') !!}
        </div>
    </div>
</section>

<style>
    .about-hero-title { font-size:clamp(26px,3.5vw,36px); margin-bottom:24px; text-align:left; color:#9bc3b1; }
    .about-hero-text { font-size:17px; line-height:1.85; text-align:left; }
    .about-hero-text p { margin:0 0 1.1em; color:#6fa398; }
    .about-hero-text .about-hero-lead { font-size:18px; }
    .about-hero-text h3 { font-family:var(--font-serif); font-size:26px; color:var(--black); margin:2em 0 .75em; font-weight:400; }
    .about-hero-text h4 { font-size:14px; letter-spacing:.5px; text-transform:uppercase; color:var(--green-dark); margin:1.5em 0 .75em; font-weight:600; font-family:var(--font-sans); }
    .about-hero-text ul { margin:0 0 1.25em; padding-left:1.2em; color:#6fa398; }
    .about-hero-text li { margin-bottom:.45em; color:#6fa398; }
</style>

@endsection

