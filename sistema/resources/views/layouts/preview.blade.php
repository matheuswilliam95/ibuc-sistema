@php($isJourneyPreview = $isJourneyPreview ?? false)
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#326748">
    <meta name="description" content="{{ $isJourneyPreview ? 'Experimente as jornadas de família, professor e secretaria do IBUC com dados fictícios.' : 'Conheça a base visual do IBUC: cores, tipografia e componentes pensados para aproximar famílias e igrejas.' }}">
    <title>{{ $isJourneyPreview ? 'Jornadas da comunidade' : 'Identidade visual' }} · IBUC</title>
    <link rel="icon" type="image/png" href="{{ asset('brand/ibuc-original.png') }}">
    <link rel="preload" href="{{ Vite::asset('resources/fonts/Manrope-Variable.ttf') }}" as="font" type="font/ttf" crossorigin>
    @vite(['resources/css/app.css', $isJourneyPreview ? 'resources/js/journeys.js' : 'resources/js/app.js'])
</head>
<body @class(['journeys-page' => $isJourneyPreview])>
    <a class="skip-link" href="#conteudo">Ir para o conteúdo</a>
    <aside class="sidebar" aria-label="Navegação principal">
        <a class="brand" href="{{ route('design-system') }}" aria-label="IBUC, início da identidade visual">
            <img src="{{ asset('brand/ibuc-original.png') }}" width="101" height="60" alt="Instituto Bíblico Único Caminho">
            <span>Conectando nossa comunidade</span>
        </a>
        <div class="region-label"><span class="region-dot"></span>{{ $isJourneyPreview ? 'REGIONAL AURORA' : 'VALE DO AÇO' }}<span class="region-label__tag">IBUC</span></div>
        @include($isJourneyPreview ? 'partials.journey-nav' : 'partials.preview-nav')
        <div class="sidebar__bottom">
            <div class="responsive-note">
                <span class="responsive-note__icons"><x-ui.icon name="monitor" :size="19" /><x-ui.icon name="phone" :size="16" /></span>
                <strong>Perto, em qualquer tela.</strong>
                <p>Uma experiência para o celular e o computador.</p>
            </div>
            <div class="preview-profile"><span class="avatar">I</span><div><strong data-preview-person>{{ $isJourneyPreview ? 'Bruno Exemplo' : 'Equipe IBUC' }}</strong><small data-preview-role>{{ $isJourneyPreview ? 'Responsável · demonstração' : 'Avaliação de interface' }}</small></div><x-ui.icon name="sparkles" :size="17" /></div>
        </div>
    </aside>

    <div class="workspace">
        <header class="topbar">
            <button class="icon-button mobile-menu-button" type="button" data-open-dialog="navigation-dialog" aria-label="Abrir navegação"><x-ui.icon name="menu" /></button>
            <a href="{{ $isJourneyPreview ? '#familia' : '#apresentacao' }}" class="mobile-brand" aria-label="IBUC, apresentação"><img src="{{ asset('brand/ibuc-original.png') }}" alt="IBUC" width="43" height="26"></a>
            <div class="breadcrumbs"><span>Experiência IBUC</span><span class="breadcrumbs__divider">/</span><strong>{{ $isJourneyPreview ? 'Nossa comunidade' : 'Identidade visual' }}</strong></div>
            <div class="topbar__actions">
                <span class="demo-label"><span></span>Prévia interativa</span>
                @if($isJourneyPreview)
                <button class="icon-button" type="button" data-open-dialog="reset-dialog" aria-label="Reiniciar os exemplos"><x-ui.icon name="reset" /></button>
                @else
                <button class="icon-button" type="button" data-open-dialog="search-dialog" aria-label="Buscar componentes"><x-ui.icon name="search" /></button>
                <button class="icon-button notification-button" type="button" data-open-dialog="notifications-dialog" aria-label="Abrir exemplo de notificação"><x-ui.icon name="bell" /><span class="notification-dot"></span></button>
                @endif
            </div>
        </header>
        <main id="conteudo" class="main" tabindex="-1">
            @yield('content')
        </main>
        <footer class="page-footer"><span><strong>IBUC</strong> · Instituto Bíblico Único Caminho</span><span>Uma comunidade. O mesmo cuidado.</span></footer>
    </div>

    <dialog id="navigation-dialog" class="navigation-dialog" aria-labelledby="navigation-title">
        <div class="dialog-heading"><h2 id="navigation-title">{{ $isJourneyPreview ? 'Nossa comunidade' : 'Explorar a identidade' }}</h2><button type="button" class="icon-button" data-close-dialog aria-label="Fechar navegação"><x-ui.icon name="close" /></button></div>
        @include($isJourneyPreview ? 'partials.journey-nav' : 'partials.preview-nav')
    </dialog>
    @unless($isJourneyPreview)
    <dialog id="search-dialog" class="search-dialog" aria-labelledby="search-title">
        <div class="dialog-heading"><h2 id="search-title">O que você quer explorar?</h2><button type="button" class="icon-button" data-close-dialog aria-label="Fechar busca"><x-ui.icon name="close" /></button></div>
        <label class="sr-only" for="component-search">Buscar componente</label>
        <div class="search-control"><x-ui.icon name="search" /><input id="component-search" type="search" placeholder="Busque por cores, campos, botões…" autocomplete="off"></div>
        <nav class="search-results" aria-label="Resultados da busca">
            <a href="#fundamentos" data-search-item="cores paleta fonte tipografia marca fundamentos"><x-ui.icon name="palette" /><span>Cores e tipografia<small>A base da nossa identidade</small></span><x-ui.icon name="arrow-right" :size="17" /></a>
            <a href="#componentes" data-search-item="botões botoes ações acoes componentes"><x-ui.icon name="grid" /><span>Botões e ações<small>Hierarquia e estados</small></span><x-ui.icon name="arrow-right" :size="17" /></a>
            <a href="#formularios" data-search-item="inputs campos formulários formularios select checkbox switch"><x-ui.icon name="sliders" /><span>Campos e formulários<small>Preenchimento com clareza</small></span><x-ui.icon name="arrow-right" :size="17" /></a>
            <a href="#em-uso" data-search-item="família familia avisos agenda confirmação confirmacao"><x-ui.icon name="users" /><span>A identidade em uso<small>Um pouco da experiência da família</small></span><x-ui.icon name="arrow-right" :size="17" /></a>
        </nav>
        <p class="search-empty" hidden>Nenhum componente encontrado. Tente “campos” ou “cores”.</p>
    </dialog>
    <dialog id="notifications-dialog" class="notification-dialog" aria-labelledby="notifications-title">
        <div class="dialog-heading"><h2 id="notifications-title">Seus avisos</h2><button type="button" class="icon-button" data-close-dialog aria-label="Fechar avisos"><x-ui.icon name="close" /></button></div>
        <x-ui.badge tone="warning" dot>Exemplo de notificação</x-ui.badge>
        <h3>Uma atualização sobre o próximo encontro</h3><p>Um aviso importante espera sua confirmação. Abra para conhecer o padrão de ciência.</p>
        <x-ui.button href="#em-uso" icon="arrow-right">Ver o aviso</x-ui.button>
    </dialog>
    @endunless
    <div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true" hidden><x-ui.icon name="check-circle" :size="19" /><span></span><button class="icon-button" type="button" aria-label="Dispensar mensagem" data-dismiss-toast><x-ui.icon name="close" :size="16" /></button></div>
</body>
</html>
