# Roteiro de implementação do sistema IBUC

**Objetivo do piloto:** uma família recebe o cancelamento de uma terça-feira, identifica o filho afetado e confirma a ciência. A coordenação vê quem ainda precisa ser contatado; o professor não lança falta naquele encontro. A secretaria consegue conduzir matrícula, inscrições e pedidos de material sem depender de conversas no WhatsApp.

Este é o roteiro de trabalho do Codex e da equipe. O [plano de produto](<output/pdf/Plano de implantação do sistema IBUC.pdf>) fundamenta as regras; o [backlog editável](<outputs/01a1da34-6809-7471-91a3-bee58d1ea8c0/Backlog de implantação IBUC.xlsx>) identifica cada entrega, decisão e área responsável. Os anexos do IBUC descrevem práticas de referência; uma prática do Vale do Aço só vale para outra regional quando adotada por ela.

**Ponto de partida:** na preparação deste roteiro a pasta continha o plano e o backlog, sem aplicação Laravel. Nenhuma entrega de implementação ou decisão da direção foi marcada como concluída. Na primeira execução, confira o estado atual antes de criar arquivos.

| Fase | Entregas | Resultado esperado | Janela indicativa |
|---|---|---|---|
| [F0](#f0) | T01–T05 | Regras, protótipos e igrejas do piloto definidos | Semanas 1–2 |
| [F1](#f1) | T06–T14 | Cadastro restrito e isolamento entre grupos | Semanas 3–5 |
| [F2](#f2) | T15–T26 | Comunicação, agenda, presença e pedidos em uso | Semanas 6–9 |
| [F3](#f3) | T27–T30 | Android e dois ciclos com as igrejas | Semanas 10–12 |
| [F4](#f4) | T31–T36 | IBUC em Casa, planejamento e galerias autorizadas | Semanas 13–17 |
| [F5](#f5) | T37–T41 | Formação dos colaboradores e reservas de espaços | Semanas 18–22 |
| [F6](#f6) | T42–T44 | Indicadores conferidos e expansão por regional | Semanas 23–25 |
| [F7](#f7) | T45–T46 | Tesouraria, pagamentos e conciliação | Após estabilização |

As janelas pressupõem dois desenvolvedores dedicados, apoio parcial de produto e testes e disponibilidade da direção. Reestime ao terminar a F0. O prazo de revisão da loja não está incluído.

## Como executar e retomar

1. Leia o [registro de execução](#retomada), a fase pedida e suas dependências. Confira os arquivos e as decisões vigentes; preserve mudanças existentes. Se o pedido for “continue”, retome a entrega em andamento ou a primeira elegível da fase aberta.
2. Trabalhe em uma entrega verificável por vez. Para uma funcionalidade, ligue banco, regra no servidor, tela, autorização e teste do comportamento. Se o pedido abranger uma fase, prossiga pelas entregas independentes sem pedir confirmação entre escolhas técnicas rotineiras.
3. Use os estados do backlog: **A fazer**, **Em execução**, **Em validação**, **Aceita**, **Bloqueada**. Uma proposta de governança pode estar pronta para validação sem que a direção a tenha aceitado. Não invente a conclusão de reuniões, testes em aparelhos ou exercícios de recuperação.
4. Ao encerrar a sessão, anote ID, mudança, verificação executada, resultado e próximo passo no registro. Se houver aceite na planilha, registre sua referência, sem sobrescrever o binário automaticamente. Se faltar uma decisão, indique qual ID bloqueia qual ação e avance no que não depende dela.
5. Encerre cada fase demonstrando o cenário de saída abaixo. A publicação, o uso de dados reais e a expansão dependem das evidências e da liberação correspondentes. Um teste automatizado não substitui o aceite institucional ou o piloto com famílias.

## Regras comuns a todas as fases

| Tema | Contrato de implementação |
|---|---|
| Escopo | Global → regional → núcleo/igreja → turma. Nível e módulo são pedagógicos. Curso, comissão e evento podem ter membros próprios; pertencer a um deles não abre a turma de origem. |
| Autorização | Verifique no servidor **ação + recurso + escopo + vínculo vigente**; negue por padrão. Aplique a regra antes de listar, contar, pesquisar, alterar, exportar ou servir arquivo, inclusive em ações Livewire, tarefas de fila e cache. O contexto enviado pelo cliente não comprova autoridade. |
| Papéis | Vários administradores globais podem gerir padrões e regionais sem acesso nominal irrestrito. Direção regional atua na sua regional; direção local no seu núcleo; coordenação nas turmas atribuídas; professor e auxiliar somente nas atribuições vigentes. Secretaria, mídia, patrimônio e tesouraria recebem permissões próprias. |
| Pessoa e família | Separe pessoa, conta, vínculo de responsabilidade, lotação e matrícula. Uma criança pode existir sem login. Um responsável pode representar vários filhos, com poderes diferentes por filho. Um professor que também é pai alterna entre os contextos sem somar privilégios. |
| Dados e menores | Por campo, declare finalidade, leitores e retenção. Separe ficha de saúde, documentos, religião quando necessária e autorizada, dados de retirada e registros pedagógicos. Registre acesso sensível e exportação sem copiar seu conteúdo para logs. Preveja correção, revogação e descarte. Dados de teste são fictícios. |
| Tempo e repetição | Guarde vigência, autor, horário, versão e escopo. Use o vínculo **na data do encontro** para compor histórico e o vínculo **atual** para conceder acesso. Trate toques repetidos, reenvios de fila e operações concorrentes no banco: nenhum deles cria segunda ciência, vaga, presença ou entrega. |
| Identidade e interface | Uma interface em português para celular, Android e computador. Use os arquivos de marca autorizados, com cores acompanhadas de rótulos; não redesenhe a marca. Teste navegação por teclado, foco, leitor de tela, texto ampliado, erros por campo e conexão fraca. Referência de acessibilidade: WCAG 2.2 AA. |

**Cenário de teste compartilhado.** Crie duas regionais fictícias, dois núcleos em uma delas, turmas com o mesmo nome em regionais diferentes, dois irmãos em turmas distintas, uma professora que também é mãe em outro núcleo e um substituto com data de término. Use essas mesmas pessoas para demonstrar permissão, negação e revogação. Nada disso representa pessoas das igrejas.

## Arquitetura e preparação do repositório

- **Aplicação:** monólito modular com Laravel 13, PHP 8.4, Blade e Livewire. Navegador e Android usam as mesmas operações. Concentre regras de negócio em ações de domínio; componha a interface com essas ações. Policies e consultas limitadas por escopo devem concordar.
- **Persistência:** PostgreSQL com chaves de regional e de núcleo onde aplicáveis, índices por escopo/data e restrições contra combinações incompatíveis. Registros globais têm dono explícito. Use transações para disputa de vaga, entrega, reserva e conciliação. Rode testes de concorrência em PostgreSQL.
- **Mensagens:** Redis para filas e cache. Na publicação, persista aviso, versão, destinatários e tarefa de envio na mesma transação (outbox). No envio, revalide o vínculo. Retentativas usam chave única; falhas ficam visíveis para a operação. O sininho continua disponível sem push.
- **Arquivos:** armazenamento privado compatível com S3. A aplicação autoriza a rota de leitura considerando estado e audiência; o mesmo vale para capa, miniatura, download e segmentos de vídeo. Um link assinado com validade restante não é suficiente para conteúdo que exige retirada imediata.
- **Domínios sugeridos:** Organização e acesso; Pessoas e matrículas; Comunicação; Agenda e inscrições; Frequência; Pedagogia; Mídia; Formação; Patrimônio; Pedidos; Relatórios; Tesouraria. Abra cada módulo quando sua fase começar.
- **Operação:** separe desenvolvimento, testes, homologação e produção. Use segredos fora do código, filas com monitoramento, cópias protegidas e liberação de funções por regional. Não mantenha mídia privada, exportações, chaves ou fichas dentro da pasta do projeto sincronizada pelo OneDrive.

**Inicialização na F1.** A raiz já contém documentação. Se não houver aplicação, use a subpasta `sistema/` para o Laravel e mantenha estes arquivos na raiz. Se já existir uma aplicação na retomada, use a existente e registre o caminho. Não reinstale por cima dela. Na raiz, após conferir PHP, Composer, Node e acesso a um PostgreSQL de desenvolvimento:

```powershell
# Somente no primeiro bootstrap, se sistema/ ainda não existir.
composer create-project laravel/laravel sistema "^13.0"
Set-Location -LiteralPath sistema
composer check-platform-reqs
```

Configure `.env` local, `.env.example` sem segredos, PostgreSQL de teste separado, Redis e armazenamento privado de desenvolvimento. Instale a versão de Livewire compatível com o projeto se não estiver presente. Use o gerenciador do arquivo de lock: com `package-lock.json`, rode `npm ci`; numa instalação nova sem lock, `npm install` e versione o lock resultante. Documente como iniciar servidor, frontend, worker e agendador locais. Não use um banco compartilhado para executar a suíte.

**Verificação a partir de `sistema/`.** Valide a conexão ao ambiente correto antes de migrar. Em desenvolvimento e testes isolados:

```powershell
php artisan migrate --env=testing
php artisan test
php vendor/bin/pint --test
npm run build
```

Durante uma entrega, rode primeiro os testes afetados. Antes de liberar a fase, execute a suíte pertinente, a compilação da interface e os ensaios manuais descritos nela. Se um pré-requisito não estiver instalado ou um ensaio não tiver sido feito, registre a limitação e o procedimento que falta executar.

<a id="f0"></a>

## F0 — Governança, regras e protótipo

**Entrada:** plano e backlog. **Resultado:** decisões prontas para validação, três jornadas demonstráveis com dados fictícios e responsáveis definidos antes do uso real. Ainda não é preciso cadastrar uma família nem instalar a aplicação.

| ID | Depende de | O que produzir e como verificar |
|---|---|---|
| T01 | — | Prepare a matriz papel × ação × escopo, nomeação e substituição. Mostre à direção um caso permitido, um negado e o caso de uma professora que também é mãe. Proposta preparada em [docs/governanca/matriz-de-acesso.md](docs/governanca/matriz-de-acesso.md); aguarda validação institucional. |
| T02 | T01 | Modele edição do currículo, faixas, data de corte, entrada e remanejamento. A apresentação e o caderno divergem nas idades: documente alternativas, peça a escolha aplicável ao piloto e mantenha vigência. Defina os marcos de revisão de faltas. Proposta em [regras pedagógicas](docs/governanca/regras-pedagogicas.md); faixas da apresentação escolhidas pelo usuário para o piloto, com demais itens de D02/D05 pendentes. |
| T03 | T01 | Prepare dicionário de campos, fluxo de guarda/retirada, política de acesso e retenção, termos por finalidade e canal. A família pode recusar imagem sem perder a matrícula. Identifique quem valida cada decisão. Propostas em [dados e privacidade](docs/governanca/dados-e-privacidade.md) e [responsabilidade e autorizações](docs/governanca/responsabilidade-e-autorizacoes.md), com referências em [fontes e decisões](docs/governanca/fontes-e-decisoes.md); aguardam validação. |
| T04 | T01, T03 | Prototipe três jornadas em celular e computador: responsável confirma aviso, professor fecha chamada e secretaria entrega apostila. Valide marca, legibilidade, acessibilidade e entendimento com os perfis correspondentes. Prévia interativa em `/jornadas`, com [roteiro e evidências](docs/jornadas/ROTEIRO-DE-AVALIACAO.md); avaliação com participantes e aceite institucional pendentes. |
| T05 | T01 | Proponha uma regional, dois núcleos e pelo menos duas turmas para o piloto. Identifique custodiante do domínio/Android, suporte assistido, ambientes, custo de armazenamento e rotina de recuperação. Nomes de titulares e datas exigem confirmação real. |

**Primeiro artefato de execução:** ao executar a F0, crie `docs/governanca/matriz-de-acesso.md` com as colunas papel, ação, escopo, condição, dado visível e exemplo negado. Em seguida, documente campos/termos e configurações do piloto. Registre o resultado de D01–D12 na seção de decisões; reúna dúvidas por responsável.

**Saída:** direção e secretaria validam matriz, regras e campos; os participantes percorrem as três jornadas. Enquanto aguardam decisões, ficam disponíveis para revisão os protótipos e exemplos fictícios. Não registrar aceite por ter produzido documentos.

<a id="f1"></a>

## F1 — Fundação, acesso e cadastros

**Entrada:** regras da F0 aplicáveis ao fluxo. Inicialize a aplicação conforme a seção de arquitetura. Construa primeiro o modelo organizacional, depois a autorização, depois as telas de cadastro.

| ID | Depende de | O que implementar e como verificar |
|---|---|---|
| T06 | T01, T02 | Crie regionais, núcleos, níveis, edições, módulos, ofertas e turmas. Garanta integridade do escopo no banco. Duas turmas homônimas de regionais diferentes não se confundem na busca ou no seletor. |
| T07 | T06 | Implemente atribuições com escopo/vigência, troca de contexto, Policies e filtros de consulta. Professor não acessa turma vizinha por busca, endereço direto ou ação Livewire; como familiar, vê somente o dependente. |
| T08 | T03, T07 | Crie convite de uso único com expiração, verificação de contato, recuperação e MFA para privilégios. Código do núcleo identifica destino; conta pendente acompanha somente o pedido. Uma tela pública não deve revelar cadastro existente. |
| T09 | T03, T08 | Secretaria verifica representação e conflito de guarda. Separe poderes de acompanhar, confirmar, inscrever, autorizar imagem e retirar. Um contato de emergência ou autorizado a buscar não recebe os demais poderes. |
| T10 | T02, T09 | Separe solicitação familiar de aprovação da matrícula por turma/módulo. Inscreva em espera se o módulo piloto já começou; transferência encerra vínculo anterior e mantém o diário histórico. |
| T11 | T03, T09 | Crie perfis distintos: aluno com nascimento/idade calculada, endereço necessário e um ou mais responsáveis com contatos; colaborador com função, turmas e lotação. Suporte dois responsáveis sem exigir segundo inexistente. Restrição alimentar, saúde, religião e dossiê civil têm acesso por finalidade; não aparecem na chamada. |
| T12 | T07, T08 | Convide colaboradores por nome, atribua papel e início/fim e permita substituição temporária. Verifique que expiração ou remoção encerra o acesso mesmo com a sessão aberta. |
| T13 | T05, T07 | Registre ator, escopo, ação, data e resultado de operações sensíveis. Faça exercício de cópia e restauração em homologação, com dados fictícios. Compare contagens, teste revogações e registre os tempos observados. |
| T14 | T07, T13 | Crie acervo privado e rota de acesso por recurso, com validação de upload e registro de autoria. Um ex-professor não consegue reutilizar o link, nem abrir o arquivo a partir de outra conta. |

**Saída:** no cenário compartilhado, exercite listar, abrir e alterar como professor, responsável e secretaria de outra unidade. Verifique a matrícula sem conta infantil e a proteção das fichas sensíveis. Só habilite um fluxo com dados reais quando a governança e a proteção correspondentes estiverem validadas.

<a id="f2"></a>

## F2 — Comunicação e rotina das igrejas

**Entrada:** F1 validada. Comece pelo aviso que precisa de ciência. Reutilize sua evidência para decisões de reunião; inscrição, autorização de saída e presença continuam sendo registros independentes.

| ID | Depende de | O que implementar e como verificar |
|---|---|---|
| T15 | T07, T10 | Crie mural, sininho, prévia de público e avisos informativos, importantes e críticos. Uma pessoa alcançada pela turma e pela regional recebe uma entrega. Guarde audiência e versão na publicação. |
| T16 | T09, T15 | Separe envio, abertura e botão “Estou ciente”. Registre autor, versão, filho ou obrigação representada e data. Duplo toque não duplica; revisão material reabre ciência. Mostre lembretes e pendentes por escopo; retorno por telefone tem registro próprio. |
| T17 | T12, T15 | Implemente tópico da turma, resposta da família à equipe, moderação e denúncia. Não exponha telefones nem respostas privadas a outras famílias. Sem mensagens privadas entre adulto e menor. |
| T18 | T06, T12 | Publique agenda global, regional, do núcleo e da turma com um único evento de origem. Diferencie obrigatório/facultativo, público e fuso local. Um núcleo pode complementar orientação local; não altera o evento superior. Preveja aprovação pastoral quando configurada. |
| T19 | T15, T18 | Modele série, ocorrência e exceção; ofereça alteração de uma data, desta em diante ou de toda a série. Cancelar uma terça preserva as demais, fecha inscrições daquela data, impede a chamada e notifica os afetados. Em F5 a mesma operação também libera a reserva. |
| T20 | T09, T18 | Formulário básico reaproveita cadastro, vincula delegado e dependente, respeita abertura, encerramento, lotação e espera. Adolescente sem delegação válida não se inscreve. Duas solicitações concorrentes para a última vaga geram um confirmado e um em espera. |
| T21 | T10, T19 | Gere lista por ocorrência e turma a partir dos matriculados vigentes naquela data. Estados: não lançado, presente, falta e falta justificada. Não feche lista incompleta; correção exige autor/motivo. Conte uma presença por pessoa/ocorrência mesmo em evento com várias turmas. |
| T22 | T02, T21 | No modelo do Vale do Aço, falta sem justificativa abre tarefa privada de contato; ao atingir **3 faltas**, avisa a coordenação; **mais de 3**, na 6ª e na última lição, indica revisão para intensivão. Use D05 para as faltas que contam. Culto facultativo, cancelamento ou lançamento pendente não viram falta de aula. |
| T23 | T16, T18 | Para reuniões e eventos, use convocados/inscritos elegíveis e registre a presença da equipe. Na ata, versione decisões e atribua ciência por assunto. Um presente pode continuar com assunto pendente; um ausente pode confirmar sua leitura depois. |
| T24 | T07, T09 | Catálogo por unidade, variante/tamanho, quantidade, beneficiário e local de retirada. Implemente solicitação, confirmação, separação, disponibilidade para retirada, entrega parcial/total e cancelamento. Registre recebedor e atendente; a baixa de uma unidade repetida não entrega a segunda. Sem cobrança ou receita. |
| T25 | T14, T15 | Publique orientações e PDFs autorizados por grupo, incluindo anexos de avisos. Buscas, resultados e downloads seguem a mesma permissão. No piloto, não disponibilize galerias com imagens identificáveis de menores. |
| T26 | T16, T18, T21, T24 | Crie “Minha família” com seletor de filho, próximo evento e pendências, e “Meu trabalho” com tarefas do cargo. Teste o professor que abre sua chamada e a secretaria que encontra o pedido a entregar. Perfis pessoais têm tela própria. |

**Saída:** simule duas terças e cancele apenas a primeira. Um responsável por irmãos vê a quem o aviso se refere; clicar duas vezes produz uma ciência por obrigação; a segunda terça continua disponível. Feche a chamada de um encontro realizado, revise os limiares de faltas e entregue parcialmente um pedido sem efeito financeiro.

<a id="f3"></a>

## F3 — Android e piloto assistido

**Entrada:** jornadas da F2 funcionando no navegador. Distribua a mesma interface como PWA e em pacote Android de teste via Trusted Web Activity (TWA).

| ID | Depende de | O que implementar e como verificar |
|---|---|---|
| T27 | T04, T05, T26 | Configure domínio HTTPS, manifesto, ícones, tela sem conexão e pacote assinado. Valide a associação domínio/pacote/certificado com Digital Asset Links. Em dois aparelhos do piloto, teste instalação, login, voltar, link interno, anexo e saída. Mantenha acesso pelo navegador. |
| T28 | T15, T27 | Implemente push opcional com texto neutro e detalhe atrás de login. Cache offline só de arquivos públicos da interface. Teste aviso com push recusado, envio interrompido, retorno do app e troca de conta: nenhuma ficha privada reaparece para a conta seguinte. |
| T29 | T10, T11, T13 | Crie importação por lote com prévia, validação, deduplicação assistida, conferência por turma e reversão rastreável. Ensaiar com dados fictícios. Na carga mínima aprovada, não importar conversas, fotos antigas ou consentimentos presumidos. |
| T30 | T20, T22, T23, T24, T28, T29 | Entregue roteiro de treinamento a um multiplicador por núcleo, canal de ajuda e alternativa presencial. Acompanhe dois ciclos reais com as igrejas; registre dificuldades, adesão e decisão de continuidade. |

**Saída:** registrar D13 e o resultado observado. Metas propostas para pactuação: nenhum vazamento ou acesso indevido nos ensaios; todos os fluxos críticos aprovados; pelo menos 90% das chamadas fechadas em 24 h e 80% das famílias convidadas capazes de abrir e confirmar um aviso sem auxílio. Registre quem precisou de apoio e a base de cada percentual. Se a loja estiver pendente, o piloto web pode prosseguir; não declare homologado o pacote ainda não testado.

<a id="f4"></a>

## F4 — IBUC em Casa, planejamento e mídias

**Entrada:** piloto validado. Ative galerias por regional somente depois de testar consentimento, visibilidade e retirada. Uma atividade entregue ao professor permanece privada mesmo quando contém vídeo.

| ID | Depende de | O que implementar e como verificar |
|---|---|---|
| T31 | T02, T14, T30 | Programe atividades por nível, turma, módulo, lição e data: leitura ou vídeo, versículo, louvor, apostila, devocional e desafio. Família acompanha individualmente os filhos; marcar um não altera o outro. Envio de vídeo é opcional. |
| T32 | T02, T31 | Implemente plano revisável com os seis momentos: acolhida, memorização do versículo, Bíblia em foco, praticando a Bíblia, hora da novidade e hora da bênção. Separe previsto de executado; registre as avaliações na sexta e última lição segundo a configuração. |
| T33 | T20, T30 | Evolua a inscrição básica para formulário versionado com tipos, campos necessários, público e termos. Ao editar para novos inscritos, os dados e termos aceitos pelos anteriores não mudam. |
| T34 | T03, T09, T14, T30 | Guarde as permissões de captação, galeria interna e canal externo em separado. Para cada versão da peça, registre cada pessoa identificável e a decisão de seu representante habilitado ou do próprio adulto. Ausência, recusa, conflito de guarda ou pessoa não identificada bloqueiam publicação. |
| T35 | T34 | Receba em área privada, valide arquivo, remova geolocalização e use identificação manual. A prévia aos responsáveis não deve expor terceiros indevidamente. Exija revisão editorial. Revogar retira original, capa, miniatura e vídeo servido; registre retirada dos canais externos administrados e acompanhe o que depender de terceiros. |
| T36 | T19, T35 | Vincule coleções a turma, núcleo e evento. A central permite busca por grupo, data, tipo e título apenas no acervo autorizado. Mudar versão, finalidade ou audiência exige nova avaliação. Aprovação interna não libera rede social. |

**Saída:** para uma foto com três crianças identificáveis, registre duas aprovações e deixe a terceira pendente: nem a capa aparece. Autorize as três para a turma, publique e revogue uma autorização: o endereço anterior deixa de servir a imagem e os derivados. Valide também duas atividades diferentes para irmãos de níveis distintos.

<a id="f5"></a>

## F5 — Formação da equipe e reservas de espaços

**Entrada:** acervo privado e agenda estáveis; termos de uso/licença dos materiais definidos. A inscrição em um curso de colaboradores não permite consultar dados de alunos do IBUC.

| ID | Depende de | O que implementar e como verificar |
|---|---|---|
| T37 | T14, T30 | Crie catálogo de cursos e palestras por público, edições, módulos, aulas, matrículas, PDFs, vídeos legendados e janela de liberação. Use “TDAH na Igreja” como curso de demonstração, com conteúdo fictício ou autorizado. Não inscrito não abre material nem por link direto. |
| T38 | T37 | Salve progresso, tentativas e resultados por edição da aula/questionário. Abra área de dúvidas moderada com resposta ao inscrito. Critérios de conclusão são definidos antes da oferta; abrir um vídeo não conclui um curso obrigatório. |
| T39 | T12, T32, T38 | Aplique D14: curso exigido ou estudo presencial equivalente validado habilita para a escala do módulo. Sem comprovação, impedir a designação e indicar substituto elegível. O curso de demonstração não é requisito universal. |
| T40 | T06, T18, T30 | Cadastre sala de vídeo, biblioteca, área aberta e demais ambientes por núcleo: dimensão, lotação, acessibilidade, recursos, horário e observações. Ofereça mapa semanal no computador e lista de horários no celular sem expor nomes de crianças. |
| T41 | T19, T40 | Implemente solicitação, aprovação, recusa, cancelamento, bloqueios de manutenção e intervalos de montagem. Só a aprovação ocupa o espaço. Garanta no PostgreSQL que aprovações simultâneas não sobreponham intervalos na mesma sala; cancelar a ocorrência libera e notifica. |

**Saída:** um colaborador conclui a preparação aprovada e se torna escalável; outro continua impedido. Para uma sessão de filme com 20 pessoas em 10/12 e necessidade de projetor, o mapa oferece apenas locais aptos. Em disputa pelo mesmo horário, uma única aprovação prevalece.

<a id="f6"></a>

## F6 — Indicadores e expansão regional

**Entrada:** diários, obrigações de ciência, estoque/entregas e execução pedagógica têm histórico. Liste pendências operacionais desde o piloto; use esta fase para histórico, consolidação e exportação.

| ID | Depende de | O que implementar e como verificar |
|---|---|---|
| T42 | T16, T21, T23, T30 | Separe cobertura da chamada, frequência de aula, comparecimento a evento/reunião e ciência por versão. Exiba período, grupo, denominador e faltas justificadas. Sem base elegível, mostre “sem base”; chamada aberta não entra como resultado final. |
| T43 | T24, T32, T38 | Conte lições executadas sobre planejadas elegíveis, tratando reposição como a mesma lição. Apure conclusão por edição/coorte e pedidos por quantidades solicitadas, entregues e pendentes. Confira amostras nos registros de origem. |
| T44 | T35, T41, T42, T43 | Consolide por regional com mínimo de grupo aprovado para evitar identificação. Global vê agregados; exportação nominal depende de finalidade, autorização e validade. Habilite nova regional em ondas após repetir ensaios de escopo, revogação e restauração. |

**Saída:** em um encontro com 20 esperados, 15 presentes, 3 faltas, 1 justificada e 1 pendente, mostre cobertura 19/20 e impeça fechamento. Se o último registro for falta, frequência final = 15/20. Para ciência, divida obrigações confirmadas pelas elegíveis da mesma versão. Não deduza ciência a partir da presença.

<a id="f7"></a>

## F7 — Tesouraria e pagamentos

**Entrada:** operação estabilizada, escopo financeiro pactuado pela tesouraria e provedor escolhido antes de integrar. Até lá, F2 continua atendendo solicitações e entregas sem receber dinheiro.

| ID | Depende de | O que implementar e como verificar |
|---|---|---|
| T45 | T24, T44 | Modele contas e lançamentos por regional, núcleo e módulo; diferencie contribuições, ofertas, patrocínios e despesas, competência e caixa. Pedido entregue, valor de referência e moeda pedagógica não geram receita automaticamente. |
| T46 | T45 | Em ambiente de teste do provedor, separe cobrança, retorno verificado, recebimento, estorno e conciliação. Um retorno repetido não duplica lançamento. Tesouraria compara um ciclo completo com extrato e aprova prestação de contas antes de habilitar cobranças reais. |

**Saída:** demonstração conciliada com recebimento, repetição de retorno, estorno e saldo por núcleo. Divergências ficam explícitas para a tesouraria; não ajuste um lançamento silenciosamente para fechar o total.

## Decisões que precisam de uma pessoa responsável

As 14 decisões estavam **em aberto** na criação deste roteiro. O Codex prepara alternativas e exemplos; a área indicada define a regra. Atualize a última coluna apenas com decisão recebida e referência de data/ata, sem nomes de menores, documentos pessoais ou credenciais. **Até a fase** indica em qual fase fechar a decisão.

| ID | Até a fase | Quem decide | Pergunta a resolver | Situação / referência |
|---|---|---|---|---|
| D01 | F0 | Direção geral e regional | Quais poderes são nacionais, quais são delegáveis e quem nomeia os administradores? | Em aberto — proposta de 14/11/2026 e alternativas na [matriz T01](docs/governanca/matriz-de-acesso.md); sem decisão institucional registrada. |
| D02 | F0 | Direção pedagógica | Qual edição define as idades, o corte e o remanejamento do piloto? | Parcialmente definida — usuário escolheu faixas 2–5, 6–8, 9–11 e 12–17 no chat de 14/11/2026, para o piloto. Corte, transição, vigência e edição completa pendentes; [proposta T02](docs/governanca/regras-pedagogicas.md). Sem aprovação nacional presumida. |
| D03 | F0 | Secretaria e privacidade | Como verificar representação, guarda, retirada e eventuais contestações? | Em aberto — procedimento e minutas preparados na [T03](docs/governanca/responsabilidade-e-autorizacoes.md), sem aceite institucional. |
| D04 | F0 | Direção local e famílias | Quem confirma cada aviso: indivíduo, responsável designado ou um responsável por filho? Como registrar o atendimento assistido? | Em aberto — proposta por filho/versão e origem assistida separada na [T03](docs/governanca/responsabilidade-e-autorizacoes.md). |
| D05 | F0 | Coordenação e direção | Quais faltas contam para cada alerta e para a revisão na 6ª e na última lição? | Em aberto — alternativas para justificadas, janela e reposição na [T02](docs/governanca/regras-pedagogicas.md). |
| D06 | F0 | Privacidade e direção | Quais dados de saúde, religião, documentos e certidões são necessários, por quanto tempo e para quais leitores? | Em aberto — [dicionário T03](docs/governanca/dados-e-privacidade.md) com hipóteses e prazos propostos; necessidade, enquadramento e aprovação por finalidade ainda pendentes. |
| D07 | F0 | Direção e tecnologia | Quem licencia a marca e responde pelo domínio, pela conta Android e pela guarda das chaves? | Em aberto |
| D08 | F0 | Direção regional | Quais núcleos, turmas, multiplicadores, canais assistidos e datas participam do piloto? | Em aberto |
| D09 | F0 | Privacidade e mídia | Quais procedimentos valem para captação, prévia protegida, autorização por canal, recusa e retirada? | Em aberto — fluxo e minutas por peça/finalidade/canal na [T03](docs/governanca/responsabilidade-e-autorizacoes.md). |
| D10 | F0 | Secretaria e tesouraria | Quais produtos/variantes terão saldo confiável, quem entrega e como tratar entrega parcial ou devolução? | Em aberto |
| D11 | F0 | Tecnologia e direção | Quais metas de recuperação, retenção, capacidade e custo são aceitáveis? O plano propõe no piloto até 24 h de perda e até 8 h de recuperação. | Em aberto — propostas de retenção/descarte e rotação de cópias na [T03](docs/governanca/dados-e-privacidade.md); demais escolhas operacionais seguem para T05. |
| D12 | F0 | Direção e privacidade | O que pode ser delegado ao adolescente, por quem e quando expira ou é revogado? | Em aberto — proposta delimitada na [T03](docs/governanca/responsabilidade-e-autorizacoes.md), incluindo avaliação do ECA Digital antes de habilitação. |
| D13 | F3 | Direção e multiplicadores | Que amostra, metas de adesão e evidências liberam nova onda? | Em aberto |
| D14 | F5 | Coordenação de formação | Qual preparatório habilita para cada módulo e quem valida equivalência presencial? | Em aberto |

## Quando uma entrega está pronta para validação

- A mudança realiza a jornada pedida com dados fictícios e respeita suas dependências. Há caso normal e caso de limite pertinente: outro grupo, vínculo encerrado, falta de vaga, repetição, alteração de versão ou revogação.
- Se houver banco, as migrações foram exercitadas em base isolada e as restrições preservam os vínculos. Se houver fila, arquivo, cache ou relatório, o escopo também foi verificado nesses caminhos.
- Se houver tela, a ação pode ser concluída no celular e no computador, incluindo mensagens de erro, vazio e falta de conexão pertinentes. Se exigir Android ou usuário real, o ensaio correspondente tem evidência própria.
- O registro informa o que mudou, quais verificações passaram, o que não pôde ser verificado e quem valida. Só então mova para **Em validação**; use **Aceita** quando houver o aceite aplicável. Teste não executado, decisão pendente e entrega aceita são estados diferentes.

<a id="retomada"></a>

## Registro de execução

**Estado atual:** F0 em preparação. As propostas [T01](docs/governanca/matriz-de-acesso.md), [T02](docs/governanca/regras-pedagogicas.md) e [T03 — dados](docs/governanca/dados-e-privacidade.md) e [autorizações](docs/governanca/responsabilidade-e-autorizacoes.md) estão em validação. D02 está parcialmente definida pela escolha do usuário das faixas 2–5, 6–8, 9–11 e 12–17 para o piloto; os demais itens/decisões continuam identificados em [fontes e decisões](docs/governanca/fontes-e-decisoes.md). A T04 tem base visual e três jornadas interativas em `sistema/`, com [roteiro de avaliação e evidências](docs/jornadas/ROTEIRO-DE-AVALIACAO.md); não há persistência ou autorização real implementada. O aceite com participantes continua pendente. O próximo trabalho elegível é preparar T05 com dados fictícios, sem habilitar acesso real antes das decisões aplicáveis. A planilha de referência mantém o estado original; use este registro para o andamento e não presuma aceite pela existência dos artefatos.

Acrescente uma linha ao concluir trabalho relevante. Uma nova entrada preserva a anterior quando houver mudança de regra, correção ou reabertura. A data do arquivo não comprova aceite.

| Data | Fase / ID | Estado | Artefato ou mudança | Verificação e resultado | Pendência ou próxima ação |
|---|---|---|---|---|---|
| — | F0 / T01 | A fazer | — | — | Preparar a matriz e os exemplos; encaminhar D01 à direção pelo canal definido pela equipe. |
| 14/11/2026 | F0 / T04 | Em validação | Prévia de identidade em `sistema/`: tokens, componentes Blade, fontes locais, logotipo original, formulários, avisos e exemplos responsivos. Guia e instruções adicionados ao AGENTS.md. | Build e compilação de views concluídos; teste HTTP e formatação PHP passaram. No navegador: formulário vazio e preenchido, erro de e-mail, ciência única, busca, diálogo e menu móvel conferidos com dados fictícios. | Avaliar visual com o usuário. A entrega é a parcela visual da T04; não equivale a aceite da marca ou das três jornadas reais. |
| 14/11/2026 | F0 / T04 | Em validação | Ícones passam a ser padrão nos campos, conforme orientação do usuário. Resolução por tipo/nome compartilhada entre input, select e textarea; escolha explícita e exceção disponíveis. Guia e AGENTS.md atualizados. | Build, compilação Blade e teste HTTP concluídos. No navegador, os cinco campos da prévia exibem ícones com espaçamento reservado e rótulos preservados. | Manter esse padrão nas próximas telas e avaliar a proposta visual. |
| 14/11/2026 | Projeto / compartilhamento | Publicado | Primeira versão pública no GitHub a pedido do usuário: documentação de instalação, capturas, código, plano e backlog. Versionamento na branch main. | Repositório público, branch e commit remoto conferidos; os 88 arquivos publicados não incluem ambiente local, credenciais, dependências, caches ou banco. | Repositório: https://github.com/matheuswilliam95/ibuc-sistema. A publicação do código não equivale à conclusão das fases nem à hospedagem PHP. |
| 14/11/2026 | F0 / T04 | Em validação | Revisão solicitada de tamanhos: texto e campos em 16 px, rótulos e botões em 14 px, apoio mínimo de 12 px. Altura dos controles ampliada, grades reorganizadas e capturas atualizadas. | Build concluído; conferência visual em computador e celular, com medidas em 320, 390 e 768 px. Nenhum texto visível abaixo de 12 px na inspeção; botão compacto com 44 px, sem reduzir a fonte. | Avaliar a leitura com o usuário e manter a escala nas próximas telas. |
| 14/11/2026 | F0 / T04 | Em validação | Teste solicitado de gradiente suave no cabeçalho dos formulários. Componente compartilhado x-ui.form-header aplicado ao formulário e às preferências, com verde muito claro, divisória e cantos arredondados. | Build e testes HTTP passaram. Em 320, 390, 768 e 1280 px, o cabeçalho acompanha as bordas do painel e não provoca rolagem horizontal. Capturas atualizadas. | Avaliar com o usuário a intensidade do gradiente. |
| 14/11/2026 | F0 / T01 | Em validação | Matriz de acesso preparada com RBAC por escopo/vínculo, dados visíveis, nomeação, substituição e revogação. Inclui 24 casos fictícios, divisão proposta entre parâmetros nacionais e autonomia regional e proposta para D01, com dependências D03/D04/D06/D09/D11/D12 explícitas. README e roteiro apontam para o artefato. | Revisão documental das regras e dos casos e conferência de T01/D01 no backlog; estrutura das tabelas e links locais conferida. PDF e backlog preservados. Não houve alteração nem teste de autorização no Laravel: implementação prevista na T07. | Direção e secretaria avaliam matriz e D01; preparar T02/T03 com decisões em aberto identificadas. |
| 14/11/2026 | F0 / T02 | Em validação | Regras pedagógicas documentadas: edição/oferta, matrícula com vigência, remanejamento, alternativas de corte, faltas/justificadas, alerta de 3 e revisão com mais de 3 nos marcos. D02 parcialmente definida: usuário escolheu N1 2–5, N2 6–8, N3 9–11 e N4 12–17 no chat; demais itens pendentes. | Referências A1/A5/A6/A7 e backlog T02/D02/D05 conferidos; páginas de idade sem texto pesquisável inspecionadas visualmente. 16 casos de limite preparados e documentação conferida; sem teste funcional no Laravel. | Direção define restante de D02 e D05; usar a proposta nas jornadas fictícias da T04. |
| 14/11/2026 | F0 / T03 | Em validação | Dicionário com 58 campos, 8 finalidades/bases em análise e 10 categorias de retenção; procedimentos de representação, retirada, ciência e mídia; minutas separadas e registro de fontes/decisões. Não há consentimento presumido ou coleta real habilitada. | Fichas e termo originais conferidos, incluindo leitura visual do termo sem texto. Referências oficiais LGPD/ANPD/ECA/ECA Digital consultadas. Tabelas, links e códigos do dicionário conferidos; plano/backlog preservados. 28 casos documentais preparados, sem execução de servidor ou validação jurídica/institucional. | Secretaria, privacidade e direção validam D03/D04/D06/D09/D11/D12; completar T04 e preparar T05 com dados fictícios. |
| 14/11/2026 | F0 / T04 | Em validação | Três jornadas interativas em `/jornadas`: ciência por filho/versão, chamada com pendências/cancelamento/fechamento e entrega parcial/total. Layout e controles compartilhados, estado só em memória, seis capturas e roteiro para os avaliadores. | 4 testes PHP/14 asserções e 10 testes Node passaram; Vite, Blade e Pint concluídos. Rota ausente em produção. Fluxos e teclado conferidos no navegador, com medidas de 320/390/768/1440 px sem rolagem horizontal e texto mínimo de 12 px nas telas menores. Protótipos não equivalem a Policies/transações no servidor; ensaios com participantes, leitor de tela, texto ampliado, conexão limitada e Android físico pendentes. | Avaliar T04 com família, professor e secretaria; preparar T05 e registrar decisões institucionais pendentes. |

**Para iniciar uma sessão:**

> Leia AGENTS.md e IMPLEMENTACAO.md. Confira as propostas T01–T03, a escolha parcial de D02 e as jornadas da T04. Prepare T05 com configurações fictícias, alternativas e responsáveis ainda a confirmar. Preserve as decisões pendentes e não inicie coleta real por haver protótipos. Se houver avaliação da direção/equipe, registre a versão e os itens validados.

**Para retomar:**

> Leia o registro de execução de IMPLEMENTACAO.md, confira o estado do projeto e continue pela primeira entrega elegível da fase atual. Execute as verificações cabíveis, registre evidências e deixe indicado o próximo ID.

**Referências técnicas:** [instalação do Laravel 13](https://laravel.com/docs/13.x/installation), [autorização](https://laravel.com/docs/13.x/authorization), [testes](https://laravel.com/docs/13.x/testing), [criação de projeto com Composer](https://getcomposer.org/doc/03-cli.md#create-project), [Trusted Web Activity no Android](https://developer.chrome.com/docs/android/trusted-web-activity) e [instruções de projeto do Codex — OpenAI Docs](https://developers.openai.com/codex/guides/agents-md/). As fontes institucionais e os materiais pedagógicos estão identificados por página no plano de produto.
