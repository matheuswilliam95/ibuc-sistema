@extends('layouts.preview', ['isStructurePreview' => true])

@section('content')
<div class="structure-page">
    <header class="structure-heading">
        <div><span class="structure-eyebrow">CADA TURMA, SEU LUGAR</span><h1>Organização e turmas</h1><p>Uma visão clara dos núcleos, das ofertas e do caminho de aprendizagem.</p></div>
        <div class="structure-heading-actions"><x-ui.badge tone="warning" icon="book">Configurações em rascunho</x-ui.badge><x-ui.button href="#cadastro" variant="secondary" icon="text">Novo cadastro</x-ui.button></div>
    </header>

    <x-ui.alert tone="warning" title="Ambiente local de demonstração">Use apenas dados fictícios. Os cadastros ficam no banco local; matrículas e acesso de participantes ainda não estão liberados. Datas, capacidades e currículo são propostas para avaliação.</x-ui.alert>

    @if(session('status'))
    <div role="status"><x-ui.alert tone="success" title="Cadastro salvo">{{ session('status') }}</x-ui.alert></div>
    @endif
    @if($errors->any())
    <div class="structure-errors" role="alert" tabindex="-1" data-error-summary>
        <h2>Confira os campos antes de salvar</h2>
        <ul>@foreach($errors->messages() as $field => $messages)<li><a href="#{{ $field === 'structure' ? 'cadastro' : $field }}">{{ $messages[0] }}</a></li>@endforeach</ul>
    </div>
    @endif

    <section class="panel structure-context" aria-label="Escolha a regional">
        <x-ui.form-header title="Escolha a regional" eyebrow="CONTEXTO" icon="building" />
        <form method="GET" action="{{ route('structure.index') }}" class="structure-context-form">
            <x-ui.select name="region" id="context-region" label="Regional" icon="building" required>
                @forelse($regions as $item)<option value="{{ $item->id }}" @selected($region?->id === $item->id)>{{ $item->name }} · {{ $item->code }}</option>
                @empty<option value="">Nenhuma regional cadastrada</option>@endforelse
            </x-ui.select>
            <x-ui.button type="submit" variant="secondary" icon="arrow-right" :disabled="$regions->isEmpty()">Abrir regional</x-ui.button>
        </form>
        <div class="structure-unit-list">
            @forelse($units as $unit)<span><x-ui.icon name="building" :size="18" />{{ $unit->name }}<small>{{ $unit->code }}</small></span>
            @empty<p>Nenhum núcleo cadastrado. Comece pela regional e depois adicione os núcleos.</p>@endforelse
        </div>
    </section>

    <div class="structure-grid">
        <section class="panel structure-directory" id="turmas" aria-labelledby="directory-title">
            <div class="panel-heading"><div><h2 id="directory-title">Turmas da regional</h2><p>{{ $region?->name ?? 'Selecione ou cadastre uma regional' }} · até 100 resultados por consulta</p></div><x-ui.icon name="users" /></div>
            <form method="GET" action="{{ route('structure.index') }}#turmas" class="structure-search">
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

        <section class="panel structure-create" id="cadastro" aria-labelledby="create-title">
            <x-ui.form-header title="Novo cadastro" eyebrow="CONSTRUA A ORGANIZAÇÃO" icon="text" />
            @php($formLabels = ['region' => 'Regional', 'unit' => 'Núcleo', 'edition' => 'Edição', 'level' => 'Nível', 'module' => 'Módulo', 'rule' => 'Regulamento', 'offering' => 'Oferta', 'classroom' => 'Turma'])
            <nav class="structure-form-tabs" aria-label="Tipo de cadastro">
                @foreach($formLabels as $key => $label)
                @if($region || $key === 'region')<a href="{{ route('structure.index', ['region' => $region?->id, 'form' => $key]) }}#cadastro" @class(['structure-form-tab', 'is-active' => $formType === $key]) @if($formType === $key) aria-current="page" @endif>{{ $label }}</a>@endif
                @endforeach
            </nav>
            <h2 id="create-title">{{ $formLabels[$formType] }}</h2>
            <p class="structure-form-hint">{{ $formType === 'region' ? 'Identifique a nova regional.' : ($region?->name ?? 'Cadastre uma regional primeiro.').' · Os vínculos são conferidos ao salvar.' }}</p>
            @include('structure.form')
        </section>
    </div>

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
</div>
@endsection
