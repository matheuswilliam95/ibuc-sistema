# T01 — Matriz de acesso do IBUC

**Estado:** Em validação. **Versão:** proposta 1, de 14/11/2026. **Responsáveis pela validação:** direção geral e regional, com secretaria e responsáveis por privacidade.

Esta entrega define papéis, ações, alcance e regras de nomeação para orientar a implementação. É uma proposta revisável, não uma autorização institucional já concedida. A decisão **D01 permanece em aberto**. O [roteiro](../../IMPLEMENTACAO.md) e seu registro de execução acompanham a entrega. Todos os exemplos abaixo são fictícios.

A matriz não habilita permissões na aplicação. O banco organizacional será construído na T06, as verificações de acesso na T07 e os convites na T08. Saúde, guarda, imagem e delegação a adolescente dependem também das decisões indicadas ao final.

## 1. Como uma permissão será avaliada

Usar **RBAC com escopo, vínculos e condições**. O papel reúne ações, mas não concede acesso a todos os recursos do sistema. Para autorizar uma operação, o servidor precisa verificar, em conjunto:

1. Conta habilitada e contexto ativo pertencente ao usuário autenticado.
2. Ação prevista em uma atribuição vigente nesse contexto.
3. Recurso pertencente ao escopo autorizado, com seus vínculos organizacionais conferidos no servidor.
4. Vínculo exigido para a operação: turma atribuída, matrícula, responsabilidade pelo filho, inscrição em curso ou designação específica.
5. Condições da operação: estado do registro, finalidade, consentimento aplicável e poderes de representação.
6. Campos autorizados para aquela finalidade. Consultar a ficha básica não libera os blocos restritos nem sua exportação.

Se qualquer verificação falhar, negar. Uma permissão não listada também é negada. O nome do papel, um identificador na URL, o contexto enviado pelo navegador e o fato de receber um aviso não comprovam autorização.

As chaves de ação nas tabelas são propostas para o catálogo da T07. `view` autoriza somente os campos indicados; `manage` não inclui delegar acesso, exportar, apagar histórico nem consultar dados restritos. Essas operações precisam de ação própria. Papéis institucionais podem ter nomes locais diferentes, preservando as ações e os limites aprovados.

## 2. Escopos e contextos

| Escopo | Recursos alcançados | Limite |
|---|---|---|
| Global | Estrutura das regionais, padrões compartilhados, publicações globais e agregados autorizados | Não concede leitura nominal de alunos, famílias ou colaboradores das regionais. |
| Regional | Recursos da regional e de seus núcleos e turmas, quando a ação permite | Não alcança outra regional nem libera blocos restritos por herança. |
| Núcleo/igreja | Recursos do núcleo e de suas turmas, quando a ação permite | Não alcança o núcleo vizinho. |
| Turma | Recursos das turmas explicitamente atribuídas | Não alcança outra turma do mesmo nível, igreja ou regional. |
| Família | Cada dependente com vínculo e poderes vigentes | Irmãos podem ter poderes e pendências diferentes; não abre a lista da turma. |
| Curso, comissão ou evento | Materiais, tarefas e participantes necessários à função naquele recurso | Não abre a turma, o núcleo ou a ficha completa dos participantes. |
| Próprio usuário | Conta, preferências e conteúdo pessoal autorizado | Editar o perfil não altera papel, matrícula, guarda ou poderes sobre filhos. |

O alcance descendente depende da ação: a direção regional pode administrar turmas da regional sem receber o conteúdo de saúde delas. Nível e módulo são referências pedagógicas, não fronteiras de autorização.

Uma atribuição combina pessoa, papel, escopo, início, fim opcional, estado, autor e aprovação. Uma mesma pessoa pode ter várias atribuições. O contexto profissional seleciona uma atribuição; o familiar exige o vínculo com o dependente. A troca de contexto não soma privilégios de atribuições diferentes. Novas funções serão compostas de ações explícitas, sem copiar um papel administrativo inteiro.

## 3. Dados visíveis por finalidade

