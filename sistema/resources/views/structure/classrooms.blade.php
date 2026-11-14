@extends('layouts.structure')
@section('structure-description', 'Encontre as turmas desta regional por nome ou núcleo.')
@section('structure-actions')
<x-ui.button :href="route('structure.create', ['type' => 'classroom', 'region' => $region?->id, 'back' => 'classrooms'] + array_intersect_key($filters, array_flip(['unit', 'q'])))" icon="users">Nova turma</x-ui.button>
@endsection

@section('structure-content')
        <section class="panel structure-directory" id="turmas" aria-labelledby="directory-title">
            <div class="panel-heading"><div><h2 id="directory-title">Turmas da regional</h2><p>{{ $region?->name ?? 'Selecione ou cadastre uma regional' }} · até 100 resultados por consulta</p></div><x-ui.icon name="users" /></div>
            <form method="GET" action="{{ route('structure.classrooms') }}#turmas" class="structure-search">
                <input type="hidden" name="region" value="{{ $region?->id }}">
                <x-ui.input name="q" type="search" label="Buscar turma" placeholder="Ex.: Sementes" :value="$filters['q'] ?? ''" maxlength="120" />
                <x-ui.select name="unit" label="Núcleo" icon="building">
                    <option value="">Todos desta regional</option>
                    @foreach($units as $unit)<option value="{{ $unit->id }}" @selected(($filters['unit'] ?? '') == $unit->id)>{{ $unit->name }}</option>@endforeach
                </x-ui.select>
                <x-ui.button type="submit" icon="search" :disabled="!$region">Buscar</x-ui.button>
            </form>
            <div class="structure-classrooms" aria-live="polite">
                @forelse($classrooms as $classroom)
                <article class="structure-classroom" data-classroom="{{ $classroom->id }}">
                    <div class="structure-card-heading"><h3>{{ $classroom->name }}</h3><x-ui.badge tone="neutral">Rascunho</x-ui.badge></div>
                    <p class="structure-location"><x-ui.icon name="building" :size="16" />{{ $classroom->region->name }} · {{ $classroom->localUnit->name }}</p>
                    <dl><div><dt>Nível</dt><dd>{{ $classroom->level->code }} · {{ $classroom->level->min_age }}–{{ $classroom->level->max_age }} anos</dd></div>
                        <div><dt>Capacidade proposta</dt><dd>{{ $classroom->capacity }} alunos</dd></div>
                        <div><dt>Oferta</dt><dd>{{ $classroom->offering->name }} · {{ $classroom->offering->module->name }}</dd></div>
                        <div><dt>Período proposto</dt><dd>{{ $classroom->offering->starts_on->format('d/m/Y') }} a {{ $classroom->offering->ends_on->format('d/m/Y') }}</dd></div>
                        <div><dt>Identificação</dt><dd>{{ $classroom->code }} · {{ $classroom->edition->code }} v{{ $classroom->edition->version }}</dd></div></dl>
                </article>
                @empty<div class="structure-empty"><x-ui.icon name="users" :size="32" /><h3>Nenhuma turma encontrada</h3><p>Confira os filtros ou cadastre uma turma nesta regional.</p></div>@endforelse
            </div>
        </section>
@endsection
