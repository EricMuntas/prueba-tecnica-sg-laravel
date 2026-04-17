@props([
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'width: 20px; height: 20px; border-width: 2px;',
        'md' => 'width: 40px; height: 40px; border-width: 4px;',
        'lg' => 'width: 60px; height: 60px; border-width: 4px;',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'spinner', 'style' => ($sizes[$size] ?? $sizes['md'])]) }}
    role="status" aria-label="Cargando..."></div>
