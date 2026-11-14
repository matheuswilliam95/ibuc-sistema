@props(['name', 'label', 'id' => null, 'hint' => null, 'icon' => null])
@php
    $fieldId = $id ?? $name;
    $fieldIcon = $icon === false ? null : ($icon ?? \App\Support\FieldIcon::resolve($name, control: 'select'));
@endphp
<div class="field">
    <label class="field__label" for="{{ $fieldId }}">{{ $label }}</label>
    <div class="field__control">
        @if($fieldIcon)<x-ui.icon :name="$fieldIcon" class="field__icon" :size="18" />@endif
        <select id="{{ $fieldId }}" name="{{ $name }}" @if($hint) aria-describedby="{{ $fieldId }}-message" @endif {{ $attributes->class(['input', 'select', 'input--with-icon' => (bool) $fieldIcon]) }}>{{ $slot }}</select>
        <x-ui.icon name="chevron-down" class="select__icon" :size="16" />
    </div>
    @if($hint)<p id="{{ $fieldId }}-message" class="field__message">{{ $hint }}</p>@endif
</div>
