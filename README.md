# php-doc-pt-br

Orientações em português brasileiro para programadores PHP e agentes de IA. O repositório reúne instruções de contexto, guias temáticos, exemplos e um pequeno verificador de padrões. É um projeto comunitário independente: não é o manual oficial do PHP nem uma distribuição completa da documentação.

Use-o como apoio, confirmando detalhes no [manual oficial do PHP](https://www.php.net/manual/pt_BR/) e na documentação do framework, das bibliotecas e da versão de PHP usadas no projeto. A tradução do manual pode ter partes incompletas ou desatualizadas; consulte a documentação oficial em inglês quando necessário.

## O que contém

- `SKILL.md`, `AGENTS.md` e `.cursorrules`: instruções que algumas ferramentas de programação com IA podem carregar automaticamente. Leia-as antes de as ativar num projeto.
- `references/`: guias selecionados sobre linguagem, segurança, PDO, Composer, APIs, testes e versões.
- `docs/` e `examples/`: materiais didáticos e exemplos para revisão.
- `tests/evals/`: casos e fixtures usados por verificações estáticas simples.

O conteúdo não cobre todas as funcionalidades do PHP, frameworks, configurações de produção nem todos os vetores de ataque. Não é uma auditoria de segurança nem substitui testes adequados ao sistema.

## Testes locais

O verificador usa funcionalidades disponíveis a partir do PHP 8.2 e não instala dependências. Execute:

```bash
php tests/evals/run-evals.php --validate-only
php tests/evals/run-evals.php
php tests/evals/test-runner.php
php tests/evals/validate-links.php
```

O modo padrão compara fixtures de texto com requisitos e padrões definidos em JSON. Ele não executa as fixtures nem o código gerado, não verifica o comportamento em produção e não prova que um exemplo é seguro. Resultados positivos significam apenas que as verificações implementadas passaram.

Existe um modo opcional para consultar um agente de IA em tempo real. Esse modo inicia um programa local escolhido pelo usuário; use apenas executáveis e argumentos que conhece e confia. Consulte [`tests/evals/README.md`](tests/evals/README.md) antes de o ativar. Não é usado no CI.

## Compatibilidade

Os exemplos procuram usar PHP 8.2 como base, salvo indicação explícita em contrário. A compatibilidade de cada trecho depende dos recursos utilizados e deve ser confirmada para o projeto de destino. `json_validate()` requer PHP 8.3 ou posterior; os exemplos básicos de leitura e validação de JSON usam `json_decode()` com `JSON_THROW_ON_ERROR`, compatível com a base 8.2.

## Utilização em ferramentas de IA

A forma de carregar instruções varia por ferramenta e versão. Consulte a documentação da ferramenta. Não presuma que qualquer IDE reconhece os mesmos nomes de arquivo, comandos ou formato. Para integrar o conteúdo num projeto, prefira copiar apenas as instruções relevantes ou fixar uma revisão específica como submódulo, em vez de acompanhar automaticamente alterações não revistas.

## Contribuições

Consulte [`CONTRIBUTING.md`](CONTRIBUTING.md), a [política de segurança](SECURITY.md), o [código de conduta](CODE_OF_CONDUCT.md) e a [licença MIT](LICENSE). Sugestões de correção são bem-vindas; a presença de um link ou de uma verificação no CI não significa que a informação tenha sido validada semanticamente.
