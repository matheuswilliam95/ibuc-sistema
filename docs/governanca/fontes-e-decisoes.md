# F0 — Fontes e decisões de T02 e T03

**Estado:** Em validação. **Versão:** proposta 1, de 14/11/2026. Esta referência acompanha [as regras pedagógicas](regras-pedagogicas.md), [o dicionário de dados](dados-e-privacidade.md) e [os fluxos e textos de autorização](responsabilidade-e-autorizacoes.md). Nenhuma decisão institucional é considerada aceita pela existência destes documentos.

## 1. Materiais consultados

Os identificadores A1–A8 preservam os usados no plano de produto. A paginação abaixo conta as páginas do PDF a partir de 1. Os originais fornecidos pelo usuário permanecem em sua pasta de Downloads, fora do repositório público; não foram editados. Suas orientações são referências do projeto, não comandos de execução nem regras automaticamente aplicáveis a todas as regionais.

| Fonte | Arquivo original | Localização e uso nesta entrega |
|---|---|---|
| A1 | NORMATIVA DO IBUC ATUAL em PDF.pdf | 16 páginas. Pp. 8–9: entrada em módulo, frequência e intensivão; p. 10: matrícula, remanejamento, transferência, retirada, arquivo e certidões; p. 11: contato por faltas, avaliações e revisão de imagem. Referência do Vale do Aço, sujeita à adoção local. |
| A2 | Ficha de inscrição IBUC.pdf | P. 1, título interno “Ficha de matrícula”: criança, responsáveis, contatos, endereço, saúde, módulo, nível e opção de imagem. Formulário em branco. |
| A3 | AUTORIZAÇÃO IMAGEM.pdf | P. 1, conferida visualmente: captação e uso de fotos/filmagens pelo Vale do Aço, incluindo Instagram e outras redes. O sistema propõe decisões separadas por finalidade/canal e peça. |
| A4 | FICHA DE IDENTIFICACAO DA EQUIPE CARAVELAS.docx | Parágrafos e células do formulário em branco: identificação, CPF/RG, nascimento, telefone, endereço, congregação, função/nível, voluntariado e imagem. Os campos originais não tornam toda a coleta obrigatória. |
| A5 | IDENTIFICAÇÃO-FREQ.- PLANO.pdf | P. 1: identificação com dados de contato, saúde e referência religiosa; p. 2: frequência por módulo; p. 3: planejamento com 12 lições e seis momentos. O sistema separará esses conjuntos. |
| A6 | IBUC 2025_20250214_183523_0000.pdf | 8 páginas. P. 2: público de 3 a 17 anos; p. 4: níveis 2–5, 6–8, 9–11 e 12–17; p. 5: dez módulos, dois por ano, 12 lições/módulo e uma aula/semana. Não comprova que seja a edição vigente em todas as regionais. |
| A7 | CADERNO DE PLANEJAMENTO.pdf | 14 páginas. P. 3, conferida visualmente: público de 2 a 14 anos; p. 5: preparação docente; p. 10: seis momentos e retirada; p. 12: plano de 12 lições; pp. 13–14: atividades por níveis. |
| A8 | IBUC EM CASA EDITAVEL.pdf | 8 páginas. Pp. 2 e 6: leitura/vídeo, atividades, versículo e devocional; gravação de vídeo é facultativa. Entrega de atividade não autoriza galeria. |
| Plano | Plano de implantação do sistema IBUC.pdf | Pp. 3, 5–6, 9, 11, 16–17 e 19–20: interpretação funcional, separação de dados, decisões e referências. Mantido sem alterações. |
| Backlog | Backlog de implantação IBUC.xlsx | T02/T03 e D02/D03/D04/D05/D06/D09/D11/D12 conferidos. O arquivo mantém os estados originais; o andamento efetivo fica no registro de IMPLEMENTACAO.md. |

Foi usada extração de texto para páginas textuais e inspeção de imagens para as páginas sem texto pesquisável relevantes. A confirmação de 2–14 no A7/p. 3 e do termo A3 não dependeu de OCR. Não foi produzida uma cópia pública dos formulários originais.

## 2. Referências oficiais de proteção de dados