| Conjunto | Conteúdo proposto | Restrição |
|---|---|---|
| Básico operacional | Nome, identificação da matrícula, turma e situação necessárias à tarefa | Não inclui endereço, contatos, saúde, religião ou documentos. Nascimento/idade só no fluxo que necessitar. |
| Contato operacional | Nome de responsável e contato necessário a uma tarefa de atendimento | Somente quem precisa conduzir o atendimento; famílias não consultam contatos umas das outras. Priorizar a comunicação interna. |
| Administrativo | Dados cadastrais e endereço necessário à matrícula, lotação e contatos | Secretaria autorizada no escopo; responsáveis consultam os dados permitidos de seus dependentes. |
| Saúde e cuidado | Instrução operacional necessária, como uma restrição alimentar | Bloco separado. Ação específica, finalidade e designação; D06/T03 definem campos, leitores e retenção. Não aparece na chamada comum. |
| Guarda e retirada | Poderes verificados e orientação de retirada necessária | Operador de retirada vê a instrução vigente, não o dossiê de guarda. Documentos comprobatórios ficam separados, sujeitos à D03/T03. |
| Pedagógico | Atividades, presença e acompanhamento do aluno | Turma atribuída ou dependente autorizado; nota de atendimento interno não é automaticamente visível à família. T03 define leitores por campo. |
| Imagem | Peça, pessoas identificáveis e permissões por finalidade/canal | Designação editorial e autorização aplicável; a prévia não pode expor terceiros indevidamente. D09 e T34–T36 detalham o fluxo. |
| Auditoria | Ator, ação, escopo, referência do recurso, horário e resultado | Permissão própria e finalidade; não copiar conteúdo sensível, documentos ou credenciais para o log. |
| Agregado | Totais sem lista nominal e com proteção contra identificação por grupos pequenos | Limiar e regras de divulgação serão definidos na T44; não liberar recortes antes disso. |

Esses conjuntos orientam a T01 e não substituem o dicionário de campos da T03. Dados de religião e dossiês civis não recebem permissão genérica nesta proposta. Sua coleta e leitura ficam pendentes de D06.

## 4. Matriz papel × ação × escopo

Todas as linhas exigem as verificações da seção 1. Recursos de fases futuras só poderão ser habilitados quando a respectiva entrega e suas decisões estiverem prontas. Nenhuma linha permite comunicação privada entre adulto e menor.

### Organização, nomeação e operação

| Papel | Ação | Escopo | Condição | Dado visível | Exemplo negado |
|---|---|---|---|---|---|
| Administrador global | `organization.manage`: criar regional e manter padrões compartilhados | Global | Nomeação institucional registrada conforme D01 | Estrutura, configuração e contatos institucionais | Abrir a ficha de um aluno por administrar a regional. |
| Administrador global | `assignments.grant` / `assignments.revoke`: gerir administradores globais e diretores regionais | Global | Fluxo da seção 5; sem aprovar a própria elevação | Atribuição, escopo, vigência e identificação do colaborador nomeado | Usar a nomeação para se conceder acesso nominal de secretaria. |
| Direção geral | `announcements.publish` / `calendar.manage`: comunicar e organizar programação global | Global | Atribuição editorial/global explícita; prévia de público sem exposição nominal desnecessária | Conteúdo, público por escopo e totais de entrega | Alterar o evento próprio de uma turma. |
| Direção geral / administrador global | `reports.aggregate.view`: consultar indicadores consolidados | Global | T44 habilitada e limiar aprovado | Agregados autorizados | Abrir lista de crianças a partir de um total. |
| Diretor regional | `organization.manage`: gerir núcleos e ofertas da regional | Regional | Atribuição regional vigente | Estrutura e dados operacionais das unidades | Alterar um núcleo de outra regional. |
| Diretor regional | `assignments.grant` / `assignments.revoke`: nomear direção local e funções regionais | Regional | Somente papéis e alcance delegáveis aprovados em D01 | Atribuições e vigências necessárias | Criar outro administrador global. |
| Diretor local | `classrooms.manage`: organizar turmas e lotação | Núcleo | Regras pedagógicas da T02; sem alterar vínculo de guarda | Turmas, colaboradores lotados e dados básicos necessários | Remanejar aluno de outro núcleo. |
| Diretor local | `assignments.grant` / `assignments.revoke`: nomear equipe local | Núcleo | Catálogo delegável aprovado; coordenação/turma explicitamente delimitadas | Papel, turma, vigência e identificação do nomeado | Dar ao professor acesso a todos os núcleos. |
| Coordenador | `classrooms.view` / `followups.manage`: acompanhar turmas e tarefas por faltas | Turmas atribuídas | Vínculo vigente; contato somente na tarefa necessária | Básico, presença, pendência e contato operacional autorizado | Consultar turma vizinha ou dossiê de saúde. |
| Responsável por auditoria/privacidade | `audit.view`: revisar acessos e exportações | Escopo expressamente designado | Solicitação/finalidade registrada; sem acesso automático ao conteúdo original | Metadados de auditoria | Usar o log como cópia de ficha ou ler outra regional. |
| Suporte técnico | `support.diagnostics.view`: verificar funcionamento | Ambiente e serviço atribuídos | Diagnóstico sem acesso nominal por padrão; acessos institucionais adicionais exigem fluxo próprio | Estado de serviços e erros sem conteúdo pessoal | Entrar como uma família ou consultar seu cadastro para depurar. |

