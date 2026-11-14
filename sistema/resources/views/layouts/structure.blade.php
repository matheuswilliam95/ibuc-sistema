@extends('layouts.preview', ['isStructurePreview' => true])

@section('content')
<div class="structure-page">
    <header class="structure-heading">
        <div><span class="structure-eyebrow">NOSSA COMUNIDADE</span><h1>{{ $structurePageTitle }}</h1><p>@yield('structure-description')</p></div>
        <div class="structure-heading-actions">@yield('structure-actions')</div>
    </header>

    <x-ui.alert tone="warning" title="Ambiente local de demonstração">Use apenas dados fictícios. As configurações permanecem em rascunho; matrículas e acesso de participantes ainda não estão liberados.</x-ui.alert>

    @if(session('status'))
    <div role="status"><x-ui.alert tone="success" title="Cadastro salvo">{{ session('status') }}</x-ui.alert></div>
    @endif
    @if($errors->any())
    <div class="structure-errors" role="alert" tabindex="-1" data-error-summary>
        <h2>Confira os campos antes de salvar</h2>
        <ul>@foreach($errors->messages() as $field => $messages)<li><a href="#{{ $field === 'structure' ? 'cadastro' : $field }}">{{ $messages[0] }}</a></li>@endforeach</ul>
    </div>
    @endif

    @if($regions->isNotEmpty())
    <section class="panel structure-context" aria-label="Escolha a regional">
        <form method="GET" action="{{ route($contextRoute, $contextParameters) }}" class="structure-context-form">
            <x-ui.select name="region" id="context-region" label="Regional em que você está trabalhando" icon="building" required>
                @foreach($regions as $item)<option value="{{ $item->id }}" @selected($region?->id === $item->id)>{{ $item->name }} · {{ $item->code }}</option>@endforeach
            </x-ui.select>
            <x-ui.button type="submit" variant="secondary" icon="arrow-right">Abrir regional</x-ui.button>
        </form>
    </section>
    @endif

    @yield('structure-content')
</div>
@endsection