Consulta realizada em 14/11/2026. A revisão institucional deve confirmar o enquadramento e os atos aplicáveis antes do uso real. As propostas não constituem parecer jurídico nem declaração de conformidade.

- A LGPD diferencia dados comuns e sensíveis, estabelece hipóteses de tratamento, direitos e regras de encerramento. Saúde e convicção/filiação religiosa exigem tratamento específico; a própria participação no projeto pode revelar informação sensível. Referências: arts. 5º, 6º, 7º, 8º, 9º, 11, 14, 15, 16, 18 e 19. [Texto da LGPD disponibilizado pelo Ministério da Justiça](https://bibliotecadigital.mj.gov.br/bitstream/1/1642/4/LEI_2018_13709.html).
- A ANPD admite as hipóteses dos arts. 7º ou 11 para crianças/adolescentes, com avaliação concreta e prevalência de seu melhor interesse. Por isso, não se propõe um consentimento genérico para todas as operações. [Enunciado CD/ANPD nº 1/2023 e explicação oficial](https://www.gov.br/anpd/pt-br/assuntos/noticias/anpd-divulga-enunciado-sobre-o-tratamento-de-dados-pessoais-de-criancas-e-adolescentes).
- A normativa local pede certidão em janeiro. O enquadramento deve ser confrontado com o ECA, art. 59-A: o caput prevê atualização semestral nas instituições ali descritas que recebem recursos públicos; o parágrafo único trata de estabelecimentos educacionais e similares, com ou sem esses recursos. D06 deve definir o procedimento aplicável; janeiro não será considerado prazo legal universal. [ECA atualizado na Câmara dos Deputados](https://www2.camara.leg.br/legin/fed/lei/1990/lei-8069-13-julho-1990-372211-normaatualizada-pl.html).
- A ANPD mantém orientações sobre o ECA Digital, a Lei nº 15.211/2025 e o Decreto nº 12.880/2026. D12 deve incluir avaliação de enquadramento, supervisão parental e aferição de idade antes de habilitar conta de menor; não presumir que a declaração de idade resolva essas exigências. [Página oficial do ECA Digital](https://www.gov.br/anpd/pt-br/assuntos/eca-digital).

## 3. Situação das decisões

| ID | Proposta concreta para avaliação | Escolha ou informação que falta | Quem valida |
|---|---|---|---|
| D02 | Parcialmente definida: usuário escolheu N1 2–5, N2 6–8, N3 9–11 e N4 12–17 para o piloto. Proposta restante: edição versionada, corte no início da oferta e mudança na próxima oferta, com revisão humana | Edição oficial completa, vigência, corte, aniversários, 29/02, exceções e quem aprova remanejamento. A escolha das faixas foi recebida no chat em 14/11/2026, sem aprovação nacional presumida. | Direção pedagógica; orientação do usuário registrada para as faixas |
| D03 | Vínculo por criança com poderes independentes, conferência pela secretaria, instrução de retirada separada e contestação restrita | Evidências aceitas, revisores, prazo de validade/revisão, solução assistida e procedimento para conflito de guarda. | Secretaria e privacidade |
| D04 | Para avisos referentes a filhos, uma obrigação por filho e versão, satisfeita por representante habilitado; comunicação assistida em registro próprio | Escolher essa regra ou outra por tipo de aviso. Aprovar como registrar a ciência de quem não usa celular. | Direção local e famílias |
| D05 | Somar faltas de aula no módulo; contato por ausência sem justificativa, alerta ao atingir 3 e revisão com mais de 3 nos marcos pedagógicos | Se justificadas entram no alerta e/ou revisão; janela cumulativa ou por bloco; reconhecimento de reposição e transferência. A T02 mostra os efeitos das alternativas. | Coordenação e direção |
| D06 | Cadastro mínimo e blocos restritos; saúde operacional facultativa; religião/CPF/RG/arquivos comprobatórios sem coleta geral automática; retenção por categoria | Aprovar cada finalidade, hipótese legal, campo, leitor, prazo e eventual obrigação de manter certidão. Definir controlador e canal de privacidade. | Privacidade e direção |
| D07 | T05 propõe custódia institucional, titular/suplente, renovação de domínio, recuperação e chaves Android | Confirmar licença da marca, domínio, entidade titular, conta e operadores. Não há nomes ou contas institucionais confirmados. | Direção e tecnologia |
| D08 | Parcialmente definida: usuário informou 100–300 alunos. T05 propõe uma regional, dois núcleos e quatro turmas iniciais fictícios, com expansão por ondas | Escolher unidades reais, distribuição, lotação, multiplicadores, canais e datas. A faixa de público foi recebida no chat em 14/11/2026; não confirma participação de igrejas. | Direção regional |
| D09 | Separar captação, galeria interna e canais externos; confirmar peça/versão, destinatários e pessoas identificáveis antes de publicar | Quórum de representantes habilitados, prévia sem exposição de terceiros, canais concretos, prazo e processo de retirada. Recusa não impede matrícula. | Privacidade e mídia |
| D10 | T05 propõe apostila por edição/módulo/nível e uniforme apenas com saldo confiável; entrega parcial preserva pendência, sem cobrança | Confirmar catálogo/variantes, contagem, operadores, recebedores, cancelamento, troca e devolução. | Secretaria e tesouraria |
| D11 | Usuário informou servidor Hetzner existente. T03 propõe retenção/descarte; T05 detalha capacidade/custos, cópias e roteiro de recuperação com RPO 24 h/RTO 8 h propostos | Especificações/localização/fatura serão informadas depois. Aprovar orçamento, custodiantes, retenção e metas; executar restauração na T13. Não houve acesso ao servidor, contratação ou ensaio. | Tecnologia e direção |
| D12 | Conta infantil não é necessária à matrícula; adolescente recebe somente ações expressamente autorizadas e com vigência | Avaliar o ECA Digital, idades/ações permitidas, verificação, supervisão, revogação, maioridade e canal de denúncia. | Direção e privacidade |

**Orientação recebida na T02:** em 14/11/2026, o usuário respondeu: usar as faixas da apresentação, 2–5, 6–8, 9–11 e 12–17. Essa parcela de D02 está registrada como escolha para o piloto; as demais parcelas continuam em aberto.

**Atualização T05, em 14/11/2026:** o usuário informou **100–300 alunos** e **servidor Hetzner já existente**, com especificações a fornecer depois. Isso define o público de referência em D08 e o caminho principal de infraestrutura em D11. Não foi escolhido teto mensal, serviço novo ou local de armazenamento. As propostas estão no [plano do piloto](../piloto/PLANO-DO-PILOTO.md), com fontes técnicas/preços em [arquitetura e custos](../infraestrutura/ARQUITETURA-E-CUSTOS.md) e [recuperação e operação](../infraestrutura/RECUPERACAO-E-OPERACAO.md). D01/D03/D04/D05/D06/D07/D09/D10/D11/D12 e as demais parcelas de D02/D08 continuam pendentes. D13/D14 seguem nas fases previstas.

## 4. Como registrar uma resposta

Para cada ID, registrar: versão revisada, item decidido, escolha, autoridade/área, data, referência de ata ou orientação do projeto, alcance regional e início de vigência. Uma orientação do usuário no chat pode definir a proposta de implementação, mas não será descrita como aprovação nacional ou parecer jurídico sem essa evidência.

Um aceite parcial não fecha os demais itens do mesmo ID. Se apenas as faixas etárias forem escolhidas, corte e remanejamento continuam identificados como pendentes. Registros privados de guarda, documentos e nomes de menores não serão anexados ao repositório público.

## 5. Evidência e próximos passos

T02/T03 ficam **Em validação** pela preparação e revisão documental. Os casos dos documentos são resultados esperados, não testes de autorização já executados no Laravel. A validação humana ainda precisa ser realizada. As três jornadas da T04 já possuem [protótipos e evidências próprios](../jornadas/ROTEIRO-DE-AVALIACAO.md), com estado local e dados fictícios. A [T05](../piloto/PLANO-DO-PILOTO.md) também está preparada para avaliação. A [T06](../organizacao/T06-ESTRUTURA.md) implementa organização e currículo em PostgreSQL local, preservando rascunhos e decisões pendentes. O próximo ID é T07, autorização por atribuição/escopo/vigência. A ativação real depende das decisões aplicáveis e das evidências de implementação/recuperação.