### Matrícula, família e trabalho pedagógico

| Papel | Ação | Escopo | Condição | Dado visível | Exemplo negado |
|---|---|---|---|---|---|
| Secretaria | `enrollments.manage` / `profiles.admin.view`: analisar solicitação e matrícula | Núcleo atribuído; regional somente com atribuição específica | Regras T02/T03 e aprovação de matrícula separada da criação da conta | Cadastro administrativo necessário | Pesquisar famílias de outra unidade ou ler saúde por ter cadastro administrativo. |
| Secretaria com designação de representação | `representation.verify`: verificar vínculo e poderes do responsável | Unidade atribuída e dependente atendido | D03/T03; procedimento de contestação e trilha de decisão | Evidência mínima de representação em área restrita | Aceitar como responsável alguém que só consta como contato de emergência. |
| Operador de retirada | `pickup.verify`: conferir retirada | Ocorrência e unidade designadas | D03; instrução vigente e operador identificado | Identificação e orientação necessária de retirada | Abrir processo de guarda ou autorizar imagem pelo responsável. |
| Professor | `students.view` / `activities.manage`: acompanhar alunos e preparar atividades | Turma atribuída | Atribuição vigente; limites pedagógicos definidos | Básico e conteúdo pedagógico autorizado | Consultar endereço ou alunos de outra turma por URL direta. |
| Professor | `attendance.manage`: lançar, fechar e solicitar correção da chamada | Turma atribuída e ocorrência realizada | T21; lista completa para fechamento; correção com motivo; encontro cancelado não admite chamada | Nomes elegíveis e estados de presença | Lançar falta em encontro cancelado ou fechar chamada de turma vizinha. |
| Auxiliar | `students.view` / `attendance.draft`: apoiar chamada | Turma atribuída | Escala vigente; proposta limita a rascunho, fechamento pelo professor ou substituto designado | Básico e rascunho de presença | Fechar chamada ou receber dados de saúde só por ser auxiliar. |
| Colaborador designado para cuidado | `care.instructions.view`: consultar orientação de cuidado | Alunos e ocorrência sob cuidado designado | D06/T03 validadas; finalidade e acesso registrados | Instrução operacional mínima, sem dossiê clínico | Consultar todo o histórico médico ou turma sem designação. |
| Responsável | `dependents.view` / `profiles.correction.request`: acompanhar dependente e pedir correção | Cada dependente autorizado | Vínculo verificado e poder de acompanhar; atualização de representação exige secretaria | Dados do dependente liberados por finalidade e próprio contato | Alterar o poder de outro responsável ou listar colegas do filho. |
| Responsável | `enrollments.request` / `event_registrations.create`: solicitar matrícula ou inscrever dependente | Dependente e oferta/evento autorizado | Poder de inscrever, matrícula/audiência elegíveis e prazos | Cadastro reaproveitado, termos e situação da inscrição | Inscrever filho sem poder vigente ou aprovar a própria matrícula. |
| Responsável | `acknowledgements.confirm`: confirmar ciência | Obrigação e dependente representado | Poder de confirmar; política D04; registrar pessoa, versão e obrigação | Aviso e pendência correspondentes | Confirmar pelo outro filho ou por outra família sem representação. |
| Responsável | `media_consents.decide`: autorizar ou recusar imagem | Próprio dependente e peça/finalidade | Poder de autorizar imagem; D03/D09; conflitos bloqueiam publicação | Prévia protegida e finalidade/canal | Autorizar criança de outra família ou transformar galeria interna em licença externa. |
| Aluno sem conta | Nenhuma ação autenticada | — | Matrícula existe sem login infantil | Conteúdo acessado por representante habilitado | Criar acesso automático apenas por possuir matrícula. |
| Adolescente com conta autorizada | `learning.own.view` / `event_registrations.create`: acessar conteúdo próprio e eventual inscrição | Própria pessoa e oferta autorizada | D12; conta e delegação válidas; inscrição só se expressamente delegada | Conteúdo próprio, termos e situação da própria inscrição | Representar irmão, autorizar a própria imagem como responsável ou acessar mensagens privadas de adultos. |
| Colaborador/aluno com conta | `profile.own.manage`: manter preferências e contato próprio | Própria conta | Verificação de novo contato; campos administrativos protegidos | Próprio perfil autorizado | Alterar cargo, escopo ou data de nascimento aprovada para obter acesso. |

