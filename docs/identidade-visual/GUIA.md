# Identidade visual da experiência IBUC

Esta é a primeira proposta implementada para avaliação. Ela preserva o logotipo recebido e estabelece uma base comum para as próximas telas. A aprovação da direção sobre marca e a avaliação com famílias/professores previstas na T04 continuam pendentes.

## Abrir a prévia

Na raiz do projeto:

```powershell
Set-Location -LiteralPath sistema
php artisan serve --host=127.0.0.1 --port=8000 --no-reload
```

Abra [a identidade visual](http://127.0.0.1:8000/identidade-visual). A raiz do aplicativo leva à mesma página. As rotas de demonstração só são registradas nos ambientes `local` e `testing`.

Os exemplos funcionam no navegador sem cadastro ou banco. Nenhum formulário envia dados ou persiste preferências. O estado da prévia é descartado ao recarregar.

Em uma instalação nova: `composer install`, copie `.env.example` para `.env` caso ele não exista, execute `php artisan key:generate`, `npm ci` e `npm run build` dentro de `sistema/`. O ambiente desta entrega usou PHP 8.3.30, compatível com o Laravel 13 instalado; o roteiro mantém PHP 8.4 como alvo do ambiente definitivo.

## Direção visual

O verde dá consistência às ações; fundos claros e quentes deixam a leitura respirar. Lima, coral e azul oferecem acentos sem transformar cada tela em uma combinação diferente. Número, texto e ícone acompanham as cores de estado e de nível.

O logotipo em `sistema/public/brand/ibuc-original.png` é o arquivo original incorporado à ficha de identificação da equipe Caravelas, sem redesenho. A ilustração `caminho.svg` é um elemento decorativo desta proposta, separado da marca. Substitua o logotipo por um arquivo oficial de maior resolução quando ele estiver disponível, preservando a proporção.

A fonte Manrope é hospedada junto à aplicação. A licença está em `sistema/resources/fonts/OFL-Manrope.txt`; a prévia não depende de serviços externos de fontes.

| Papel | Valor | Uso |
|---|---|---|
| Ação principal | `#326748` | Botão principal, foco e indicação positiva |
| Ação em hover | `#254F37` | Resposta ao apontar ou interagir |
| Destaque | `#D9EC9A` | Superfície de destaque com texto escuro |
| Fundo | `#F7F8F5` | Tela e áreas de apoio |
| Superfície | `#FFFFFF` | Formulários e conteúdo |
| Texto principal | `#20372C` | Títulos e rótulos |
| Texto secundário | `#647268` | Orientação e metadados legíveis |
| Acento coral | `#C75447` | Amostras e decoração |
| Texto de nível 1 | `#AC463C` | Tom mais escuro sobre fundo coral suave |
| Nível 2 | `#8A6824` | Rótulo sobre amarelo suave |
| Nível 3 | `#3E7552` | Rótulo sobre verde suave |
| Nível 4 | `#3B7492` | Rótulo sobre azul suave |

Esses valores estão definidos em `sistema/resources/css/tokens.css`. Esse arquivo também contém espaçamento, fonte, raios de borda, sombras e duração das transições. Use variáveis semânticas, por exemplo `--color-primary`, em vez de repetir valores hexadecimais nas telas.

### Tamanhos para leitura confortável

A escala é compartilhada entre computador e celular. Layouts menores reorganizam os elementos sem reduzir as letras para caber.

| Elemento | Padrão |
|---|---|
| Texto principal e conteúdo dos campos, inclusive placeholder | 16 px |
| Rótulos, botões e navegação | 14 px |
| Orientações, erros, etiquetas e metadados | Pelo menos 12 px |
| Títulos de painéis e seções | 16 a 24 px, conforme a hierarquia |
| Altura dos campos e botões principais | Pelo menos 48 px |
| Botão compacto | Pelo menos 44 px, mantendo texto de 14 px |

Use os tokens `--font-size-base`, `--font-size-label`, `--font-size-control` e `--font-size-caption`. Evite estilos locais de 8 a 11 px. Quando o conteúdo não couber, permita quebra de linha, aumente o espaço ou reorganize a grade. Não reduza os textos de apoio no celular.

## Usar os componentes

Os componentes Blade ficam em `sistema/resources/views/components/ui/`:

| Componente | Opções principais |
|---|---|
| `x-ui.button` | `variant`: primary, secondary, ghost, danger; `size`: sm, md, lg; `icon`; `href` ou `type` |
| `x-ui.input` | `name`, `label`, `type`, `hint`, `error`, ícone automático ou `icon` explícito e `optional` |
| `x-ui.select` | `name`, `label`, `hint`, ícone automático ou `icon` explícito e opções no slot |
| `x-ui.textarea` | `name`, `label`, `hint`, `error`, `rows`, ícone automático ou `icon` explícito e `optional` |
| `x-ui.switch` | `name`, `label`, `hint`; aceita `checked` e `disabled` |
| `x-ui.badge` | `tone`: neutral, success, warning, danger, info, level-1 a level-4; `dot` ou `icon` |
| `x-ui.alert` | `tone`, `title` e explicação no slot |
| `x-ui.form-header` | `title`, `eyebrow`, `icon` e conteúdo complementar no slot |
| `x-ui.icon` | `name` e `size`; sem significado acessível próprio quando acompanha texto |

```blade
<x-ui.input
    name="nome"
    label="Nome completo"
    hint="Informe o nome do responsável."
    required
/>

<x-ui.select name="turma" label="Turma">
    <option value="">Selecione uma turma</option>
    <option value="turma-exemplo">Turma de exemplo</option>
</x-ui.select>

<x-ui.button type="submit" icon="check">Salvar cadastro</x-ui.button>
<x-ui.button variant="secondary">Voltar</x-ui.button>
<x-ui.badge tone="warning" dot>Aguardando conferência</x-ui.badge>
```

O componente de campo associa rótulo, orientação e erro ao controle. Os atributos adicionais passam para o elemento correspondente; por exemplo, `wire:model` pode ser usado na futura integração com Livewire. O componente visual não concede acesso nem implementa validação no servidor.

### Cabeçalho suave para formulários

O componente `x-ui.form-header` está em avaliação no formulário e no bloco de preferências. Ele cria uma faixa verde muito clara que clareia em direção à direita, com uma divisória fina antes dos campos. O gradiente é definido pelo token `--gradient-form-header`, de `#EDF3E8` a `#FCFDFB`.

Use-o como primeiro elemento dentro de um `.panel`. Os recuos acompanham o painel no computador e no celular, e os cantos superiores conservam o mesmo arredondamento.

```blade
<article class="panel">
    <x-ui.form-header title="Dados do responsável" eyebrow="CADASTRO" icon="user" />
    {{-- Campos do formulário --}}
</article>
```

### Ícones nos campos como padrão

Campos usam ícones sempre que a informação pode ser representada de forma clara e o controle comporta a decoração. A escolha padrão fica em `sistema/app/Support/FieldIcon.php`: primeiro considera o tipo do input; depois o nome do campo. Nomes em português e inglês, vínculos aninhados como `responsaveis.0.telefone` e referências como `turma_id` são reconhecidos.

| Informação | Ícone padrão |
|---|---|
| Nome ou responsável | Pessoa |
| Turma, grupo ou nível | Grupo de pessoas |
| E-mail, telefone e senha | Envelope, telefone e cadeado |
| Data, horário e endereço | Calendário, relógio e localização |
| Igreja, núcleo ou regional | Edificação |
| Observação e mensagem | Balão de mensagem |
| Campo de texto ou seleção sem correspondência específica | Texto ou lista |

Use `icon="nome-do-icone"` para escolher outro símbolo da biblioteca e `:icon="false"` para uma exceção justificada. Não é necessário repetir a propriedade em cada formulário. Controles nativos como checkbox, radio, upload, seletor de cor e intervalo não recebem ícone adicional automaticamente.

```blade
{{-- O tipo e o nome já selecionam os ícones. --}}
<x-ui.input name="email" type="email" label="E-mail" />
<x-ui.select name="turma_id" label="Turma">
    <option value="">Selecione uma turma</option>
</x-ui.select>
<x-ui.textarea name="observacoes" label="Observações" optional />

{{-- Sobrescrita ou exceção consciente. --}}
<x-ui.input name="referencia" label="Referência" icon="book" />
<x-ui.input name="codigo" label="Código de verificação" :icon="false" />
```

Ícones são decorativos, não substituem rótulos e não recebem foco. O espaçamento do texto é ajustado pelo componente, a seta do select fica à direita e o ícone da área de texto se alinha à primeira linha.

## Regras para as próximas telas

- Reutilize os componentes e tokens existentes. Quando surgir um novo padrão, acrescente-o à biblioteca e à página de avaliação antes de repeti-lo em vários módulos.
- Aplique o padrão de ícones dos componentes aos campos sempre que viável. Ajuste o ícone por significado, sem acrescentar enfeites aos controles que já possuem um indicador nativo suficiente.
- Use um botão principal para a ação mais importante da área. Ações de atenção precisam de texto explícito e, quando necessário, confirmação. Links navegam; botões executam ações.
- Mantenha rótulos visíveis; placeholder só exemplifica o preenchimento. Mostre o erro em texto próximo ao campo e permita foco perceptível pelo teclado.
- Use a mesma família tipográfica, espaçamentos da escala e bordas de 10 px nos controles e 16 px nos painéis. Campos e botões principais têm pelo menos 48 px de altura; o botão compacto mantém 44 px. Os textos de entrada continuam com 16 px no computador e no celular.
- Respeite preferência por movimento reduzido. Diálogos usam o comportamento nativo de foco, Escape e fundo inativo; notificações de resultado têm anúncio para tecnologia assistiva.
- Em páginas reais, faça autorização e validação no Laravel/Livewire. O JavaScript desta prévia apenas demonstra os estados visuais. Substitua a tela de demonstração quando a experiência real for liberada.

## Organização dos arquivos

| Arquivo ou diretório em `sistema/` | Responsabilidade |
|---|---|
| `resources/css/tokens.css` | Valores comuns e cores por significado |
| `resources/css/base.css` | Estrutura da página, navegação e foco |
| `resources/css/components.css` | Botões, campos, seletores, estados, diálogos e alertas |
| `resources/css/showcase.css` | Composição da página de avaliação |
| `resources/css/responsive.css` | Adaptação do layout e dos exemplos às larguras |
| `resources/views/components/ui/` | Componentes Blade compartilhados |
| `resources/views/layouts/preview.blade.php` | Estrutura da demonstração |
| `resources/views/design-system.blade.php` | Exemplos para avaliação |
| `resources/js/app.js` | Interações locais da demonstração |

## Avaliar a proposta

Abra em computador e celular e experimente: copiar uma cor; alternar tamanho dos botões; abrir e dispensar confirmação; enviar o formulário vazio e preenchido; alternar preferências; confirmar um aviso; buscar “cores”; abrir os detalhes do evento.

Registre o que dificulta leitura ou entendimento e qual ajuste deseja. Esta entrega prepara a base visual da T04. As jornadas reais de matrícula, presença e pedidos, as permissões e os aceites institucionais pertencem às próximas entregas.

## Verificação desta entrega

Foram executados o build do Vite, a compilação de templates Blade, a verificação de formatação PHP e os testes iniciais do Laravel (2 testes, 4 asserções). As dependências passaram na checagem de requisitos do Composer.

No navegador, foram conferidos o formulário vazio/preenchido, o erro de formato de e-mail, a troca de tamanho dos botões, a busca, o diálogo de cancelamento, o menu móvel, o switch, a confirmação única do aviso e o detalhe de evento. Não houve erro ou aviso no console nessa inspeção.

As larguras de 320, 390 e 768 px foram verificadas sem rolagem horizontal, além do desktop na largura padrão do navegador. A prévia foi inspecionada visualmente e as rotas foram verificadas como ausentes no ambiente de produção. Isso não constitui certificação de acessibilidade nem teste em aparelhos Android físicos.

Na revisão de legibilidade, a escala mínima foi conferida no DOM e os campos, placeholders, rótulos e mensagens foram medidos nos três tamanhos. O botão compacto mantém 14 px de fonte e 44 px de altura. As grades, os ícones e as capturas foram atualizados para acompanhar os textos maiores.
