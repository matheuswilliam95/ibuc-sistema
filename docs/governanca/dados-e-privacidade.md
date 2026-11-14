# T03 — Dicionário de dados, acesso e retenção

**Estado:** Em validação. **Versão:** proposta 1, de 14/11/2026. **Validação:** secretaria, privacidade e direção. As finalidades, hipóteses legais e prazos abaixo são propostas para avaliação em D03/D06/D09/D11/D12; não habilitam coleta real.

O cadastro separa pessoa, conta, matrícula, lotação, representação, saúde, documentos e decisões de imagem. Uma criança pode existir sem login. A família pode recusar imagem e continuar o processo de matrícula. As permissões seguem [a T01](matriz-de-acesso.md); os procedimentos e minutas estão em [responsabilidade e autorizações](responsabilidade-e-autorizacoes.md).

## 1. Como ler o dicionário

As chaves são nomes lógicos para orientar T06–T14, não tabelas/migrações já implementadas. Finalidades `F01`–`F08` identificam a hipótese em análise e retenções `R01`–`R10` identificam o prazo proposto. Assim, cada linha possui finalidade, base em análise, leitores, alteração e destino do dado.

**Necessidade:** obrigatório no respectivo fluxo não significa obrigatório para toda pessoa. Campo condicionado só aparece quando a finalidade e a regra aplicáveis estiverem aprovadas. “Não informado” não equivale a “não possui”. O segundo responsável é opcional; o modelo aceita um ou mais e poderes diferentes por criança.

**Leitores:** secretaria sempre significa atribuição na unidade da matrícula/atendimento, com ação específica quando necessária. Professor/auxiliar não recebem cadastro administrativo completo. Responsável acessa o próprio contato e dados permitidos do dependente, não dados privados de outro adulto. Administrador global não ganha leitura nominal por esse cargo. Exportação é ação separada da consulta.

Endereços, contatos e instruções devem ter contexto de uso; compartilhar o identificador de pessoa não expõe automaticamente seus registros em outra regional. Se um conjunto revelar filiação/convicção religiosa ou saúde, aplicar a análise de dado sensível mesmo quando não existir um campo chamado “religião”.

## 2. Finalidades e hipóteses a validar

| Código | Finalidade delimitada | Hipótese proposta ou avaliação exigida | Quem decide |
|---|---|---|---|
| F01 | Criar conta, verificar contato e permitir acesso restrito | Avaliar art. 7º, I, ou V quando a relação aplicável efetivamente o sustentar; dados que revelem informação sensível exigem análise do art. 11. Não converter aceite de uso em autorização geral. | Privacidade/direção, D06 |
| F02 | Matrícula, lotação, atividades, presença, comunicação e pedidos ligados ao atendimento | A vinculação religiosa pode ser sensível. Proposta: avaliar consentimento específico do art. 11, I, e melhor interesse; eventual outra hipótese deve ser justificada por operação. Separar o necessário de escolhas facultativas. | Privacidade/direção e secretaria, D06 |
| F03 | Receber e consultar orientação mínima de cuidado e acessibilidade | Proposta de consentimento específico/destacado, art. 11, I. Não presumir que igreja seja serviço de saúde ou usar “tutela da saúde” genericamente. Eventual emergência exige protocolo próprio, sem liberar acesso geral. | Privacidade/direção, D06 |
| F04 | Verificar representação, poderes e retirada | Definir a hipótese do art. 7º ou 11 conforme necessidade e conteúdo da evidência. Parentesco declarado e telefone verificado não comprovam todos os poderes. Obrigações ou exercício de direitos só serão invocados quando identificados. | Secretaria/privacidade, D03/D06 |
| F05 | Captar e usar imagem em finalidade, audiência e canal determinados | Proposta: consentimento específico, art. 7º, I, e art. 11, I, quando revelar dado sensível; verificar representante e melhor interesse. Imagem não é tratada como biometria por padrão; não haverá reconhecimento facial. | Privacidade/mídia, D09 |
| F06 | Proteger conta e rastrear operações sensíveis | Para dados comuns, avaliar art. 7º, IX, com necessidade/balanceamento; obrigação legal somente se identificada. Metadados que revelem informação sensível precisam de hipótese adequada do art. 11. Reduzir conteúdo dos logs. | Privacidade/tecnologia, D06/D11 |
| F07 | Verificar vínculo de colaborador e documentos exigíveis | Determinar enquadramento e obrigação concreta, inclusive ECA art. 59-A; art. 7º, II, ou outra hipótese demonstrada, e art. 11 quando aplicável. Não presumir que todos os documentos da ficha original sejam necessários. | Privacidade/direção, D06 |
| F08 | Atender direitos e manter evidência mínima de decisões de tratamento | Avaliar art. 7º, II, e art. 11, II, a, quando necessários à obrigação identificada. Especificar prova, prazo e eventual conservação permitida; não guardar todo o cadastro sob rótulo de auditoria. | Privacidade/direção, D06 |