### Comunicação, mídia e demais módulos

| Papel | Ação | Escopo | Condição | Dado visível | Exemplo negado |
|---|---|---|---|---|---|
| Diretor regional/local ou editor designado | `announcements.publish` / `calendar.manage`: publicar aviso e evento | Respectivo escopo; professor somente turma atribuída com essa ação | Prévia de público; regras de aprovação local; não editar evento superior | Conteúdo e destinatários necessários à operação | Publicar para outra regional ou obter contatos de todos os destinatários. |
| Professor / equipe de atendimento | `classroom_threads.manage`: responder e moderar comunicação da turma | Turma atribuída | T17; resposta privada da família visível apenas à equipe autorizada | Tópico e respostas necessárias | Expor resposta privada às outras famílias ou abrir conversa privada com menor. |
| Responsável / participante adulto | `classroom_threads.reply`: responder à equipe | Turma do dependente ou grupo autorizado | Vínculo vigente; destinatário é a equipe | Própria resposta e conteúdo autorizado | Ler respostas privadas de outra família. |
| Coordenação / direção / editor designado | `acknowledgements.pending.view`: acompanhar ciência e contato | Escopo do aviso e da atribuição | Finalidade de acompanhamento; D04; contato assistido separado da ciência digital | Obrigações, confirmados/pendentes e contato operacional autorizado | Marcar “Estou ciente” em nome do pai ou abrir pendentes de outra unidade. |
| Equipe de mídia | `media.upload` / `media.review`: receber, organizar e revisar peça | Coleção e escopo atribuídos | Upload privado; D09; identificação manual e prévia protegida | Peças em revisão e situação das autorizações necessárias | Publicar porque fez upload ou navegar em ficha de saúde. |
| Publicador de mídia designado | `media.publish`: divulgar peça aprovada | Coleção, audiência e canal autorizados | Todas as pessoas identificáveis autorizadas; revisão editorial; ausência de decisão bloqueia | Peça aprovada e evidência mínima de liberação | Publicar peça com uma autorização pendente ou reutilizar após revogação. |
| Instrutor / gestor de curso | `courses.manage` / `course_progress.view`: preparar curso e acompanhar inscritos | Curso/edição atribuídos | T37/T38; conteúdo autorizado e finalidade pedagógica | Material, dúvidas e progresso dos inscritos | Abrir ficha escolar do filho de um cursista. |
| Cursista | `courses.enrolled.view` / `course_questions.create`: estudar e enviar dúvida | Própria matrícula na edição | Inscrição vigente e janela de liberação | Aulas, materiais e próprio progresso; dúvidas conforme moderação | Abrir aula sem inscrição ou ver tentativas de outro cursista. |
| Responsável por patrimônio | `spaces.manage` / `reservations.decide`: manter ambientes e decidir reserva | Núcleo atribuído | T40/T41; conflito verificado no banco na aprovação | Ambiente, horário, finalidade, grupo e solicitante necessários | Aprovar sobreposição ou consultar nomes de crianças no mapa público do grupo. |
| Colaborador designado para reserva | `reservations.request`: solicitar ambiente | Núcleo autorizado e grupo sob sua responsabilidade | Horário, capacidade e recursos compatíveis | Ambiente e disponibilidade autorizada | Aprovar a própria solicitação sem atribuição de aprovação. |
| Secretaria / responsável por materiais | `orders.fulfill`: atender pedido e registrar entrega | Unidade e catálogo atribuídos | T24; quantidade e recebedor registrados | Pedido, beneficiário, retirada e saldo | Usar entrega como receita ou acessar pedido de outra unidade. |
| Responsável / colaborador autorizado | `orders.request`: solicitar produto | Próprio pedido ou dependente autorizado | Catálogo disponível; representação quando atuar pelo filho | Produto, quantidade e andamento do próprio pedido | Marcar produto como entregue ou ver pedidos de outra família. |
| Tesouraria | `finance.manage`: operar lançamentos e conciliação | Regional/núcleo expressamente atribuídos | Somente F7, D10 e regras financeiras aprovadas; auditoria | Registros financeiros necessários | Ter acesso financeiro por ser diretor ou inferir pagamento de um pedido entregue. |
| Analista autorizado | `reports.nominal.export`: exportar dados nominais necessários | Escopo, finalidade e prazo específicos | Ação separada da consulta; campos/retenção T03; auditoria e revalidação ao gerar/baixar | Apenas colunas autorizadas | Exportar saúde, religião, contatos ou outra unidade por ter acesso a um relatório geral. |

