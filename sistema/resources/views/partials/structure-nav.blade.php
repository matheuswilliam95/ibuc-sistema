<nav class="side-nav" aria-label="Seções da organização">
    <span class="nav-heading">NOSSA COMUNIDADE</span>
    @foreach([
        'organization' => ['structure.index', 'Organização', 'grid'],
        'units' => ['structure.units', 'Núcleos', 'building'],
        'classrooms' => ['structure.classrooms', 'Turmas', 'users'],
        'offerings' => ['structure.offerings', 'Ofertas', 'calendar'],
        'curriculum' => ['structure.curriculum', 'Currículo', 'book'],
        'create' => ['structure.create.index', 'Novo cadastro', 'text'],
    ] as $key => [$route, $label, $icon])
    <a href="{{ route($route, ['region' => $region?->id]) }}" @class(['nav-link', 'is-active' => $pageKey === $key]) @if($pageKey === $key) aria-current="page" @endif><x-ui.icon :name="$icon" /><span>{{ $label }}</span></a>
    @endforeach
    <a href="{{ route('journeys') }}" class="nav-link"><x-ui.icon name="arrow-right" /><span>Experimentar jornadas</span></a>
    <a href="{{ route('design-system') }}" class="nav-link"><x-ui.icon name="palette" /><span>Identidade visual</span></a>
</nav>
