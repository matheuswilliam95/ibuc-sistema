@extends('layouts.structure')
@section('structure-description', 'Escolha a área que deseja consultar ou organizar nesta regional.')
@section('structure-actions')
<x-ui.button :href="route('structure.create', ['type' => 'region', 'region' => $region?->id])" variant="secondary" icon="building">Nova regional</x-ui.button>
@endsection

@section('structure-content')
<section class="structure-destinations" aria-label="Áreas da organização">
    @foreach([
        'units' => ['structure.units', 'Núcleos', 'building', 'Consulte as igrejas e unidades participantes.'],
        'classrooms' => ['structure.classrooms', 'Turmas', 'users', 'Encontre cada turma no seu núcleo e nível.'],
        'offerings' => ['structure.offerings', 'Ofertas', 'calendar', 'Consulte módulos, períodos e capacidades propostos.'],
        'curriculum' => ['structure.curriculum', 'Currículo', 'book', 'Acompanhe as edições, os níveis e os módulos.'],
    ] as $key => [$route, $label, $icon, $description])
    <a class="panel structure-destination" href="{{ route($route, ['region' => $region?->id]) }}">
        <div class="structure-destination-heading"><span class="structure-destination-icon"><x-ui.icon :name="$icon" :size="24" /></span><strong>{{ $counts[$key] }}</strong></div>
        <h2>{{ $label }}</h2><p>{{ $description }}</p><span class="text-link">Abrir {{ mb_strtolower($label) }} <x-ui.icon name="arrow-right" :size="17" /></span>
    </a>
    @endforeach
    <a class="panel structure-destination" href="{{ route('structure.create.index', ['region' => $region?->id]) }}"><span class="structure-destination-icon"><x-ui.icon name="text" :size="24" /></span><h2>Novo cadastro</h2><p>Escolha o que deseja adicionar e abra o formulário correspondente.</p><span class="text-link">Escolher cadastro <x-ui.icon name="arrow-right" :size="17" /></span></a>
</section>
@endsection
