@props(['name', 'label', 'id' => null, 'hint' => null, 'error' => null, 'icon' => null])
@php
    $fieldId = $id ?? $name;
    $fieldIcon = $icon === false ? null : ($icon ?? \App\Support\FieldIcon::resolve($name, control: 'select'));
@endphp
<div class="field">
    <label class="field__label" for="{{ $fieldId }}">{{ $label }}</label>
    <div class="field__control">
        @if($fieldIcon)<x-ui.icon :name="$fieldIcon" class="field__icon" :size="18" />@endif
        <select id="{{ $fieldId }}" name="{{ $name }}" @if($hint || $error) aria-describedby="{{ $fieldId }}-message" @endif @if($error) aria-invalid="true" @endif {{ $attributes->class(['input', 'select', 'input--with-icon' => (bool) $fieldIcon]) }}>{{ $slot }}</select>
        <x-ui.icon name="chevron-down" class="select__icon" :size="16" />
    </div>
    @if($error)<p id="{{ $fieldId }}-message" class="field__message field__message--error"><x-ui.icon name="info" :size="14" />{{ $error }}</p>
    @elseif($hint)<p id="{{ $fieldId }}-message" class="field__message">{{ $hint }}</p>@endif
</div>
