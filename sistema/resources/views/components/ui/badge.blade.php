@props(['tone' => 'neutral', 'dot' => false, 'icon' => null])
@php
    $tone = in_array($tone, ['neutral', 'success', 'warning', 'danger', 'info', 'level-1', 'level-2', 'level-3', 'level-4'], true) ? $tone : 'neutral';
@endphp
<span {{ $attributes->class(['badge', "badge--{$tone}"]) }}>
    @if($dot)<span class="badge__dot" aria-hidden="true"></span>@endif
    @if($icon)<x-ui.icon :name="$icon" :size="14" />@endif
    {{ $slot }}
</span>