## 5. Nomeação, delegação e substituição — proposta para D01

### Quem pode nomear

| Destino | Proposta de autoridade nomeante | Limite e aprovação proposta |
|---|---|---|
| Primeiro administrador global | Direção institucional, com registro fora da aplicação | Provisionamento inicial controlado; sem credencial padrão no repositório ou autocadastro público. Identidade do titular ainda não definida. |
| Outros administradores globais e direção regional | Administrador global nomeado | Aprovação institucional por pessoa distinta do beneficiário; mudança com autor, aprovador e vigência. Administrador global não se autonomeia para outra função. |
| Direção local e funções regionais | Diretor regional da mesma regional | Apenas catálogo delegável aprovado; sem poderes nacionais nem acesso a outra regional. |
| Coordenação e equipe local | Diretor local do mesmo núcleo, ou direção regional autorizada | Exigir turmas para coordenação/professor/auxiliar e designações para funções restritas. Atribuição regional não substitui a análise de finalidade dos dados. |
| Substituto temporário | Autoridade que poderia nomear o titular para aquele escopo | Definir início, fim e ações necessárias; sem copiar acessos pessoais, familiares ou de outros grupos do titular. |
| Responsável por criança | Secretaria com ação de verificação de representação | Validar vínculo e poderes conforme D03; não tratar vínculo familiar como cargo delegável por professor. |
| Adolescente com conta/delegação | Representante habilitado solicita; equipe verifica conforme D12 | Poderes delimitados, prazo e revogação; sem ativação antes da decisão aplicável. |

### Como a mudança deve funcionar na T07/T12

1. **Solicitação:** informar beneficiário, papel, escopo, ações, início/fim e motivo. Ninguém edita diretamente o próprio cargo.
2. **Conferência:** verificar autoridade do nomeante no servidor, escopo contido no permitido e catálogo de ações delegáveis. Não repassar poderes recebidos sem faculdade de delegação. O papel global de nomeação é uma permissão específica; não implica acesso operacional às unidades.
3. **Aprovação e ativação:** guardar autor/aprovador; registrar a aceitação aplicável e ativar na data prevista. Solicitação pendente não concede acesso. MFA será requisito dos acessos privilegiados na T08.
4. **Substituição:** criar atribuição própria com prazo obrigatório. Registrar se o titular continua ativo ou se sua atribuição será suspensa no período. Troca não equivale a compartilhar conta ou senha.
5. **Encerramento:** revogar ou expirar a atribuição sem apagar histórico. Revalidar acesso na próxima operação, mesmo com sessão aberta, fila pendente ou link anteriormente emitido. Uma substituição vencida não pode ser selecionada como contexto.
6. **Continuidade:** impedir remoção do último administrador global ativo sem sucessor válido; prever recuperação institucional controlada, a definir em D01/D11, sem conta compartilhada permanente.

