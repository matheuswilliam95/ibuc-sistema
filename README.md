# Sistema IBUC

Sistema em desenvolvimento para aproximar famílias, colaboradores e igrejas participantes do IBUC. O projeto prevê administrações regionais independentes, comunicação com confirmação de ciência, agenda, presença e acompanhamento pedagógico.

A aplicação apresenta a identidade visual em Laravel e três jornadas interativas: família confirma aviso por filho, professor fecha chamada e secretaria registra entrega parcial de material. Essas jornadas usam estado temporário no navegador. A T06 acrescenta cadastros de organização e currículo persistidos no PostgreSQL local, com dados fictícios; autorização de participantes e demais operações continuam nas próximas entregas.

![Identidade visual no computador](docs/identidade-visual/previa-inicial.jpg)

![Formulários com ícones](docs/identidade-visual/campos-com-icones.jpg)

## Executar a prévia

Requisitos: PHP 8.3 ou 8.4 com as extensões do Laravel, Composer 2, Node.js 22.12 ou superior e npm. A prévia não precisa de PostgreSQL ou Redis: seu ambiente de demonstração usa sessão/cache temporários e não envia os formulários.

Clone o repositório e instale as dependências:

```powershell
git clone https://github.com/matheuswilliam95/ibuc-sistema.git
Set-Location -LiteralPath ibuc-sistema/sistema
composer install
```

Em uma instalação nova, prepare a configuração local:

```powershell
Copy-Item -LiteralPath .env.example -Destination .env
php artisan key:generate
npm ci
npm run build
```

Se já houver um `.env` local, mantenha-o. Para esta prévia, o ambiente deve ser `APP_ENV=local`, com `SESSION_DRIVER=array`, `CACHE_STORE=array` e `QUEUE_CONNECTION=sync`, conforme o exemplo versionado.

## Abrir no navegador

Na pasta `sistema/`, com as dependências instaladas:

```powershell
php artisan serve --host=127.0.0.1 --port=8000 --no-reload
```

