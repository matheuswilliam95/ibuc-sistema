@props(['variant' => 'primary', 'icon' => null, 'size' => 'md', 'type' => 'button', 'href' => null])
@php
    $variant = in_array($variant, ['primary', 'secondary', 'ghost', 'danger'], true) ? $variant : 'primary';
    $size = in_array($size, ['sm', 'md', 'lg'], true) ? $size : 'md';
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->class(['button', "button--{$variant}", "button--{$size}"]) }}>
        @if($icon)<x-ui.icon :name="$icon" :size="18" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class(['button', "button--{$variant}", "button--{$size}"]) }}>
        @if($icon)<x-ui.icon :name="$icon" :size="18" />@endif
        {{ $slot }}
    </button>
@endif