A vigência deve usar instante com fuso tratado: início inclusivo e fim exclusivo. Exemplo de substituição fictícia: início `2026-11-17T00:00:00-03:00`, fim `2026-11-25T00:00:00-03:00`. O acesso já estará encerrado no instante do fim.

## 6. Cenário fictício para revisão e testes futuros

| Identificador | Situação |
|---|---|
| Regional Aurora | Contém os núcleos Jardim e Ponte. |
| Regional Horizonte | Contém o núcleo Farol. |
| Turmas Jardim/Sementes, Ponte/Caminhos e Farol/Sementes | As duas turmas chamadas Sementes pertencem a regionais diferentes. Toda seleção deve identificar regional e núcleo, além do nome. |
| Ana Exemplo | Professora de Jardim/Sementes; mãe de Lucas Exemplo em Ponte/Caminhos. Tem poderes de acompanhar, confirmar e inscrever Lucas, sem poder de autorizar imagem neste cenário. |
| Bruno Exemplo | Responsável por Lucas e por sua irmã Lia Exemplo, em Jardim/Sementes, com poderes de acompanhar, confirmar e inscrever para ambos. Poder de imagem será definido em outro caso fictício específico. |
| Caio Exemplo | Substitui a professora de Jardim/Sementes no intervalo da seção 5. Não recebe nenhum vínculo familiar do titular. |
| Dora Exemplo | Diretora local do núcleo Jardim. |
| Eva Exemplo | Secretária do núcleo Jardim, sem designação para saúde ou verificação de documentos de guarda. |
| Fábio Exemplo | Administrador global, sem papel regional ou familiar. |
| Gabi Exemplo | Auxiliar de Jardim/Sementes, sem poder de fechar chamada. |
| Hugo Exemplo | Inscrito em um curso da equipe; não é colaborador de Jardim/Sementes. |

### Casos para demonstrar à direção

As respostas são **resultados esperados da proposta**. São uma revisão documental; ainda não são testes de autorização executados no Laravel.

