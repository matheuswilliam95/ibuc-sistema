@props(['name', 'label', 'id' => null, 'type' => 'text', 'hint' => null, 'error' => null, 'icon' => null, 'optional' => false])
@php
    $fieldId = $id ?? $name;
    $fieldIcon = $icon === false ? null : ($icon ?? \App\Support\FieldIcon::resolve($name, $type));
@endphp
<div class="field">
    <label class="field__label" for="{{ $fieldId }}">{{ $label }} @if($optional)<span>Opcional</span>@endif</label>
    <div class="field__control">
        @if($fieldIcon)<x-ui.icon :name="$fieldIcon" class="field__icon" :size="18" />@endif
        <input id="{{ $fieldId }}" name="{{ $name }}" type="{{ $type }}"
            @if($hint || $error) aria-describedby="{{ $fieldId }}-message" @endif
            @if($error) aria-invalid="true" @endif
            {{ $attributes->class(['input', 'input--with-icon' => (bool) $fieldIcon]) }}>
    </div>
    @if($error)<p id="{{ $fieldId }}-message" class="field__message field__message--error"><x-ui.icon name="info" :size="14" />{{ $error }}</p>
    @elseif($hint)<p id="{{ $fieldId }}-message" class="field__message">{{ $hint }}</p>@endif
</div>