Abra [a prévia visual](http://127.0.0.1:8000/identidade-visual) ou [as três jornadas](http://127.0.0.1:8000/jornadas). Para alterar CSS ou JavaScript, execute `npm run build` e recarregue a página. Nas jornadas, use as abas Família, Professor e Secretaria; recarregar ou reiniciar descarta as simulações.

Esse endereço é local à máquina que executa o Laravel. O repositório compartilha o código, a documentação e as imagens; ele não hospeda o servidor PHP. As rotas de demonstração estão disponíveis nos ambientes `local` e `testing`.

Consulte [o guia da identidade](docs/identidade-visual/GUIA.md) para instalar as dependências, reutilizar componentes e orientar as próximas telas.

## Executar organização e turmas

A T06 possui oito formulários com validação no servidor, busca por regional/núcleo e proteção das relações no PostgreSQL. Ela exige um banco local dedicado e permanece desativada por padrão. As [instruções da T06](docs/organizacao/T06-ESTRUTURA.md) explicam os binários PostgreSQL 17.12 e o auxiliar para PowerShell 7.

Com as dependências da aplicação e os binários preparados, execute **da raiz do repositório**:

```powershell
pwsh -NoProfile -File ./sistema/scripts/local-postgres.ps1 -Action Prepare
pwsh -NoProfile -File ./sistema/scripts/local-postgres.ps1 -Action Serve
```

Abra [Organização e turmas](http://127.0.0.1:8001/estrutura). O auxiliar mantém banco, credenciais e dados fora do projeto/OneDrive, separa demonstração e testes e preserva o `.env` existente. Os cadastros pedagógicos ficam em rascunho. A tela é uma entrada temporária de desenvolvimento; a administração com usuários e permissões virá nas T07/T08.

## Seguir a implementação

O [roteiro de implementação](IMPLEMENTACAO.md) organiza as 46 entregas. A T04 tem base visual e três jornadas demonstráveis, com [roteiro de avaliação](docs/jornadas/ROTEIRO-DE-AVALIACAO.md); o aceite com participantes está pendente. As propostas T01–T03 estão prontas para avaliação: acesso, regras pedagógicas, dicionário de dados e autorizações. O usuário escolheu as faixas de 2–5, 6–8, 9–11 e 12–17 anos para o piloto; as demais decisões aplicáveis continuam identificadas. Os módulos de negócio e as verificações de autorização no servidor serão implementados na F1 e nas fases seguintes.

A T05 propõe o piloto para **100–300 alunos**, considerando o **servidor Hetzner já existente**, conforme orientação do usuário. O plano detalha ondas, suporte, ambientes, custos e recuperação; especificações do servidor, custodiantes, unidades reais e orçamento seguem pendentes. A T06 já implementa a estrutura local de regionais, núcleos, currículo, ofertas e turmas. O próximo trabalho é a T07, com atribuições, vigência, contexto autorizado e Policies. A implantação real continua pendente.

| Material | Conteúdo |
|---|---|
| [Aplicação Laravel](sistema) | Código da prévia e componentes compartilhados |
| [Guia visual](docs/identidade-visual/GUIA.md) | Cores, tipografia e uso dos componentes |
| [Jornadas interativas](docs/jornadas/ROTEIRO-DE-AVALIACAO.md) | T04: passos de avaliação, evidências e capturas de computador/celular |
| [Roteiro](IMPLEMENTACAO.md) | Fases, dependências e critérios de aceite |
| [Matriz de acesso](docs/governanca/matriz-de-acesso.md) | Proposta T01 de permissões, nomeação e substituição, com decisões pendentes |
| [Regras pedagógicas](docs/governanca/regras-pedagogicas.md) | Proposta T02 de currículo, idade, matrícula, remanejamento e faltas |
| [Dados e privacidade](docs/governanca/dados-e-privacidade.md) | T03: campos, finalidade, hipótese legal em análise, leitores e retenção |
| [Responsabilidade e autorizações](docs/governanca/responsabilidade-e-autorizacoes.md) | T03: representação, retirada, ciência, imagem e minutas por finalidade |
| [Fontes e decisões](docs/governanca/fontes-e-decisoes.md) | Evidências dos anexos, referências oficiais e situação das escolhas institucionais |
| [Plano do piloto](docs/piloto/PLANO-DO-PILOTO.md) | T05: público, núcleos/turmas fictícios, ondas, custódia, atendimento e critérios de expansão |
| [Arquitetura e custos](docs/infraestrutura/ARQUITETURA-E-CUSTOS.md) | T05: inventário Hetzner pendente, ambientes, capacidade e custos adicionais/total |
| [Recuperação e operação](docs/infraestrutura/RECUPERACAO-E-OPERACAO.md) | T05: metas propostas, cópias, alertas e roteiro de restauração a executar na T13 |
| [Organização e currículo](docs/organizacao/T06-ESTRUTURA.md) | T06: modelo PostgreSQL, oito cadastros locais, isolamento dos testes e evidências da interface |
| [Plano do sistema](<output/pdf/Plano de implantação do sistema IBUC.pdf>) | Planejamento funcional e técnico |
| [Backlog](<outputs/01a1da34-6809-7471-91a3-bee58d1ea8c0/Backlog de implantação IBUC.xlsx>) | Entregas e decisões acompanháveis |

## Verificar uma alteração

Execute dentro de `sistema/`:

```powershell
php artisan test
npm test
php vendor/bin/pint --test
npm run build
```

Para incluir os testes de integridade PostgreSQL da T06, execute da raiz `pwsh -NoProfile -File ./sistema/scripts/local-postgres.ps1 -Action Test`. Sem esse ambiente dedicado, os casos T06 são informados como ignorados; SQLite não valida essas restrições.

As instruções de desenvolvimento estão em [AGENTS.md](AGENTS.md). Credenciais, dependências, logs, banco de dados e arquivos gerados ficam fora do versionamento. A identidade e os materiais institucionais mantêm os direitos de seus respectivos titulares.