São hipóteses para revisão, não uma escolha legal concluída. A ANPD admite bases dos arts. 7º ou 11 para crianças/adolescentes, com prevalência de seu melhor interesse. [Orientação oficial da ANPD](https://www.gov.br/anpd/pt-br/assuntos/noticias/anpd-divulga-enunciado-sobre-o-tratamento-de-dados-pessoais-de-criancas-e-adolescentes). As referências completas e os pontos do ECA/ECA Digital estão em [fontes e decisões](fontes-e-decisoes.md).

## 3. Pessoa, conta e contato

| Campo lógico | Tipo e necessidade | Finalidade/base | Leitores e alteração | Retenção |
|---|---|---|---|---|
| `person.id` | Identificador interno; obrigatório; não usar CPF como chave pública | F01/F02 conforme vínculo | Serviços autorizados; identificar pessoa não autoriza abrir seus outros vínculos | R02; vínculo técnico mínimo conforme descarte |
| `person.full_name` | Texto; obrigatório no cadastro de aluno/adulto | F02 | Secretaria e titular/representante pertinente; professor vê nome do aluno da turma | R02 |
| `person.preferred_name` | Texto opcional para tratamento respeitoso | F02 | Mesmos leitores do nome, conforme finalidade | R02 |
| `person.birth_date` | Data válida; necessária para faixa do aluno; colaborador conforme D06 | F02/F07 | Secretaria e titular/representante; professor vê idade necessária à atividade, não nascimento por padrão | R02 |
| `person.age_at_date` | Derivado, sem idade digitada no cadastro | F02 | Leitor do perfil autorizado; histórico guarda referência da decisão pedagógica | Sem retenção própria; referência na matrícula R03 |
| `account.person_id` | Relação com pessoa; apenas quem precisa de conta | F01 | Titular e operador de contas autorizado; não cria matrícula | R02 |
| `account.login_contact` | E-mail ou telefone verificado, com alternativa assistida sem conta | F01 | Titular e operação autorizada; mascarado quando suficiente | R02 |
| `account.verification_state` | Estado, data e canal de verificação | F01/F06 | Titular e operação de acesso; canal verificado não prova guarda | R06 |
| `account.credentials` | Hash de senha e fatores protegidos; nunca texto de senha ou código em log | F01/F06 | Somente mecanismos de autenticação; administradores não leem segredos | R06; revogar ao encerrar |
| `account.status` | Pendente, ativa, suspensa ou encerrada; data/motivo restrito | F01/F06 | Titular recebe orientação necessária; operação autorizada decide | R02/R06 |
| `contact.phone` | Telefone por adulto e finalidade; ao menos um meio de contato operacional ou via assistida aprovada | F01/F02/F04 conforme uso | Próprio adulto e secretaria; professor somente em tarefa autorizada de contato | R02 |
| `contact.channel_preference` | Aplicativo, telefone ou atendimento assistido; push opcional | F01/F02 | Titular e equipe que conduz o contato | R02 |
| `address.locality` | Cidade/UF quando necessário ao atendimento | F02 | Secretaria e titular/representante do registro | R02 |
| `address.details` | Logradouro, número, complemento e CEP somente se finalidade aprovada | F02 | Secretaria autorizada e titular/representante; fora da chamada | R02 |
| `person.birthplace` | Naturalidade da ficha original; coleta desativada na proposta mínima | D06 deve demonstrar finalidade/hipótese antes de habilitar | Sem leitores operacionais enquanto desativado | Não coletar |

Não exigir e-mail, telefone próprio, CPF ou senha da criança. Homônimos não serão mesclados automaticamente. A associação de conta existente exige verificação e não revela se a pessoa participa de outra regional.

## 4. Matrícula, colaborador e acompanhamento

| Campo lógico | Tipo e necessidade | Finalidade/base | Leitores e alteração | Retenção |
|---|---|---|---|---|
| `enrollment.scope` | Regional, núcleo, oferta e turma; obrigatório | F02 | Secretaria, direção/coordenação no alcance e família do aluno; professor vê própria turma | R03 |
| `enrollment.curriculum` | Edição, nível, módulo, regulamento e corte aplicados | F02 | Mesmos leitores, por tarefa; secretaria/coordenação aprovam | R03 |
| `enrollment.validity` | Estado, início/fim, autor, motivo e decisão | F02 | Secretaria e equipe de acompanhamento habilitada; família vê situação pertinente | R03 |
| `enrollment.movement` | Origem/destino e equivalência necessária da transferência | F02 | Operadores autorizados nas unidades envolvidas; não expor demais vínculos | R03 |
| `assignment.scope_role` | Papel, unidade/turma, ações delegáveis, início/fim | F02/F07 | Nomeante autorizado e colaborador; famílias veem função/nome da equipe necessária | R03 |
| `assignment.approval` | Autor, aprovador, motivo e estado | F02/F06 | Operação de nomeação e auditoria autorizadas | R03/R06 |
| `volunteer.term_receipt` | Versão, manifestante, data e evidência do termo institucional | F07/F08 | Colaborador e secretaria designada; termo jurídico a validar | R05/R07 conforme obrigação |
| `attendance.status` | Não lançado, presente, falta ou falta justificada por ocorrência | F02 | Professor/auxiliar conforme T01; coordenação; responsável vê dependente | R03 |
| `attendance.justification` | Categoria e nota mínima; documento de saúde não é requisito padrão | F02; F03 se revelar saúde | Equipe que analisa justificativa; nota sensível separada da chamada e dos demais responsáveis | R03; eventual saúde R04 |
| `followup.contact_record` | Tentativa, canal, retorno necessário e encaminhamento | F02 | Professor/coordenação designados; detalhe interno não se torna mural ou perfil público | R03 |
| `learning.progress` | Atividade, lição, versão, entrega e avaliação necessária | F02 | Equipe pedagógica autorizada e família/dependente habilitados | R03 |
| `acknowledgement.receipt` | Ator, filho/obrigação, aviso/versão, data e origem digital/assistida | F02/F08 | Confirmante e acompanhamento no escopo; sem lista de outras famílias | R07 |
| `event_registration.answers` | Versão do formulário, participante, representante e respostas necessárias | F02; campo extra sensível exige F03 ou hipótese própria | Organização do evento na finalidade; não ficha completa da turma | R03; respostas de cuidado R04 |
| `order.fulfillment` | Solicitante, beneficiário, produto/variante, quantidades e recebedor | F02 | Família/solicitante e equipe de materiais do núcleo | R03; sem dado de pagamento no piloto |

Dados de cursos, reservas e finanças ganharão dicionários complementares nas fases próprias, usando os mesmos critérios. Criar um campo em formulário dinâmico não contorna aprovação de finalidade, leitores e prazo; dado sensível não será aceito como texto extra de uso geral.

## 5. Responsabilidade, guarda e retirada

| Campo lógico | Tipo e necessidade | Finalidade/base | Leitores e alteração | Retenção |
|---|---|---|---|---|
| `responsibility.parties` | Criança e adulto distintos; obrigatório por vínculo | F04 | Secretaria designada e adulto no vínculo permitido; não listar todas as pessoas da rede | R03 |
| `responsibility.relationship` | Mãe, pai, tutor ou outra relação declarada | F04 | Secretaria e adulto pertinente; não concede poderes automaticamente | R03 |
| `responsibility.powers` | Acompanhar, confirmar ciência, inscrever, decidir imagem e autorizar retirada como poderes separados | F04 | Secretaria designada verifica; adulto vê seus poderes; editor não se autoconcede | R03/R07 |
| `responsibility.verification` | Pendente, verificado, em revisão, revogado ou expirado; revisor/data | F04 | Equipe designada; solicitante acompanha situação sem ver documentos de terceiros | R03 |
| `responsibility.evidence` | Tipo de evidência, conferência, referência e arquivo somente quando necessário/aprovado | F04 | Verificador de representação e privacidade; não professor ou diretor apenas por cargo | R05 |
| `responsibility.validity` | Início/fim, restrição operacional mínima e motivo restrito | F04 | Verificador; operador recebe apenas instrução necessária | R03; evidência R05 |
| `emergency_contact.details` | Nome, relação e telefone facultativos para emergência | F02/F04 | Secretaria e cuidador designado quando necessário; contato não recebe portal automaticamente | R02/R04 conforme conteúdo |
| `pickup_authorization.person` | Pessoa autorizada e identificação mínima necessária | F04 | Operador de retirada designado e representante que pode solicitar autorização | R03 |
| `pickup_authorization.validity` | Criança, unidade/ocorrência, período, autor e estado | F04 | Mesmos leitores; sem acesso ao dossiê de guarda | R03 |
| `pickup.receipt` | Quem entregou/recebeu, criança, ocorrência, instante e decisão usada | F04/F08 | Secretaria/privacidade autorizadas; família recebe registro pertinente | R07 |
| `delegation.adolescent` | Ação específica, concedente, adolescente, prazo e revogação | F04/F08; D12 e enquadramento prévios | Adolescente, representante habilitado e equipe verificadora | R03/R07 |

“Mãe/pai” será informação do vínculo, sem exigir uma composição familiar fixa. Duas crianças da mesma família podem ter representantes e poderes distintos. Cadastro de emergência, convivência familiar, retirada e responsabilidade legal não são equivalentes.

## 6. Saúde, documentos e religião

| Campo lógico | Tipo e necessidade | Finalidade/base | Leitores e alteração | Retenção |
|---|---|---|---|---|
| `care.food_restrictions` | Não informado/não relatado/relatado; orientação alimentar mínima, opcional após D06 | F03 | Adulto habilitado informa; equipe de cuidado designada consulta instrução necessária | R04 |
| `care.allergy_instruction` | Orientação operacional relatada, sem diagnóstico geral obrigatório | F03 | Mesmos leitores; acesso registrado | R04 |
| `care.accessibility_support` | Apoio necessário à participação; sem rótulo público “AEE” ou diagnóstico exigido | F03 | Equipe de cuidado/pedagógica expressamente designada, conforme finalidade | R04 |
| `care.medication_note` | Coleta de medicação geral desativada; habilitar somente informação necessária aprovada | F03; protocolo institucional separado | Designação específica; não constitui prescrição nem autorização para administrar medicamento | R04 |
| `care.review` | Fonte, data, responsável informante, revisão e autorização aplicável | F03/F08 | Equipe designada e informante habilitado | R04; prova mínima R07 |
| `document.identity_number` | CPF/RG/identificador da ficha da equipe; não obrigatório por padrão | F07/F04 somente com finalidade aprovada | Secretaria/verificador designado; mascarar em listas | R05 |
| `document.criminal_certificate` | Arquivo, emissor, emissão, conferência, próxima atualização e versão da regra | F07; D06 e art. 59-A a analisar | Colaborador e operador de conformidade designado; sem diretório geral da equipe | R05; cumprir guarda/atualização aplicáveis |
| `document.volunteer_attachment` | Arquivo privado do termo se necessário; conservar versão/recibo | F07/F08 | Colaborador e secretaria designada | R05/R07 |
| `religion.declaration` | Convicção/denominação ou classificação religiosa explícita | Sem coleta no piloto até D06 justificar F02/art. 11 por finalidade | Nenhum leitor operacional enquanto desativado; sem classificação em chamada | Não coletar |
| `media.profile_photo` | Foto de identificação opcional, finalidade distinta de galeria | F05; decisão específica necessária | Perfil restrito e operador que precise identificar; sem página pública infantil | R08 |

Não inferir religião individual pela igreja selecionada ou reproduzir os marcadores religiosos da A5 em listagens de turma. A vinculação ao projeto permanece protegida mesmo sem declaração religiosa. Saúde não será usada para pontuar comportamento, frequência ou elegibilidade automaticamente. Certidão não gera decisão automática sobre aptidão do colaborador; análise caberá à área competente e ao procedimento aprovado.

## 7. Imagem, evidência e operação

| Campo lógico | Tipo e necessidade | Finalidade/base | Leitores e alteração | Retenção |
|---|---|---|---|---|
| `consent.context` | Pessoa, representante/poder, finalidade, audiência, canal e prazo | F05/F08 | Representante/adulto titular e revisores designados; não outras famílias | R07 |
| `consent.decision` | Pendente, concedido, recusado, revogado ou expirado; instante e versão do termo | F05/F08 | Mesmos leitores; decisão é acrescentada com histórico, não sobrescrita | R07 |
| `media.piece_version` | Arquivo privado, versão, coleção e pessoas identificadas manualmente | F05 | Equipe de revisão; prévia a representantes sem exposição indevida de terceiros | R08 |
| `media.publication` | Peça/versão, aprovação editorial, canal, audiência e endereços administrados | F05/F08 | Equipe de mídia e audiência efetivamente autorizada | R08; evidência mínima R07 |
| `media.withdrawal` | Motivo mínimo, afetados, derivados, pedidos a canais e conclusão | F05/F08 | Solicitante e equipe de retirada; preservar terceiros | R07 |
| `privacy_request.record` | Protocolo, titular/representação, objeto, responsável, andamento e resposta | F08 | Solicitante e equipe de privacidade designada | R10 |
| `audit.metadata` | Ator, contexto, ação, referência de recurso, data e resultado | F06/F08 | Auditoria no escopo; sem conteúdo de saúde, senha, documento ou mensagem | R06 |
| `export.artifact` | Arquivo privado, finalidade, colunas, solicitante e expiração | Finalidade dos campos exportados; F06/F08 na evidência | Solicitante ainda autorizado; revalidar geração e download | R09; trilha mínima R06 |

Não guardar IP/geolocalização, impressão digital do dispositivo ou conteúdo de mensagem como prova obrigatória genérica. Se algum metadado de segurança/registro de acesso for necessário ou exigido, justificar necessidade, base, prazo e leitores em D06/D11 antes de habilitar; não confundir descarte do arquivo exportado com descarte da evidência da operação.

## 8. Retenção por categoria — proposta para D06/D11

Os números abaixo são **prazos operacionais propostos**, não prazos legais universais. Antes do uso real, a área responsável deve fixar versão, marco inicial, prazo, destino, exceções e fundamento. A proposta não autoriza descartar documento sujeito a obrigação de conservação, nem manter tudo indefinidamente porque falta uma decisão.

| Código | Categoria e marco de contagem | Proposta de prazo e destino | Revisão necessária |
|---|---|---|---|
| R01 | Solicitação incompleta/abandonada sem matrícula, a partir da última interação | 30 dias; aviso e eliminação do rascunho/dados sem outra finalidade válida | Secretaria/privacidade; solicitação em análise ativa tem prazo e responsável próprios |
| R02 | Cadastro e contatos, após terminar o último vínculo que justifique seu uso naquele contexto | Rever ao término e, no máximo, em 12 meses; eliminar contatos/endereço sem finalidade. Manter somente referências mínimas ainda justificadas | D06; vínculo em outra regional não perpetua os dados administrativos da anterior |
| R03 | Matrícula, lotação, presença e conteúdo pedagógico, após encerrar módulo/vínculo | Proposta de 24 meses para revisão do histórico nominal; depois eliminar/anonimizar o que não tenha finalidade ou conservação demonstrada | Coordenação/privacidade; decidir necessidade de histórico de conclusão e prazo próprio, antes de ativar |
| R04 | Orientação de cuidado, a partir do fim do atendimento ou substituição da orientação | Retirar da consulta operacional quando deixar de valer; proposta de descarte em até 30 dias após fim da finalidade | D06; incidente com conservação justificada fica segregado e com revisão própria |
| R05 | Evidências/documentos restritos | Não criar arquivo por padrão. Se necessário, prazo e obrigação por tipo; cópia transitória de conferência: proposta de até 30 dias após verificação, apenas se nenhuma guarda for exigida | D03/D06; certidões e termos podem exigir guarda diversa. Não aplicar 30 dias automaticamente a documento obrigatório |
| R06 | Sessão, token e trilha de segurança/auditoria | Token/sessão conforme expiração/revogação; trilha mínima: proposta de 12 meses, com prazo específico para registro de acesso legalmente exigido | D06/D11; conferir obrigações e separar segredos, diagnóstico e auditoria |
| R07 | Evidência mínima de ciência, representação, consentimento ou retirada, após cessar a finalidade | Proposta de 24 meses para revisão da prova mínima; prazo definitivo conforme necessidade e obrigação demonstradas | Privacidade; evidência não inclui conservar a peça inteira, dossiê ou ficha de saúde |
| R08 | Imagem e derivados | Material não liberado: proposta de 30 dias sem decisão, com descarte; publicado: prazo da finalidade/autorização, com revisão proposta ao fim do módulo e em até 12 meses | D09; revogação/expiração retira o acesso imediatamente, sem aguardar revisão periódica |
| R09 | Exportação pronta | Proposta de expiração em 24 horas, sem link público; nova geração exige nova autorização | D06/D11; dados baixados exigem orientação ao destinatário e procedimento de descarte |
| R10 | Protocolo de direitos, após resposta final | Proposta de 24 meses para revisão da evidência mínima; eliminar anexos excessivos antes | Privacidade; justificar eventual conservação e não reabrir tratamento original |

R01 será aplicado à solicitação antes de efetivar matrícula. Ao aprovar, os campos necessários passam a R02/R03 conforme sua finalidade; não duplicar o rascunho completo indefinidamente.

O descarte considera banco, busca, cache, arquivos, miniaturas, vídeos, exportações e cópias operacionais. Anonimização deve impedir associação razoável; trocar nome por identificador reversível é pseudonimização e continua exigindo proteção.

**Cópias de segurança:** proposta inicial de rotação de até 30 dias, para avaliação em D11, separada de RPO/RTO. Não usar backup para consulta cotidiana. Registrar eliminação/revogação para reaplicá-la antes de disponibilizar ambiente restaurado; a restauração não reativa publicação, conta ou vínculo revogado. Obrigações de conservação e exceções recebem finalidade, responsável e data de revisão; sem suspensão genérica e ilimitada do descarte.

## 9. Regras para implementação

1. Cada campo habilitado terá tipo, limite e validação no servidor, finalidade/base aprovadas, leitores, operadores de alteração e política de retenção versionada. Campo não aprovado permanece desativado.
2. Nas telas e respostas, selecionar apenas os campos necessários à operação. Nome e presença na chamada; instrução de retirada na saída; orientação mínima em tela de cuidado designada.
3. Dados sensíveis, comprovações e imagem ficam em recursos privados separados. Proteger também miniaturas e anexos; permissões de lista, busca, contagem, exportação e fila devem concordar.
4. Corrigir pelo fluxo adequado: adulto mantém próprio contato com verificação; família solicita correção cadastral; secretaria verifica nascimento/representação; professor corrige chamada com motivo, sem alterar guarda ou saúde.
5. Não pedir diagnóstico, CPF ou documento como forma automática de desempatar homônimos. Encaminhar revisão humana com informação mínima.
6. Controlador, contatos, fornecedores e compartilhamentos serão identificados por finalidade antes de publicar avisos. Administrador global é papel da aplicação; isso não define automaticamente quem é o controlador legal.
7. Aplicar retenção por contexto/finalidade, não pelo cargo do usuário. Não transportar arquivos privados para este repositório ou para a pasta sincronizada do código.

## 10. Casos documentais de validação

| Caso | Situação | Resultado esperado na implementação futura |
|---|---|---|
| DADO01 | Família tem só um responsável verificado | Cadastro válido, sem exigir segundo adulto fictício. |
| DADO02 | Criança não possui contato/login próprio | Matrícula pode seguir pelo representante e atendimento assistido. |
| DADO03 | Professor abre a chamada | Nome e presença; sem endereço, religião, documento ou bloco de saúde. |
| DADO04 | Responsável não informa orientação alimentar | Registrar não informado, sem marcar “sem alergia” nem recusar automaticamente a matrícula. |
| DADO05 | Operador de retirada confere criança | Ver instrução vigente; não dossiê de guarda nem dados de outros responsáveis. |
| DADO06 | Usuário tem cargo global e busca criança pelo nome | Negar leitura nominal sem atribuição aplicável. |
| DADO07 | Pessoa deixa uma regional e continua em outra | Encerrar acesso anterior e aplicar retenção daquele contexto, sem revelar o vínculo restante. |
| DADO08 | Revogação de imagem ocorre antes do prazo periódico de revisão | Retirar original/derivados/capa servidos, sem esperar o prazo; manter somente prova mínima justificada. |
| DADO09 | Restauração usa cópia anterior à revogação | Reaplicar revogação/descarte antes de liberar acesso. |
| DADO10 | Campo livre de evento tenta coletar diagnóstico sem finalidade aprovada | Não habilitar o campo nem incorporá-lo à ficha geral. |
| DADO11 | Pedido de eliminação encontra obrigação concreta de conservação | Segregar somente o necessário, registrar fundamento/prazo e responder ao titular; não alegar backup para uso indefinido. |
| DADO12 | Certidão da equipe chega ao marco de atualização | Encaminhar ao operador autorizado segundo regra aprovada; sem publicar conteúdo ou decidir aptidão automaticamente. |

Estes são resultados esperados, sem teste de servidor executado. D06 ainda precisa aprovar hipóteses e prazos por finalidade; a revisão documental não equivale a consentimento recebido de famílias ou à implantação da rotina de descarte.
