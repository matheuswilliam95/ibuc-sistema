@extends('layouts.structure')
@section('structure-description', 'Preencha os dados e confira o contexto antes de salvar.')
@section('structure-actions')
<x-ui.button :href="$backUrl" variant="secondary" icon="arrow-left">Voltar</x-ui.button>
@endsection

@section('structure-content')
<section class="panel structure-create structure-create-page" id="cadastro" aria-label="Formulário de cadastro">
    <x-ui.form-header :title="$structurePageTitle" eyebrow="CADASTRO" :icon="match($formType) { 'region', 'unit' => 'building', 'classroom', 'level' => 'users', 'offering' => 'calendar', default => 'book' }" />
    @if(!$region && $formType !== 'region')
    <p>Cadastre uma regional primeiro.</p><x-ui.button :href="route('structure.create', ['type' => 'region'])" icon="building">Nova regional</x-ui.button>
    @else
    <p class="structure-form-hint">{{ $formType === 'region' ? 'Identifique a nova regional.' : $region->name.' · Os vínculos são conferidos ao salvar.' }}</p>
    @include('structure.form')
    <a href="{{ $backUrl }}" class="text-link structure-cancel">Cancelar e voltar</a>
    @endif
</section>
@endsection
