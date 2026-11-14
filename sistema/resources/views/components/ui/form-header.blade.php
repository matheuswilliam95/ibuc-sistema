@props(['title', 'eyebrow' => null, 'icon' => null])
<header {{ $attributes->class(['panel-heading', 'form-heading']) }}>
    <div>
        @if($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span>@endif
        <h3>{{ $title }}</h3>
    </div>
    @if($icon)<x-ui.icon :name="$icon" :size="20" />@endif
    {{ $slot }}
</header>
