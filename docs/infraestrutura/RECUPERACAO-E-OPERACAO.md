# T05 — Procedimento proposto de recuperação e operação

**Estado:** Em validação. **Versão:** proposta 1, de 14/11/2026. **Execução:** nenhum backup, restauração, monitoramento ou acesso ao servidor Hetzner foi realizado nesta entrega. O ensaio será executado na T13, em ambiente isolado com dados fictícios, depois da implementação necessária.

Este procedimento complementa [o plano do piloto](../piloto/PLANO-DO-PILOTO.md) e [a arquitetura e os custos](ARQUITETURA-E-CUSTOS.md). Tecnologia e direção decidem as metas da D11; privacidade valida retenção, finalidades e tratamento dos registros. Não há plantão, SLA, perda ou recuperação observada sendo declarados.

## 1. Metas para pactuação em D11

| Medida | Proposta inicial | Como conferir |
|---|---|---|
| RPO — quanto pode ser perdido | Até 24 horas de registros operacionais | Diferença entre o ponto íntegro recuperável e o intervalo do incidente; não usar só o horário de término de um arquivo de backup |
| RTO — tempo para recuperar o serviço autorizado | Até 8 horas da declaração do incidente à liberação validada | Registrar também a detecção e a indisponibilidade total; cobertura da equipe precisa ser compatível com a meta |
| Cópia consistente do banco | A cada 12 h, proposta de 03h e 15h em America/Sao_Paulo | Duas oportunidades por dia dão margem para detectar falha antes de superar o RPO; sucesso exige cópia remota e manifesto conferidos |
| Arquivos e versões necessários ao ponto de recuperação | Copiar junto ao conjunto do banco e manifestar versões/chaves imutáveis referenciadas | A cópia só conta como recuperável se possuir os objetos que os registros do banco referenciam |
| Retenção de conjuntos recuperáveis | Proposta de 30 dias, conforme D06/D11 e T03 | Incluir consumo de versões/snapshots; expirar com registro e considerar exceções justificadas |
| Verificação de restauração | Antes do piloto, mensal durante ele e após mudança relevante | Registrar resultado, tempos e pendências; ler um arquivo ou listar cópias não substitui restaurar |

São propostas, não configurações já ativadas. Um backup íntegro de mais de 24 h deve gerar incidente operacional de backup e impedir declarar o RPO atendido. Se os ensaios ou a cobertura humana não sustentarem as metas, revisar D11 e o desenho/custo antes de prometer esse atendimento.

RPO de 24 h não autoriza reativar vínculo, imagem ou permissão revogados nesse intervalo. Na dúvida sobre a atualidade dessas decisões, manter as ações dependentes bloqueadas até reconciliar/revalidar, conforme a seção 4.

## 2. O que compõe uma cópia recuperável

| Componente | Conteúdo e proteção | Critério de integridade |
|---|---|---|
| Banco PostgreSQL | Exportação consistente validada, esquema/dados e identificação da versão da aplicação/regra | Ferramenta compatível, conclusão sem erro, checksum, tamanho plausível e restauração de teste |
| Objetos privados | Arquivos/versões referenciados pelo banco, inclusive os derivados que devem existir naquele ponto | Manifesto por chave/versão e checksum; original sem miniatura é tratado conforme o recurso, não como cópia completa automaticamente |
| Configuração de implantação | Commit/release, dependências fixadas, migrações e configuração operacional necessária | Relação suficiente para reconstruir o ambiente; sem despejar segredos em documentação pública |
| Chaves e credenciais de recuperação | Referências e custódia separada em cofre institucional; versão da APP_KEY necessária à leitura de dados cifrados | Custodiante e suplente conseguem recuperar a chave pelo procedimento aprovado; não compartilhar valor no GitHub ou log |
| Controle de revogação e descarte | Registros necessários para não reapresentar dados/imagens/poderes retirados | Recuperar o controle mais recente disponível e conferir sua cobertura; ausência de prova exige restrição/revalidação |
| Manifesto do conjunto | Identificador, ambiente, instante do ponto de dados, início/fim, commit, arquivos, checksums e resultado | Conjunto identificável, cifrado e confirmado no destino; alerta se banco e objetos não corresponderem |

