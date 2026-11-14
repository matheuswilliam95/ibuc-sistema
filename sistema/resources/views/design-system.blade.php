@extends('layouts.preview')

@section('content')
    <section id="apresentacao" class="intro">
        <div><div class="eyebrow">NOSSO JEITO DE CONECTAR</div><h1>Identidade visual<span class="title-dot">.</span></h1><p>O mesmo cuidado, em cada detalhe da experiência.</p></div>
        <x-ui.button variant="secondary" href="#em-uso" icon="eye">Ver a identidade em uso</x-ui.button>
    </section>

    <section class="hero" aria-labelledby="hero-title">
        <div class="hero__copy">
            <span class="hero__eyebrow"><span class="tiny-spark">✳</span> FAMÍLIA, FÉ E CONEXÃO</span>
            <h2 id="hero-title">Mais perto.<br>No mesmo caminho.</h2>
            <p>Uma interface leve, acolhedora e fácil de usar.<br>Para que o mais importante seja estar junto.</p>
            <a href="#fundamentos" class="text-link">Conheça a nossa base visual <x-ui.icon name="arrow-right" :size="17" /></a>
        </div>
        <img class="hero__art" src="{{ asset('brand/caminho.svg') }}" width="380" height="280" alt="" aria-hidden="true">
        <span class="hero__edition">GUIA VISUAL / 01</span>
    </section>

    <nav class="section-tabs" aria-label="Explorar a identidade">
        <a href="#fundamentos" class="section-tab is-active" aria-current="location"><span>01</span>Fundamentos</a>
        <a href="#componentes" class="section-tab"><span>02</span>Componentes</a>
        <a href="#em-uso" class="section-tab"><span>03</span>Em uso</a>
        <span class="section-tabs__note">Pensado para todas as telas <x-ui.icon name="monitor" :size="16" /><x-ui.icon name="phone" :size="14" /></span>
    </nav>

    <section id="fundamentos" class="guide-section">
        <div class="section-heading"><div><span class="eyebrow">01 / FUNDAMENTOS</span><h2>Uma base, muitas possibilidades.</h2></div><p>As cores da marca, com espaço para respirar.</p></div>
        <div class="foundation-grid">
            <article class="panel palette-panel" aria-labelledby="palette-title">
                <div class="panel-heading"><h3 id="palette-title">Cores que nos conectam</h3><span>Toque para copiar</span></div>
                <div class="swatches">
                    @foreach([
                        ['Verde caminho', '#326748', 'primary', 'Ações principais'],
                        ['Lima acolhedor', '#D9EC9A', 'highlight', 'Detalhes e destaque'],
                        ['Areia suave', '#F7F8F5', 'canvas', 'Fundo da experiência'],
                        ['Coral encontro', '#C75447', 'coral', 'Acento do nível 1'],
                        ['Azul descoberta', '#3B7492', 'blue', 'Acento do nível 4'],
                    ] as [$name, $hex, $tone, $usage])
                        <button class="swatch swatch--{{ $tone }}" type="button" data-copy="{{ $hex }}" aria-label="Copiar {{ $name }}, {{ $hex }}">
                            <span class="swatch__color"><span><x-ui.icon name="copy" :size="16" /></span></span>
                            <strong>{{ $name }}</strong><code>{{ $hex }}</code><small>{{ $usage }}</small>
                        </button>
                    @endforeach
                </div>
                <p class="panel-footnote"><span class="color-dots" aria-hidden="true"><i></i><i></i><i></i><i></i></span>A marca continua colorida. A interface ganha equilíbrio.</p>
            </article>
            <article class="panel type-panel" aria-labelledby="type-title">
                <div class="panel-heading"><h3 id="type-title">Palavras com clareza</h3><span>Manrope</span></div>
                <div class="type-specimen" aria-hidden="true">Aa<span>↗</span></div>
                <p class="type-sentence">Aprender. Compartilhar.<br>Caminhar juntos.</p>
                <div class="type-sizes"><span><b>600</b> Títulos</span><span><b>400</b> Textos</span></div>
            </article>
        </div>
    </section>

    <section id="componentes" class="guide-section">
        <div class="section-heading"><div><span class="eyebrow">02 / COMPONENTES</span><h2>Consistência que facilita.</h2></div><p>Reconhecer uma ação também é se sentir em casa.</p></div>
        <article class="panel buttons-panel">
            <div class="panel-heading"><div><h3>Um próximo passo sempre claro</h3><p>Botões com hierarquia, estados e o mesmo jeito de interagir.</p></div><div class="segmented" role="group" aria-label="Tamanho dos botões de exemplo"><button type="button" class="is-active" aria-pressed="true" data-button-size="md">Padrão</button><button type="button" aria-pressed="false" data-button-size="sm">Compacto</button></div></div>
            <div class="button-specimens" id="button-specimens">
                <div><x-ui.button icon="plus" data-demo-action="Aviso de exemplo preparado.">Criar aviso</x-ui.button><span>Principal</span></div>
                <div><x-ui.button variant="secondary" icon="calendar" data-demo-action="Seu próximo encontro está no exemplo abaixo." data-demo-scroll="em-uso">Ver agenda</x-ui.button><span>Secundário</span></div>
                <div><x-ui.button variant="ghost" icon="arrow-right" data-demo-action="Você está explorando a identidade do IBUC.">Saiba mais</x-ui.button><span>Discreto</span></div>
                <div><x-ui.button variant="danger" data-open-dialog="cancel-dialog">Cancelar evento</x-ui.button><span>Ação de atenção</span></div>
                <div><x-ui.button disabled>Indisponível</x-ui.button><span>Desabilitado</span></div>
            </div>
            <div class="buttons-panel__note"><x-ui.icon name="info" :size="15" />Experimente os botões. Esta prévia usa apenas exemplos.</div>
        </article>
    </section>

    <section id="formularios" class="form-state-grid guide-section" aria-label="Campos, estados e opções">
        <article class="panel form-panel">
            <div class="panel-heading"><div><span class="eyebrow">PREENCHER SEM COMPLICAR</span><h3>Campos que orientam.</h3></div><span class="mini-index">01–06</span></div>
            <form id="preview-form" novalidate>
                <div class="form-grid">
                    <x-ui.input name="name" label="Nome completo" placeholder="Nome de exemplo" autocomplete="off" hint="Use um nome fictício nesta prévia." required minlength="2" />
                    <x-ui.select name="classroom" label="Turma">
                        <option value="">Selecione uma turma</option>
                        <option value="nivel-1">Nível 1 · Pequenos começos</option>
                        <option value="nivel-2">Nível 2 · Novas descobertas</option>
                        <option value="nivel-3">Nível 3 · Crescendo juntos</option>
                        <option value="nivel-4">Nível 4 · Mais longe</option>
                    </x-ui.select>
                    <x-ui.input name="email" label="E-mail" type="email" placeholder="exemplo@ibuc.local" autocomplete="off" />
                    <x-ui.input name="example-phone" label="Telefone" type="tel" value="(31) 9" :error="'Exemplo: informe o DDD e os 9 dígitos.'" readonly />
                </div>
                <x-ui.textarea id="example-note" name="note" label="Observação" :rows="2" optional placeholder="Há algo que você gostaria de compartilhar?" />
                <p class="form-feedback" id="form-feedback" role="status" hidden></p>
                <div class="form-actions"><span><x-ui.icon name="check-circle" :size="16" />Apenas uma demonstração visual</span><x-ui.button type="submit" icon="check">Experimentar formulário</x-ui.button></div>
            </form>
        </article>
        <div class="state-stack">
            <article class="panel status-panel"><div class="panel-heading"><h3>Cada estado tem seu lugar</h3><span>Com clareza</span></div>
                <div class="badge-samples"><x-ui.badge tone="success" dot>Confirmado</x-ui.badge><x-ui.badge tone="warning" dot>Aguardando</x-ui.badge><x-ui.badge tone="info" dot>Informativo</x-ui.badge><x-ui.badge tone="danger" dot>Cancelado</x-ui.badge></div>
                <div class="level-samples"><span class="field__label">Cor e nome, sempre juntos</span><div><x-ui.badge tone="level-1">Nível 1</x-ui.badge><x-ui.badge tone="level-2">Nível 2</x-ui.badge><x-ui.badge tone="level-3">Nível 3</x-ui.badge><x-ui.badge tone="level-4">Nível 4</x-ui.badge></div></div>
            </article>
            <article class="panel preferences-panel"><div class="panel-heading"><h3>Você no controle</h3><x-ui.icon name="sliders" :size="17" /></div>
                <x-ui.switch name="event-alert" label="Lembretes de encontros" hint="Um aviso antes de começar." checked />
                <x-ui.switch name="weekly-news" label="Novidades da semana" hint="O que acontece na comunidade." />
                <label class="checkbox-label"><input type="checkbox" name="demo-check"><span>Quero acompanhar o que há de novo.</span></label>
            </article>
        </div>
    </section>

    <section id="em-uso" class="guide-section">
        <div class="section-heading"><div><span class="eyebrow">03 / A IDENTIDADE EM USO</span><h2>O cuidado vira experiência.</h2></div><p>Um pouco do que as famílias vão encontrar.</p></div>
        <div class="experience-grid">
            <article class="panel notice-card">
                <div class="panel-heading"><div class="icon-tile icon-tile--amber"><x-ui.icon name="bell" :size="21" /></div><x-ui.badge tone="warning" id="notice-status">Ciência necessária</x-ui.badge></div>
                <span class="card-eyebrow">AVISO DA TURMA · EXEMPLO</span><h3>Uma mudança no nosso encontro.</h3>
                <p>O encontro de terça-feira foi cancelado. Nossa próxima aula continua na semana seguinte, no mesmo horário.</p>
                <div class="notice-card__meta"><span class="avatar avatar--small">I</span><span>Coordenação IBUC<small>Para os responsáveis da turma</small></span></div>
                <div class="notice-card__footer"><span id="acknowledgement-hint">Confirme que recebeu este aviso.</span><x-ui.button id="acknowledgement-button" icon="check">Estou ciente</x-ui.button></div>
            </article>
            <article class="panel event-card">
                <div class="event-art" aria-hidden="true"><span class="event-art__sun"></span><x-ui.icon name="book" :size="54" /><span class="event-art__caption">BONS ENCONTROS<br>FAZEM PARTE DO CAMINHO.</span><span class="event-art__leaf"></span></div>
                <div class="event-card__body"><div class="event-date"><strong>20</strong><span>NOV</span></div><div class="event-card__details"><x-ui.badge tone="success">Encontro de famílias</x-ui.badge><h3>Família no IBUC</h3><p><x-ui.icon name="clock" :size="15" />19h30 <span>·</span><x-ui.icon name="pin" :size="15" />Núcleo de demonstração</p></div><button class="icon-button event-details-button" type="button" data-open-dialog="event-dialog" aria-label="Ver detalhes de Família no IBUC"><x-ui.icon name="arrow-up-right" /></button></div>
            </article>
        </div>
        <div class="closing-note"><x-ui.icon name="heart" :size="17" /><span>Uma base compartilhada. Espaço para a nossa comunidade crescer.</span><a class="text-link" href="#apresentacao">Voltar ao início <span aria-hidden="true">↑</span></a></div>
    </section>

    <dialog id="cancel-dialog" class="action-dialog" aria-labelledby="cancel-title">
        <div class="dialog-heading"><span class="icon-tile icon-tile--coral"><x-ui.icon name="calendar" /></span><button type="button" class="icon-button" data-close-dialog aria-label="Fechar confirmação"><x-ui.icon name="close" /></button></div>
        <h2 id="cancel-title">Cancelar este encontro?</h2><p>Este é um exemplo de confirmação para uma ação importante. Os responsáveis receberiam um aviso sobre a mudança.</p>
        <div class="dialog-actions"><x-ui.button variant="secondary" data-close-dialog>Manter encontro</x-ui.button><x-ui.button variant="danger" data-confirm-cancel>Confirmar cancelamento</x-ui.button></div>
    </dialog>
    <dialog id="event-dialog" class="action-dialog" aria-labelledby="event-title">
        <div class="dialog-heading"><span class="icon-tile icon-tile--green"><x-ui.icon name="users" /></span><button type="button" class="icon-button" data-close-dialog aria-label="Fechar detalhes"><x-ui.icon name="close" /></button></div>
        <x-ui.badge tone="success">Encontro de demonstração</x-ui.badge><h2 id="event-title">Família no IBUC</h2><p>Um momento para conhecer o novo módulo, compartilhar o que vem pela frente e caminhar juntos.</p>
        <div class="event-dialog-meta"><span><x-ui.icon name="calendar" />20 de novembro</span><span><x-ui.icon name="clock" />19h30</span><span><x-ui.icon name="pin" />Núcleo de demonstração</span></div>
        <x-ui.alert tone="info" title="Por enquanto, só uma prévia">Este encontro demonstra o visual da agenda. Não há uma inscrição real.</x-ui.alert>
        <x-ui.button variant="secondary" data-close-dialog>Entendi</x-ui.button>
    </dialog>
@endsection
