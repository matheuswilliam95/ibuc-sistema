@extends('layouts.structure')
@section('structure-description', 'O que você deseja adicionar? Cada opção abre seu próprio formulário.')
@section('structure-content')
<section class="structure-destinations" aria-label="Tipos de cadastro">
    @foreach($formLabels as $type => $label)
    @if($region || $type === 'region')
    <a class="panel structure-destination" href="{{ route('structure.create', ['type' => $type, 'region' => $region?->id, 'back' => $backPage] + array_intersect_key($filters, array_flip(['unit', 'q']))) }}">
        <span class="structure-destination-icon"><x-ui.icon :name="match($type) { 'region', 'unit' => 'building', 'classroom', 'level' => 'users', 'offering' => 'calendar', default => 'book' }" :size="24" /></span>
        <h2>{{ $label }}</h2><span class="text-link">Abrir formulário <x-ui.icon name="arrow-right" :size="17" /></span>
    </a>
    @endif
    @endforeach
</section>
@endsection
