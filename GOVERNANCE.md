# Governança de conteúdo

## Finalidade e limites

Este repositório é um projeto comunitário independente que reúne referências selecionadas em português brasileiro para desenvolvimento PHP. Complementa o [Manual oficial do PHP](https://www.php.net/manual/pt_BR/), mas não o substitui, não é uma tradução integral e não é fonte normativa para linguagem, runtime ou segurança. A existência de um guia, teste, link ou estado `coberto` na matriz não certifica a qualidade técnica nem a segurança de uma aplicação.

A referência oficial de tradução para pt_BR é o repositório [`php/doc-pt_br`](https://github.com/php/doc-pt_br). Para decisões de uso real, devem ser conferidas também as fontes oficiais em inglês quando a tradução estiver incompleta, além da documentação do runtime, framework, bibliotecas e versão efetivamente utilizadas.

## Papéis e revisão

O autor da contribuição deve indicar o problema, o escopo, as fontes primárias consultadas, as versões afetadas e os comandos de validação executados. O autor também deve distinguir orientação geral de decisões que dependem de arquitetura, configuração ou ameaça.

A política do projeto é solicitar revisão técnica por outra pessoa mantenedora antes de integrar alterações de conteúdo. Temas que possam mudar a postura de segurança devem receber ainda revisão de segurança, com análise de pressupostos, fronteiras de confiança, cenários de abuso e limitações. O revisor deve conferir as fontes primárias e confirmar que o texto não transforma recomendações contextuais em garantias universais.

Esta política documental não afirma que o GitHub imponha aprovações: a proteção da branch é configurada fora dos arquivos do repositório. Uma alteração a esta política ou ao CI não modifica, por si só, as regras de proteção da branch.

## Versões e ciclo de atualização

O baseline de compatibilidade continua a ser PHP 8.2, com a matriz de CI em 8.2, 8.3, 8.4 e 8.5. Este documento não altera esse baseline. Recursos que exijam versões posteriores devem indicar `<!-- php-min: X.Y -->` junto ao bloco relevante e não devem ser executados em runtimes incompatíveis.

Quando uma versão, função, diretiva ou comportamento for relevante, o autor deve citar documentação primária, indicar a versão mínima e evitar afirmações que não tenham sido conferidas. Mudanças de baseline exigem uma proposta e revisão separadas. Versões de dependências e serviços não devem ser presumidas a partir de um exemplo genérico.

## Fontes e matriz de cobertura

A [matriz de cobertura](docs/MATRIZ-COBERTURA.md) registra as seções imediatas encontradas no índice do Manual PHP pt_BR, o estado editorial atribuído, as referências locais relacionadas, a fundamentação, a data da revisão e os hashes das referências.

Ao editar um arquivo em `references/`, a mesma contribuição deve rever os mapeamentos afetados. Atualizar somente o hash não basta: o autor deve reavaliar se a relação temática e o estado ainda são adequados. Toda nova referência deve ser associada a pelo menos uma seção pertinente da matriz antes de passar a validação; o verificador rejeita arquivos sem mapeamento.

Os estados significam apenas:

- `coberto`: a referência local trata substantivamente do foco central da seção oficial, sem alegar equivalência integral;
- `parcial`: existe correspondência, mas o tratamento é limitado ou seletivo;
- `ausente`: não foi encontrada cobertura local substantiva;
- `desatualizado`: há evidência verificável de divergência técnica ou temporal.

O índice, os títulos, URLs e a organização do manual podem mudar. A data e os links registrados permitem orientar uma nova consulta, mas não prometem atualização automática. A matriz deve ser revista quando forem alterados os guias, quando uma fonte citada mudar de forma relevante ou quando uma revisão editorial solicitar isso.

## Verificações antes da integração

As contribuições de conteúdo devem executar, quando PHP estiver disponível, as verificações documentadas em `CONTRIBUTING.md`. O verificador da matriz garante apenas que todos os arquivos Markdown de primeiro nível em `references/` estão associados a linhas da matriz, que os URLs seguem o domínio esperado e que os hashes registrados coincidem. Ele não valida semanticamente as justificativas, não verifica disponibilidade HTTP e não prova segurança, completude ou compatibilidade.

Nenhuma revisão textual ou análise sintática substitui testes funcionais apropriados. Separar processos de teste pode reduzir interferências, mas não cria uma sandbox. Exemplos Markdown não devem ser executados como fixtures confiáveis.