O PostgreSQL documenta que `pg_dump` produz exportação consistente mesmo com atividade concorrente. Isso não torna o bucket automaticamente consistente com o mesmo ponto: a T13 deve coordenar exportação, manifesto de objetos e retenção temporária das versões necessárias, ou usar uma janela controlada. Também é preciso registrar extensões e perfis de acesso necessários à restauração. [Referência oficial do pg_dump](https://www.postgresql.org/docs/current/app-pgdump.html).

Cópias privadas ficam cifradas no destino externo ao servidor de aplicação, com credencial de rotina limitada e custódia de recuperação independente. Segregar o acesso administrativo que permite apagar conjuntos/versões; testar essas restrições. A aplicação não deve deter a credencial mestre do backup. Não considerar o mesmo diretório em outro disco do servidor como proteção contra perda do host.

**Limites do fornecedor a considerar:** no Hetzner Cloud, backups/snapshots cobrem o disco da instância e não volumes anexados; há sete slots de backup. Eles são recurso complementar, não prova de cópia lógica do PostgreSQL ou cobertura do Object Storage. O tipo do servidor existente ainda é desconhecido. [Backups e snapshots do Hetzner Cloud](https://docs.hetzner.com/cloud/servers/backups-snapshots/overview/).

Snapshots de Storage Box ficam na própria Storage Box e usam sua capacidade; não serão contados como cópia em outro fornecedor ou outro domínio de falha. A localização e as proteções do destino precisam de decisão própria. [Descrição oficial da Storage Box](https://docs.hetzner.com/storage/storage-box/general/).

## 3. Rotina e alertas propostos

| Rotina | Quando | Responsável a nomear e ação |
|---|---|---|
| Confirmar conjunto recuperável externo | Após cada execução | Operador de backup confere manifesto, acesso do custodiante e sinais de falha; não basta retorno de processo sem erro |
| Idade do último ponto íntegro | Acompanhamento contínuo a implementar | Tecnologia: aviso proposto acima de 18 h, incidente acima de 24 h; investigar causa e executar cópia segura adicional |
| HTTP, banco, fila e armazenamento | Acompanhamento funcional a implementar | Tecnologia verifica jornada sintética sem dados pessoais, conexão ao banco, tarefa de fila e leitura privada; `/up` isolado não prova a saúde de todos os componentes |
| Disco, memória, CPU e filas | Métricas e revisão no horário dos encontros | Operador acompanha recursos do IBUC e impacto nos outros serviços; agir antes de esgotar disco ou acumular envio |
| Capacidade/custo | Revisão semanal no piloto e antes de nova onda | Custodiante e direção comparam fatura/consumo, quotas, tamanho de backup e orçamento escolhido |
| Acessos de equipe e custodiantes | Entrada, saída, substituição e revisão periódica | Área nomeante revoga o que encerrou, incluindo console/DNS/backup quando aplicável; acesso técnico não é herdado do papel do aplicativo |
| Atualizações e releases | Janela acordada | Tecnologia valida dependências, cópia recuperável, migração e retorno; registrar commit e resultado |
| Exercício de recuperação | Antes do piloto, mensal e após mudança relevante | Operador de recuperação restaura em destino isolado; secretaria/coordenação verificam os resultados operacionais |

Destinatários, telefone/canal, cobertura e escalonamento ainda serão definidos. Não enviar conteúdo de ficha, nome de criança, senha, token ou documento em alerta. Métricas identificam ambiente, componente, horário, erro técnico mínimo e protocolo.

## 4. Recuperação, passo a passo

1. **Declarar e delimitar.** Identificar responsável, hora de detecção/declaração, ambientes atingidos e último ponto íntegro conhecido. Distinguir falha de máquina, erro de release, exclusão acidental e possível comprometimento. Acionar tecnologia e privacidade quando pertinente, com registro mínimo.
2. **Proteger a operação.** Suspender escrita e envios afetados; usar o atendimento alternativo do plano. Preservar evidências necessárias. Não restaurar sobre outro serviço ou sobre o banco ativo sem conferir destino e janela; no ensaio, restaurar apenas em ambiente descartável isolado.
3. **Selecionar o conjunto.** Conferir manifesto, checksums, versões de aplicação/banco, objetos e dependências. Registrar o ponto de dados e o intervalo potencialmente perdido. Se faltar objeto necessário ou chave de leitura, o conjunto ainda não é recuperável.
4. **Reconstruir em isolamento.** Preparar infraestrutura e perfis de acesso do ambiente de recuperação, recuperar a versão de configuração/chaves apropriada e restaurar banco e objetos. E-mail, push, callbacks e tarefas operacionais ficam interceptados; não alcançar participantes reais a partir do ensaio.
5. **Conferir integridade.** Comparar contagens por regional/núcleo/turma, referências de matrículas, chamadas, recibos, quantidades e arquivos. Abrir amostras autorizadas e testar negações para outra unidade, ex-professor e representante sem poder.
6. **Reconciliar o período perdido.** Secretaria compara atendimento assistido, ciência, presença e entregas com a base recuperada. Nenhum item perdido vira falta presumida; pedido entregue não volta automaticamente a disponível para nova retirada. Registrar divergências e responsáveis pela solução.
7. **Reaplicar revogações e descartes.** Usar o controle mais recente e evidência de sua cobertura para reaplicar vínculos encerrados, retirada de imagens e eliminação. Se o intervalo posterior ao backup não puder ser conferido, bloquear as ações/dados dependentes e revalidar com a área competente. Não reabrir todos os acessos só porque o sistema iniciou.
8. **Revalidar sessões, filas e mensagens.** Invalidar sessões conforme mecanismo da F1, revisar tarefas/outbox e revalidar público/vínculos antes de reprocessar. A chave de idempotência sozinha não prova uma entrega perdida no intervalo do backup. Não trocar APP_KEY cegamente: perder a chave necessária pode tornar dados cifrados ilegíveis; eventual rotação exige procedimento próprio.
9. **Liberar por evidência.** Tecnologia, responsável operacional e direção indicada registram fluxos liberados, restrições restantes, tempos e decisão. Só habilitar canais externos após conferência de versão, audiência e estado. Durante o ensaio, não apontar DNS nem conectar famílias ao ambiente restaurado.
10. **Revisar o incidente/ensaio.** Documentar perda observada, recuperação, impacto e ação corretiva. Privacidade avalia as comunicações aplicáveis quando houver incidente real. Atualizar o procedimento e repetir o que falhou; não registrar uma simulação como incidente real resolvido.

Se houver suspeita de comprometimento, reconstruir em ambiente confiável e avaliar credenciais/imagens de sistema antes de reutilizá-las. Preservação de evidências e restauração não justificam leitura nominal irrestrita pela equipe técnica.

## 5. Retorno de release

Falha de código sem perda de dados pode permitir voltar para release compatível, após conferir esquema e tarefas pendentes. Migração que descarta/transforma dados não recebe a suposição de que voltar arquivos ou rodar uma reversão genérica resolverá o problema.

Na F1, documentar cada migração relevante com pré-condições, cópia, compatibilidade de aplicação e procedimento de recuperação. Se o retorno exigir perder operações posteriores, tratar como restauração e reconciliar o intervalo, com decisão explícita. Manter o histórico e não ajustar saldo/ciência silenciosamente para fazer o ensaio passar.

## 6. Ensaio da T13, com dados fictícios

| Caso | Preparação e ação | Resultado esperado |
|---|---|---|
| REC01 | Duas regionais, dois núcleos, turmas homônimas e vínculos do cenário T01 | Contagens e relações restauradas sem mistura de escopos |
| REC02 | Criar conjunto com banco e objetos, depois restaurar em destino isolado | Dados e arquivos correspondem ao mesmo ponto documentado; todos os canais externos interceptados |
| REC03 | Criar referência de objeto ausente ou checksum divergente | Reprovar integridade; não declarar o conjunto recuperável ou liberar leitura nominal |
| REC04 | Revogar professor e autorização de imagem depois do ponto de backup | Reaplicar controle atualizado e negar vínculo/arquivo; sem cobertura confiável, manter restrição e encaminhar revalidação |
| REC05 | Registrar entrega parcial e atendimento assistido no intervalo perdido | Reconciliar antes de reabrir retirada/chamada/ciência; repetição não gera segundo fato |
| REC06 | Recuperar com chave/versão inadequada em cenário de teste | Detectar falha de leitura e seguir a custódia apropriada, sem gerar nova chave para ocultar o problema |
| REC07 | Expirar a atribuição do operador com fila pendente | Revalidar antes de executar; destinatário e ação não usam permissão antiga |
| REC08 | Simular falha de cópia e ponto íntegro com mais de 24 h | Alerta/incidente e RPO não atendido, mesmo se o arquivo mais recente existir |
| REC09 | Servidor Cloud usa volume anexado ou objetos externos | Inventário revela o que o snapshot não contém; exigir cópia independente dos componentes faltantes |
| REC10 | Usar somente credencial da aplicação para tentar administrar o backup | Negar acesso ao destino administrativo/conjuntos protegidos conforme política definida; registrar o resultado |

### Registro do exercício

| Campo | Situação nesta entrega |
|---|---|
| Execução, participantes e autorização | Não executados; titulares a definir |
| Ambiente/commit/ponto restaurado | A registrar no exercício |
| Detecção, declaração, início e fim | A medir; nenhuma duração observada |
| Perda/RPO observado e comparação com meta | Não medido |
| RTO observado e indisponibilidade total | Não medidos |
| Contagens, checksums, negações e revogações | A verificar após implementação |
| Reconciliado, restrito e ainda pendente | A registrar por fluxo |
| Decisão de liberação e próximos ajustes | Não emitida |

Guardar evidências técnicas privadas no local institucional apropriado e publicar somente um resumo sem dados pessoais/segredos. Enquanto o exercício não tiver sido feito com sucesso, a recuperação continua proposta. O próximo trabalho independente é a T06 em ambiente de desenvolvimento/testes, sem necessidade de acessar o Hetzner nesta etapa.
