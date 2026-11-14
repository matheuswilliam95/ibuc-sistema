@extends('layouts.structure')
@section('structure-description', 'Consulte a execução proposta de cada módulo, com período e núcleo próprios.')
@section('structure-actions')
<x-ui.button :href="route('structure.create', ['type' => 'offering', 'region' => $region?->id])" icon="calendar">Nova oferta</x-ui.button>
@endsection

@section('structure-content')
<section class="panel" aria-label="Filtrar ofertas">
    <form method="GET" action="{{ route('structure.offerings') }}" class="structure-context-form">
        <input type="hidden" name="region" value="{{ $region?->id }}">
        <x-ui.select name="unit" label="Núcleo" icon="building"><option value="">Todos desta regional</option>@foreach($units as $unit)<option value="{{ $unit->id }}" @selected(($filters['unit'] ?? '') == $unit->id)>{{ $unit->name }}</option>@endforeach</x-ui.select>
        <x-ui.button type="submit" variant="secondary" icon="search">Filtrar</x-ui.button>
    </form>
</section>
<section class="structure-records" aria-label="Ofertas da regional">
    @forelse($offerings as $offering)
    <article class="panel structure-record"><div class="structure-card-heading"><h2>{{ $offering->name }}</h2><x-ui.badge tone="neutral">Rascunho</x-ui.badge></div><p class="structure-location">{{ $offering->localUnit->name }} · {{ $offering->code }}</p>
        <dl class="structure-details"><div><dt>Módulo e edição</dt><dd>{{ $offering->module->name }} · {{ $offering->edition->code }} v{{ $offering->edition->version }}</dd></div><div><dt>Período previsto</dt><dd>{{ $offering->starts_on->format('d/m/Y') }} a {{ $offering->ends_on->format('d/m/Y') }}</dd></div><div><dt>Capacidade proposta</dt><dd>{{ $offering->capacity }} alunos</dd></div><div><dt>Regulamento</dt><dd>Versão {{ $offering->rule->version }} · rascunho</dd></div></dl>
        <x-ui.button :href="route('structure.classrooms', ['region' => $region->id, 'unit' => $offering->local_unit_id])" variant="secondary" icon="users">Ver turmas do núcleo</x-ui.button>
    </article>
    @empty<div class="panel structure-empty"><h2>Nenhuma oferta encontrada</h2><p>Confira o núcleo selecionado ou cadastre uma oferta.</p></div>@endforelse
</section>
@endsection