| Caso | Ator, contexto e ação | Resultado esperado e motivo |
|---|---|---|
| 01 | Ana, como professora, abre alunos de Jardim/Sementes | Permitir dados básicos/pedagógicos autorizados da turma atribuída. |
| 02 | Ana, como professora, troca o identificador para Farol/Sementes na URL ou na busca | Negar acesso e não revelar nomes, contagens ou resultados da outra regional. Mesmo nome não significa mesmo escopo. |
| 03 | Ana, como professora, tenta abrir a turma Ponte/Caminhos de seu filho | Negar acesso à turma. O papel de mãe não amplia sua atribuição profissional. |
| 04 | Ana troca para o contexto familiar e abre Lucas | Permitir os dados do dependente que o poder de acompanhar libera; não listar seus colegas. |
| 05 | Ana, como mãe, tenta lançar presença ou publicar atividade em Ponte/Caminhos | Negar. A condição de professora em outra turma não é somada ao contexto familiar. |
| 06 | Ana tenta autorizar imagem de Lucas | Negar neste cenário: acompanhar, confirmar e inscrever não incluem autorizar imagem. |
| 07 | Bruno confirma o aviso referente a Lucas | Registrar a obrigação de Lucas na versão recebida. A de Lia permanece pendente, se D04 adotar ciência por dependente. O caso fica condicionado à decisão, sem escolha presumida. |
| 08 | Bruno tenta consultar respostas privadas de outra família | Negar, mesmo se os filhos estiverem na mesma turma. |
| 09 | Caio lança chamada de Jardim/Sementes em 24/11/2026 dentro da vigência | Permitir se a ocorrência estiver realizada e a ação estiver na substituição. |
| 10 | Caio repete a operação em 25/11/2026, com a sessão ainda aberta | Negar: fim exclusivo atingido. Remover o contexto expirado. |
| 11 | Caio usa um link privado emitido durante a substituição após seu encerramento | Negar leitura de original, miniatura e download. A validade do link não substitui a autorização atual. |
| 12 | Dora organiza uma turma do núcleo Jardim e depois tenta alterar Ponte/Caminhos | Permitir a primeira operação e negar a segunda: alcance local não abrange Ponte. |
| 13 | Eva analisa uma matrícula em Jardim e tenta abrir o bloco de saúde | Permitir cadastro administrativo necessário; negar saúde sem designação específica aprovada em D06. |
| 14 | Um contato de emergência tenta confirmar aviso ou inscrever Lucas | Negar se não houver vínculo de representação com o poder exigido. |
| 15 | Fábio cria uma regional e tenta pesquisar fichas nominais de crianças | Permitir manutenção estrutural; negar fichas. Nenhum atalho de administrador global contorna as regras. |
| 16 | Dora tenta nomear administrador global ou atribuir núcleo Ponte a um professor | Negar por papel/escopo fora do catálogo delegável. |
| 17 | Um aprovador tenta aprovar sua própria elevação ou a solicitação ainda pendente | Negar autoelevação; solicitação pendente continua sem efeito. |
| 18 | Gabi preenche rascunho da chamada e tenta fechá-la | Permitir rascunho; negar fechamento sem designação de professor/substituto. |
| 19 | Ana tenta lançar presença em encontro cancelado | Negar mesmo com papel e turma corretos: o estado da ocorrência impede a ação. |
| 20 | Hugo abre curso em que está inscrito e tenta consultar crianças da turma de outro cursista | Permitir material liberado do curso; negar dados da turma e dos filhos de participantes. |
| 21 | Mídia tenta publicar peça com três pessoas, sendo uma sem decisão de imagem | Negar publicação e exposição de capa; upload não significa autorização. |
| 22 | Um usuário exporta uma lista que pode consultar, sem ação de exportação | Negar a exportação. Permissão de consulta não inclui arquivo nominal. |
| 23 | A atribuição de Ana é revogada enquanto há envio ou exportação em fila | Revalidar antes de executar/entregar e negar o que depender do vínculo revogado; não usar lista ou cache antigos como autorização. |
| 24 | Bruno sai da conta e outro usuário entra no mesmo aparelho | Limpar contexto e conteúdo privado; nenhum dado ou arquivo privado da conta anterior pode reaparecer. |

Na T07 e nas entregas de cada módulo, levar os casos aos caminhos pertinentes: lista, busca, contagem, detalhe, alteração, requisição direta, ação Livewire, arquivo, exportação, fila e cache. Os casos dos módulos futuros serão executados quando esses módulos existirem. O histórico registra quem podia atuar na data do encontro; para abrir esse histórico hoje, a pessoa ainda precisa de autorização atual.

## 7. Decisões preparadas para avaliação

### D01 — poderes, nomeação e delegação

**Recomendação para o piloto:** adotar a separação entre administração global de estrutura e operação nominal por atribuição local/regional; nomeação pelo fluxo da seção 5; coordenação/professores por turmas explícitas; substituição com prazo; auxiliar limitado a rascunho de chamada. A direção precisa registrar sua escolha sobre os itens abaixo antes de conceder acessos reais.

| Item de D01 | Proposta apresentada | Alternativa ou ajuste a avaliar | Situação |
|---|---|---|---|
| Administração nacional | Vários administradores globais, sem leitura nominal automática | Acesso operacional específico só por atribuição independente, justificada e auditada | Em aberto. |
| Nomeação | Autoridades e limites da seção 5; aprovação institucional distinta do beneficiário para privilégios globais/regionais | Exigir segundo aprovador também para nomeações locais, se houver equipe disponível | Em aberto. |
| Coordenação | Uma ou mais turmas explicitamente atribuídas | Atribuição para todas as turmas de um núcleo somente se aprovada expressamente | Em aberto. |
| Auxiliares | Consulta básica e rascunho; fechamento pelo professor/substituto | Permitir fechamento a auxiliar apenas com ação e designação específicas | Em aberto. |
| Substituição | Prazo obrigatório e escolha explícita sobre suspensão do titular | Eventual coexistência de titular/substituto deve ficar registrada | Em aberto. |
| Recuperação e continuidade | Sucessor antes de remover último administrador; recuperação institucional rastreável | Direção define custódia e recuperação com tecnologia em D11 | Em aberto. |

