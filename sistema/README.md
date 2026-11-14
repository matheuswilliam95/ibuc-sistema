# Aplicação Laravel do IBUC

A instalação, a execução da prévia e as capturas da interface estão no [README do projeto](../README.md).

Consulte o [guia visual](../docs/identidade-visual/GUIA.md) antes de criar telas e o [roteiro de implementação](../IMPLEMENTACAO.md) para seguir as próximas fases.

Esta aplicação usa Laravel 13 e componentes Blade compartilhados. As prévias de identidade e jornadas funcionam sem banco e mantêm estado temporário. A [estrutura organizacional da T06](../docs/organizacao/T06-ESTRUTURA.md) usa PostgreSQL local isolado e persiste cadastros fictícios em rascunho; os testes de integridade têm base própria. A administração com acesso de participantes ainda depende das T07/T08.
