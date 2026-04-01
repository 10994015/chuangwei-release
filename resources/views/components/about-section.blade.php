{{-- resources/views/components/about-section.blade.php --}}
@props([
    'title'            => null,
    'subtitle'         => null,
    'descriptions'     => [],
    'linkText'         => null,
    'linkUrl'          => '#',
    'imagePlaceholder' => null,
])
@php
    $title            = $title            ?? __('ui.aboutSection.title');
    $subtitle         = $subtitle         ?? '';
    $linkText         = $linkText         ?? __('ui.aboutSection.historyBtn');
    $imagePlaceholder = $imagePlaceholder ?? __('ui.aboutSection.imgAlt');
    if (empty($descriptions)) {
        $descriptions = ['', ''];
    }
@endphp

<section class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-image">
                <div class="image-placeholder">{{ $imagePlaceholder }}</div>
            </div>
            <div class="about-text">
                <h2 class="about-title">{{ $title }}</h2>
                <p class="about-subtitle">{{ $subtitle }}</p>

                @foreach ($descriptions as $desc)
                    <p class="about-description">{{ $desc }}</p>
                @endforeach

                <a href="{{ $linkUrl }}" class="about-link">{{ $linkText }}</a>
            </div>
        </div>
    </div>
</section>
