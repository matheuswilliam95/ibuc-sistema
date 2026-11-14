@props(['name', 'label', 'hint' => null])
<label class="switch-row">
    <span class="switch-row__copy"><span>{{ $label }}</span>@if($hint)<small>{{ $hint }}</small>@endif</span>
    <span class="switch">
        <input type="checkbox" name="{{ $name }}" role="switch" {{ $attributes }}>
        <span class="switch__track" aria-hidden="true"></span>
    </span>
</label>