### Parâmetros nacionais e autonomia regional — proposta para D01

O backlog da T01 também pede identificar parâmetros que exigem aprovação nacional. A divisão abaixo é uma proposta de competência; não registra aprovação ou regra oficial do IBUC.

| Parâmetro | Alteração proposta para aprovação nacional | Autonomia regional proposta |
|---|---|---|
| Papéis e delegação | Catálogo de ações, limites máximos delegáveis e nomeação global/regional | Nomear equipe dentro dos limites aprovados e restringir ações por escopo. Não ampliar o catálogo unilateralmente. |
| Isolamento e proteção de acesso | Regras comuns de segregação, exigência de MFA privilegiado, auditoria e acesso nominal excepcional | Restringir acessos e definir responsáveis locais; sem desligar as verificações comuns. |
| Marca e interface compartilhada | Uso/licenciamento da marca e componentes institucionais, conforme D07 | Identificar a regional e seus núcleos nos espaços previstos, sem alterar a marca oficial. |
| Currículo e parâmetros pedagógicos | Alterar referência oficial compartilhada ou regra que deva valer nacionalmente | Propor/adotar edição e configurações locais explicitamente permitidas, com vigência e D02; diferenças são documentadas na T02. |
| Dados e imagem | Padrões comuns de finalidade, acesso, retenção e uso por canal, com D03/D06/D09 | Adotar restrições adicionais e designar operadores; ampliar coleta ou finalidade exige revisão aplicável, sem consentimento presumido. |
| Agenda e comunicação | Editar evento ou publicação de origem global | Criar programação própria e orientação complementar; não sobrescrever conteúdo global nem publicar em outra regional. |

### Dependências que continuam com seus responsáveis

| Decisão | Responsável do roteiro | O que falta definir para os acessos relacionados |
|---|---|---|
| D03 | Secretaria e privacidade | Como comprovar representação, contestar vínculo e fornecer instruções de retirada sem expor documentos. |
| D04 | Direção local e famílias | Unidade da ciência: indivíduo, responsável designado ou um responsável por filho; procedimento de contato assistido. |
| D06 | Privacidade e direção | Campos de saúde, religião e documentos necessários; leitores por finalidade, retenção e acesso de emergência. Sem acesso excepcional automático nesta proposta. |
| D09 | Privacidade e mídia | Captação, prévia, autorização por canal e peça, conflito, revogação e retirada. |
| D11 | Tecnologia e direção | Custódia, continuidade e recuperação de acesso/ambiente. |
| D12 | Direção e privacidade | Quais poderes um adolescente pode receber, quem pode delegar, prazo e revogação. |

As demais decisões D02, D05, D07, D08 e D10 continuam nas entregas correspondentes da F0; D13 e D14 nas fases previstas. A T01 não as aprova nem altera suas situações.

## 8. Revisão e passagem para a próxima entrega

- [x] Matriz com papel, ação, escopo, condição, dado visível e exemplo negado.
- [x] Nomeação, delegação, substituição, revogação e continuidade descritas.
- [x] Casos permitido, negado, professora/mãe, irmãos, outra regional e vínculo vencido preparados.
- [x] Dados fictícios e decisões pendentes identificados.
- [ ] Direção e secretaria percorreram os casos e registraram ajustes/aceite da T01.
- [ ] D01 decidida com referência de data/ata, sem expor dados pessoais no repositório público.
- [ ] Verificações no Laravel implementadas e executadas na T07 e nos módulos correspondentes.

**Próximo trabalho elegível:** preparar a T02 (regras pedagógicas) e a T03 (dicionário de campos e representação) usando esta versão como proposta e mantendo as decisões abertas. A validação da direção deve citar a versão e os itens aceitos/alterados. A planilha de referência permanece preservada; o estado corrente da execução é registrado em [IMPLEMENTACAO.md](../../IMPLEMENTACAO.md).
