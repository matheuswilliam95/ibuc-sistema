# Instruções do projeto IBUC

Para implementar ou retomar o sistema, abra [IMPLEMENTACAO.md](IMPLEMENTACAO.md). Leia as regras comuns, o registro de execução e a fase solicitada antes de modificar o projeto.

- Comece na primeira entrega elegível da fase. Preserve os IDs `T01`–`T46` e `D01`–`D14` do backlog. O pedido atual do usuário define até onde executar.
- Faça as mudanças e os testes da entrega; registre o resultado na seção **Registro de execução** do roteiro. Diferencie código verificado, decisão pendente e aceite da direção.
- Na ausência de uma decisão institucional, prepare a proposta com dados fictícios e avance no trabalho independente. Não habilite a função dependente com uma autorização presumida.
- Preserve o PDF e a planilha de referência. Nunca coloque cadastros reais, credenciais, documentos de crianças ou fotos de famílias no repositório, nos testes ou no histórico de execução.

Exemplo de início: **“Leia o roteiro e execute a F0, começando pela T01.”**

## Padrão visual das telas

Antes de criar ou alterar uma interface, leia [o guia visual](docs/identidade-visual/GUIA.md) e confira a página `/identidade-visual` da aplicação em `sistema/`. Reutilize os componentes Blade `x-ui.*` e os valores de `sistema/resources/css/tokens.css`. Ajustes comuns devem ser feitos nessa base, para alcançar todas as telas. A proposta permanece em avaliação; não declare aceite da direção com base na execução dos exemplos.

Use ícones nos campos por padrão, sempre que possível e viável. Os componentes `x-ui.input`, `x-ui.select` e `x-ui.textarea` já escolhem um ícone pelo tipo ou nome; use `icon` para ajustá-lo e `:icon="false"` nas exceções. Preserve rótulos visíveis e os controles nativos; não repita essa lógica em cada tela.
