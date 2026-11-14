@extends('layouts.structure')
@section('structure-description', 'Consulte os núcleos e acesse as turmas ou ofertas de cada unidade.')
@section('structure-actions')
<x-ui.button :href="route('structure.create', ['type' => 'unit', 'region' => $region?->id])" icon="building">Novo núcleo</x-ui.button>
@endsection

@section('structure-content')
<section class="structure-records" aria-label="Núcleos da regional">
    @forelse($units as $unit)
    <article class="panel structure-record"><div class="structure-card-heading"><h2>{{ $unit->name }}</h2><x-ui.icon name="building" /></div><p class="structure-location">{{ $region->name }} · {{ $unit->code }}</p>
        <div class="structure-record-actions"><x-ui.button :href="route('structure.classrooms', ['region' => $region->id, 'unit' => $unit->id])" variant="secondary" icon="users">Ver turmas</x-ui.button><x-ui.button :href="route('structure.offerings', ['region' => $region->id, 'unit' => $unit->id])" variant="ghost" icon="calendar">Ver ofertas</x-ui.button></div>
    </article>
    @empty<div class="panel structure-empty"><h2>Nenhum núcleo cadastrado</h2><p>Adicione um núcleo à regional para organizar suas ofertas e turmas.</p></div>@endforelse
</section>
@endsection
