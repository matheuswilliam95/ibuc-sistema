@extends('layouts.structure')
@section('structure-description', 'Consulte as edições, os níveis, os módulos e os regulamentos em preparação.')
@section('structure-actions')
<x-ui.button :href="route('structure.create', ['type' => 'edition', 'region' => $region?->id])" icon="book">Nova edição</x-ui.button>
@endsection

@section('structure-content')
    <section id="curriculo" class="structure-curriculum" aria-labelledby="curriculum-title">
        <div class="structure-section-heading"><div><span class="structure-eyebrow">CAMINHO DE APRENDIZAGEM</span><h2 id="curriculum-title">Currículo da regional</h2></div><p>Edições, níveis e módulos preservam suas próprias referências.</p></div>
        @forelse($editions as $edition)
        <article class="panel">
            <div class="structure-card-heading"><div><h3>{{ $edition->name }}</h3><p>{{ $edition->code }} · versão {{ $edition->version }}</p></div><x-ui.badge tone="warning">Rascunho</x-ui.badge></div>
            <p class="structure-form-hint">{{ $edition->source_reference ?: 'Referência institucional ainda não informada.' }}</p>
            <div class="structure-levels">
                @foreach($edition->levels as $level)<div><x-ui.badge :tone="'level-'.min(4, max(1, $level->position))">{{ $level->code }}</x-ui.badge><strong>{{ $level->min_age }}–{{ $level->max_age }} anos</strong><span>{{ $level->name }}</span></div>@endforeach
            </div>
            <div class="structure-module-list">
                @forelse($edition->modules as $module)<p><x-ui.icon name="book" :size="18" /><strong>{{ $module->name }}</strong><span>{{ $module->code }} · {{ $module->lesson_count }} lições propostas</span></p>@empty<p>Adicione os módulos desta edição.</p>@endforelse
            </div>
            <p class="structure-form-hint">{{ $edition->rules->count() }} regulamento(s) em rascunho. Corte, transição, vigência e edição completa aguardam decisão. Nenhum enquadramento ou matrícula é automático.</p>
        </article>
        @empty<div class="panel structure-empty"><h3>O currículo começa com uma edição</h3><p>Cadastre a edição, depois seus níveis, módulos e regulamento em rascunho.</p></div>@endforelse
    </section>
@endsection
