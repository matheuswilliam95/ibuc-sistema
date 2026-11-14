@extends('layouts.preview', ['isJourneyPreview' => true])

@section('content')
    <div class="journey-demo"><x-ui.icon name="sparkles" /><p><strong>Um pouco do nosso dia a dia.</strong> Experimente os três perfis com dados fictícios. Ao recarregar, os exemplos recomeçam.</p></div>
    <noscript><x-ui.alert tone="warning" title="Ative o JavaScript para experimentar">Esta prévia interativa não envia formulários. Os controles ficam desativados sem JavaScript.</x-ui.alert></noscript>
    <header class="journey-intro"><div><span class="eyebrow">FAMÍLIA, EQUIPE E COMUNIDADE</span><h1 id="journey-heading" tabindex="-1">Minha família</h1><p id="journey-description">Olá, Bruno. Um lugar para acompanhar quem você ama.</p></div><span class="journey-region"><x-ui.icon name="pin" :size="16" />Regional Aurora · exemplo</span></header>
    <nav class="journey-switch" aria-label="Experimentar um perfil">
        <a href="#familia" data-journey="familia" aria-current="page"><x-ui.icon name="heart" :size="18" />Família</a>
        <a href="#professor" data-journey="professor"><x-ui.icon name="book" :size="18" />Professor</a>
        <a href="#secretaria" data-journey="secretaria"><x-ui.icon name="building" :size="18" />Secretaria</a>
    </nav>

    <section id="familia" data-journey-panel aria-label="Jornada da família">
        <div class="journey-grid">
            <div class="journey-stack">
                <article class="panel">
                    <x-ui.form-header title="Quem vamos acompanhar?" eyebrow="MINHA FAMÍLIA" icon="users" />
                    <fieldset class="journey-fields" disabled data-prototype-controls>
                        <x-ui.select name="child" id="family-child" label="Aluno" icon="user">
                            @foreach($preview['family']['children'] as $child)<option value="{{ $child['id'] }}">{{ $child['name'] }}</option>@endforeach
                        </x-ui.select>
                    </fieldset>
                    <div class="journey-person"><span class="avatar" id="child-avatar">L</span><div><strong id="child-name">Lucas Exemplo</strong><p id="child-classroom">Ponte · Caminhos</p></div><x-ui.badge tone="level-3" id="child-level">Nível 3</x-ui.badge></div>
                </article>
                <article class="panel journey-notice">
                    <div class="panel-heading"><span class="icon-tile icon-tile--amber"><x-ui.icon name="bell" /></span><x-ui.badge tone="warning" id="family-notice-status">Ciência pendente</x-ui.badge></div>
                    <span class="eyebrow">AVISO IMPORTANTE · VERSÃO 1</span>
                    <h2>{{ $preview['notice']['title'] }}</h2>
                    <p>O encontro de <strong>17 de novembro</strong> não acontecerá. A próxima aula será em <strong>24 de novembro, às 19h</strong>, no núcleo de cada turma.</p>
                    <div class="journey-context"><x-ui.icon name="users" :size="18" /><span>Este aviso se refere a <strong id="notice-child">Lucas</strong>.</span></div>
                    <p class="journey-caption">Coordenação da Regional Aurora · publicado em 14 nov. 2026</p>
                    <div class="journey-action"><p id="family-receipt">Confirme para sabermos que esta informação chegou até você.</p><x-ui.button id="review-notice" icon="check" disabled><span id="notice-action-label">Estou ciente por Lucas</span></x-ui.button></div>
                </article>
            </div>
            <aside class="journey-stack" aria-label="Acompanhamento da família">
                <article class="panel journey-progress"><div class="panel-heading"><h3>Estamos em contato</h3><x-ui.icon name="heart" /></div><strong class="journey-number" id="family-progress">0 de 2</strong><p>filhos com ciência confirmada deste aviso.</p><ul class="journey-checklist" id="family-checklist">
                    @foreach($preview['family']['children'] as $child)<li><span>{{ $child['shortName'] }}</span><span class="badge badge--warning" data-child-receipt="{{ $child['id'] }}">Pendente</span></li>@endforeach
                </ul><p class="journey-caption">Cada filho tem sua própria confirmação.</p></article>
                <article class="panel"><div class="panel-heading"><h3>Próximos encontros</h3><x-ui.icon name="calendar" /></div>
                    <div class="journey-event"><span class="journey-date"><strong>17</strong>NOV</span><div><strong>Encontro de terça</strong><x-ui.badge tone="danger">Cancelado</x-ui.badge></div></div>
                    <div class="journey-event"><span class="journey-date"><strong>24</strong>NOV</span><div><strong>Aula da turma</strong><p>19h · no seu núcleo</p><x-ui.badge tone="success">Mantida</x-ui.badge></div></div>
                </article>
            </aside>
        </div>
    </section>

    <section id="professor" data-journey-panel aria-label="Jornada do professor" hidden>
        <div class="journey-grid">
            <article class="panel">
                <x-ui.form-header title="Presença com cuidado" eyebrow="JARDIM · SEMENTES · NÍVEL 1" icon="book" />
                <fieldset class="journey-fields" disabled data-prototype-controls><x-ui.select name="classroom-date" id="attendance-session" label="Encontro" icon="calendar">
                    @foreach($preview['attendance']['sessions'] as $session)<option value="{{ $session['id'] }}">{{ $session['label'] }}</option>@endforeach
                </x-ui.select></fieldset>
                <div id="cancelled-attendance" class="journey-spaced" hidden><x-ui.alert tone="info" title="Este encontro foi cancelado">Não há chamada ou faltas para 17 de novembro. A aula de 24 de novembro continua disponível.</x-ui.alert></div>
                <div id="attendance-list">
                    <div class="journey-list-heading"><p id="attendance-list-instruction">Marque cada aluno antes de fechar.</p><x-ui.button variant="ghost" id="mark-all-present" size="sm" disabled>Todos presentes</x-ui.button></div>
                    @foreach($preview['attendance']['students'] as $student)
                    <fieldset class="attendance-row" data-student="{{ $student['id'] }}" disabled data-prototype-controls>
                        <legend><span class="avatar">{{ $student['initial'] }}</span>{{ $student['name'] }}</legend>
                        <div class="attendance-choices">
                            @foreach(['present' => 'Presente', 'absent' => 'Falta', 'justified' => 'Justificada'] as $value => $label)
                            <label class="attendance-choice"><input type="radio" name="attendance-{{ $student['id'] }}" value="{{ $value }}" aria-label="{{ $label }}: {{ $student['name'] }}"><span>{{ $label }}</span></label>
                            @endforeach
                        </div>
                    </fieldset>
                    @endforeach
                </div>
                <p id="attendance-feedback" class="journey-feedback" role="alert" hidden></p>
                <div class="journey-action"><p id="attendance-hint">Nenhum aluno foi marcado ainda.</p><x-ui.button id="review-attendance" icon="check" disabled><span id="attendance-action-label">Conferir chamada</span></x-ui.button></div>
            </article>
            <aside class="journey-stack" aria-label="Resumo da chamada">
                <article class="panel"><div class="panel-heading"><h3>O encontro em números</h3><x-ui.badge id="attendance-status">Em preenchimento</x-ui.badge></div><dl class="journey-totals"><div><dt>Presentes</dt><dd id="count-present">0</dd></div><div><dt>Faltas</dt><dd id="count-absent">0</dd></div><div><dt>Justificadas</dt><dd id="count-justified">0</dd></div><div><dt>A marcar</dt><dd id="count-pending">3</dd></div></dl></article>
                <article class="panel journey-quiet"><span class="icon-tile icon-tile--green"><x-ui.icon name="heart" /></span><h3>Uma presença, uma história.</h3><p>As faltas ajudam a perceber quem precisa de acolhimento. Uma ausência justificada continua identificada à parte.</p><p class="journey-caption">Nesta chamada aparecem apenas os alunos da turma Sementes, do Núcleo Jardim.</p></article>
            </aside>
        </div>
    </section>

    <section id="secretaria" data-journey-panel aria-label="Jornada da secretaria" hidden>
        <div class="journey-grid">
            <div class="journey-stack">
                <article class="panel"><div class="panel-heading"><span class="icon-tile icon-tile--green"><x-ui.icon name="package" /></span><x-ui.badge tone="info" id="order-status">Pronto para retirada</x-ui.badge></div><span class="eyebrow">{{ $preview['order']['id'] }} · NÚCLEO JARDIM</span><h2 class="journey-card-title">Material para {{ $preview['order']['beneficiary'] }}</h2><p class="journey-caption">Solicitado por {{ $preview['order']['requester'] }}</p><div class="journey-product"><span class="journey-book"><x-ui.icon name="book" :size="32" /></span><div><strong>{{ $preview['order']['product'] }}</strong><p>{{ $preview['order']['quantity'] }} unidades solicitadas</p></div></div><dl class="journey-order-totals"><div><dt>Entregues</dt><dd id="order-delivered">0</dd></div><div><dt>A entregar</dt><dd id="order-remaining">2</dd></div></dl></article>
                <article class="panel" id="delivery-panel"><x-ui.form-header title="Registrar retirada" eyebrow="ENTREGA DE MATERIAL" icon="package" />
                    <form id="delivery-form" novalidate><fieldset class="journey-fields" disabled data-prototype-controls>
                        <x-ui.input name="quantity" id="delivery-quantity" label="Quantidade a entregar agora" type="number" min="1" max="2" step="1" value="1" icon="hash" required hint="Você pode registrar uma entrega parcial." />
                        <x-ui.input name="receiver" id="delivery-receiver" label="Quem está recebendo?" value="Bruno Exemplo" maxlength="100" autocomplete="off" required hint="Use apenas um nome fictício nesta prévia." />
                        <p id="delivery-feedback" class="journey-feedback" role="alert" hidden></p>
                        <x-ui.button type="submit" icon="check">Conferir entrega</x-ui.button>
                    </fieldset></form>
                    <div id="delivery-complete" hidden><x-ui.alert tone="success" title="Tudo entregue!">As duas unidades deste pedido já foram retiradas. Você pode consultar os registros no histórico do pedido.</x-ui.alert></div>
                </article>
            </div>
            <aside class="journey-stack" aria-label="Histórico do pedido"><article class="panel"><div class="panel-heading"><h3>Cada entrega registrada</h3><x-ui.icon name="clock" /></div><p id="delivery-empty" class="journey-caption">Ainda não houve retirada. Ao registrar, você verá aqui a quantidade, o recebedor e o atendente.</p><ol class="journey-history" id="delivery-history"></ol></article><article class="panel journey-quiet"><h3>Organizado até a última unidade.</h3><p>Se uma unidade for entregue agora, a outra continua pendente no mesmo pedido.</p><p class="journey-caption">Este exemplo registra somente a retirada do material. Não há cobrança ou pagamento.</p></article></aside>
        </div>
    </section>

    <dialog id="notice-confirm-dialog" aria-labelledby="notice-confirm-title"><div class="dialog-heading"><h2 id="notice-confirm-title">Confirmar ciência</h2><button class="icon-button" data-close-dialog aria-label="Fechar confirmação de ciência"><x-ui.icon name="close" /></button></div><p id="notice-confirm-summary"></p><p class="journey-caption journey-spaced">Você confirma apenas para o filho indicado. A confirmação do irmão é independente.</p><div class="dialog-actions"><x-ui.button variant="secondary" data-close-dialog>Voltar</x-ui.button><x-ui.button id="confirm-notice" icon="check">Confirmar ciência</x-ui.button></div></dialog>
    <dialog id="attendance-confirm-dialog" aria-labelledby="attendance-confirm-title"><div class="dialog-heading"><h2 id="attendance-confirm-title">Fechar esta chamada?</h2><button class="icon-button" data-close-dialog aria-label="Fechar conferência da chamada"><x-ui.icon name="close" /></button></div><p>Turma Sementes · Núcleo Jardim<br>24 de novembro de 2026</p><p id="attendance-confirm-summary" class="journey-spaced"></p><p class="journey-caption journey-spaced">Confira as marcações. Após fechar, elas ficarão disponíveis para consulta nesta prévia.</p><div class="dialog-actions"><x-ui.button variant="secondary" data-close-dialog>Revisar marcações</x-ui.button><x-ui.button id="confirm-attendance" icon="check">Fechar chamada</x-ui.button></div></dialog>
    <dialog id="delivery-confirm-dialog" aria-labelledby="delivery-confirm-title"><div class="dialog-heading"><h2 id="delivery-confirm-title">Conferir entrega</h2><button class="icon-button" data-close-dialog aria-label="Fechar conferência da entrega"><x-ui.icon name="close" /></button></div><p id="delivery-confirm-summary"></p><p id="delivery-confirm-balance" class="journey-caption journey-spaced"></p><p id="delivery-confirm-error" class="journey-feedback" role="alert" hidden></p><div class="dialog-actions"><x-ui.button variant="secondary" data-close-dialog>Voltar</x-ui.button><x-ui.button id="confirm-delivery" icon="check">Registrar entrega</x-ui.button></div></dialog>
    <dialog id="reset-dialog" aria-labelledby="reset-title"><div class="dialog-heading"><h2 id="reset-title">Recomeçar os exemplos?</h2><button class="icon-button" data-close-dialog aria-label="Fechar reinício"><x-ui.icon name="close" /></button></div><p>As confirmações, marcações e entregas desta demonstração voltarão ao início.</p><div class="dialog-actions"><x-ui.button variant="secondary" data-close-dialog>Continuar explorando</x-ui.button><x-ui.button id="reset-journeys" icon="reset">Reiniciar exemplos</x-ui.button></div></dialog>
    <p class="sr-only" id="journey-announcement" role="status" aria-live="polite" aria-atomic="true"></p>
    <script type="application/json" id="journey-data">@json($preview)</script>
@endsection
