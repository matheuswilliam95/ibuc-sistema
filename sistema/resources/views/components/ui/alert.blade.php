@props(['tone' => 'info', 'title'])
@php $tone = in_array($tone, ['info', 'success', 'warning', 'danger'], true) ? $tone : 'info'; @endphp
<div {{ $attributes->class(['alert', "alert--{$tone}"]) }}>
    <span class="alert__icon"><x-ui.icon :name="$tone === 'success' ? 'check-circle' : ($tone === 'warning' ? 'alert' : 'info')" :size="19" /></span>
    <div><strong>{{ $title }}</strong><div class="alert__copy">{{ $slot }}</div></div>
</div>
