# T04 — Avaliação das jornadas da comunidade

**Estado:** Em validação. **Entrega:** três jornadas interativas com dados fictícios, de 14/11/2026. A identidade visual e o entendimento pelos participantes ainda aguardam avaliação humana; esta entrega não registra aceite da direção ou piloto com famílias.

Abra [a prévia das jornadas](http://127.0.0.1:8000/jornadas). No menu da identidade visual também existe o link **Experimentar jornadas**. A aplicação deve estar iniciada conforme o [README](../../README.md).

## 1. Escopo da demonstração

Os perfis Família, Professor e Secretaria demonstram Bruno Exemplo, Ana Exemplo e Eva Exemplo. Os nomes e os núcleos são fictícios. A troca de perfil é um recurso de avaliação, não login, nomeação ou concessão real de permissões.

O estado existe somente na memória da página: não há banco, API de envio, cobrança, contato com participantes, push ou persistência em localStorage. Recarregar ou usar **Reiniciar os exemplos** recomeça as jornadas. Sem JavaScript os formulários permanecem desativados. As rotas de prévia só são registradas nos ambientes `local` e `testing`.

Reutilizam-se o layout, os componentes Blade, ícones, fonte, tokens e gradiente dos cabeçalhos existentes. Diálogos, retorno de foco e notificações foram extraídos para `resources/js/ui.js`, compartilhado com a página de identidade. Os textos de entrada mantêm 16 px; rótulos/botões, 14 px; apoio, ao menos 12 px; ações principais e opções da chamada, pelo menos 48 px.

As hipóteses da [T01](../governanca/matriz-de-acesso.md), da [T02](../governanca/regras-pedagogicas.md) e da [T03](../governanca/responsabilidade-e-autorizacoes.md) orientam o protótipo. Ciência por filho/versão é a proposta de D04, ainda não uma decisão institucional. Nenhuma regra de D05 sobre justificadas/alertas é ativada aqui.

## 2. Família confirma aviso

1. Abra **Família**, atuando como Bruno. Escolha Lucas Exemplo e leia o aviso de cancelamento de 17/11; a aula de 24/11 permanece indicada como mantida.
2. Clique em **Estou ciente por Lucas**. Confira filho, ator e versão na janela. Use **Voltar** ou Escape para sair sem confirmar, ou confirme a ciência.
3. Confira o recibo local e o resumo **1 de 2**, com Lucas confirmado e Lia pendente. Repetir a mesma manifestação não cria outra obrigação/recibo.
4. Selecione Lia. Seu aviso permanece pendente, com botão próprio. Ao confirmar, o resumo deve passar a **2 de 2**.

Perguntas para a família: fica claro a qual filho se refere a confirmação? A data cancelada se distingue da próxima aula? O texto “ciência” é compreensível? Consegue concluir sem orientação da equipe?

Capturas: [computador](familia-desktop.jpg) e [celular](familia-mobile.jpg).

## 3. Professor fecha chamada

1. Abra **Professor**. A lista mostra somente a turma Sementes do Núcleo Jardim, com Lia, Mateus e Sofia. Saúde, endereço, religião e documentos não aparecem.
2. Clique em **Conferir chamada** antes de marcar todos: deve aparecer uma mensagem, com foco no primeiro aluno pendente; nenhuma falta é presumida.
3. Marque Presente, Falta ou Justificada para cada aluno. **Todos presentes** é um atalho explícito, com possibilidade de ajustar cada marcação antes de fechar. Tab, espaço e setas operam os controles nativos.
4. Confira o resumo e feche a chamada. A tela preserva as marcações e mostra ator/horário local. Nesta prévia, o fechamento é somente consultável; correção com motivo será implementada na T21.
5. Selecione o encontro cancelado de 17/11. Não há lista editável nem fechamento; os totais dessa ocorrência são zero. Voltar para 24/11 preserva a chamada correspondente.

Perguntas para o professor: as três situações de presença são claras? Consegue identificar o aluno pendente? Entende o que será fechado e que o encontro cancelado não gera falta?

Capturas: [computador](professor-desktop.jpg) e [celular](professor-mobile.jpg).

## 4. Secretaria entrega material

1. Abra **Secretaria**. O pedido PED-001 contém duas apostilas destinadas a Lia, solicitadas por Bruno.
2. Informe quantidade maior que o saldo ou recebedor vazio e clique em **Conferir entrega**. Deve aparecer erro em texto, associado ao campo, sem modificar o pedido.
3. Informe uma unidade e Bruno Exemplo como recebedor. Confira item, beneficiário, recebedor e saldo previsto na janela antes de registrar.
4. Após registrar, o pedido mostra **Entrega parcial**, uma unidade entregue e uma pendente. O histórico identifica quantidade, recebedor, atendente e horário.
5. Registre a segunda unidade com nova conferência. O estado passa a **Entregue**, saldo zero e formulário substituído pela conclusão. Não é possível entregar uma terceira unidade pelo fluxo.

Reenvio do mesmo comando de confirmação não consome outra unidade. Uma conferência antiga, cujo saldo já foi alterado por outra entrega, exige revisão. São comportamentos locais do exemplo; transação e concorrência reais serão implementadas no servidor na T24.

Perguntas para a secretaria: distingue solicitado, entregue e pendente? A janela permite conferir antes de concluir? O histórico é suficiente para entender quem recebeu? Fica claro que retirada não é pagamento?

Capturas: [computador](secretaria-desktop.jpg) e [celular](secretaria-mobile.jpg).

## 5. Evidências verificadas nesta execução

| Verificação | Resultado observado |
|---|---|
| Testes PHP | 4 testes, 14 asserções, incluindo renderização das jornadas, controles desativados antes de JavaScript e ausência de endpoint POST. |
| Testes das interações | 10 testes Node: navegação/âncora de conteúdo, ciência por filho/versão, repetição, chamada incompleta/cancelada/fechada, marcação inválida, entrega parcial/repetida, validação e saldo alterado. |
| Compilação e estilo | Vite, compilação Blade e Pint concluídos; `git diff --check` sem erros. |
| Rota em produção | `php artisan route:list --env=production --path=jornadas --json` retornou `[]`. |
| Computador | Fluxos percorridos em 1440 × 1000; largura do conteúdo igual à largura disponível (1425 px), sem rolagem horizontal na medição. |
| Telas menores | Três jornadas medidas em 320, 390 e 768 px: largura do conteúdo igual à disponível em todos os casos. Textos visíveis medidos com mínimo de 12 px. |
| Fluxos no navegador | Ciência de Lucas sem satisfazer Lia; chamada incompleta impedida; cancelamento sem chamada; fechamento com justificadas à parte; entrega inválida recusada, parcial e completa verificadas. |
| Teclado e foco | Seleção de presença com espaço/setas, erro com foco no campo, diálogos, Escape e retorno ao menu conferidos. Link “Ir para o conteúdo” preserva o perfil ativo. |
| Biblioteca compartilhada | Busca “campos”, diálogo e ciência da identidade visual conferidos após extração das interações comuns; sem erro/aviso de console na inspeção. |
| Reinício | Confirmações, chamada e entregas retornam ao cenário inicial. A preferência de perfil não transforma o exemplo em outra conta real. |

Os testes JavaScript verificam a lógica local, não autorização, unicidade ou transação no banco. A ficha real, a atribuição e o vínculo familiar ainda serão protegidos pelo servidor nas entregas da F1. Não houve teste em Android físico, leitor de tela real, conexão limitada ou texto ampliado a 200%; esses ensaios continuam pendentes.

## 6. Registro de avaliação humana

Para cada sessão, registrar data, perfil avaliado, aparelho/navegador, tarefa concluída sem ajuda, dificuldade observada e ajuste solicitado. Usar identificadores fictícios no repositório; nomes/contatos dos participantes ficam no canal institucional apropriado.

- [x] Três jornadas interativas preparadas e verificadas com exemplos.
- [x] Capturas de computador/celular e casos de erro disponíveis.
- [ ] Família percorreu a confirmação e distinguiu os irmãos.
- [ ] Professor percorreu marcação e fechamento da chamada.
- [ ] Secretaria percorreu entrega parcial e total.
- [ ] Marca, compreensão, acessibilidade e ajustes receberam avaliação da direção/equipe.
- [ ] D04 e demais decisões aplicáveis validadas para o fluxo real.

**Continuidade:** a [T05 — proposta do piloto](../piloto/PLANO-DO-PILOTO.md) já está preparada para avaliação, considerando 100–300 alunos e o servidor Hetzner existente informado pelo usuário. A próxima entrega técnica independente é a T06, estrutura organizacional e pedagógica com dados fictícios. O aceite destas jornadas com participantes e as decisões institucionais continuam pendentes. O [registro de execução](../../IMPLEMENTACAO.md) mantém esse estado; a F0 não está aceita por haver código demonstrável.
