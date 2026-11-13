# Como contribuir

Contribuições que melhorem clareza, correção técnica e compatibilidade são bem-vindas. Antes de abrir uma alteração, confira se o conteúdo não duplica uma referência existente e se as afirmações dependentes de versão apontam para documentação primária.

## Alterações em guias e exemplos

Inclua o contexto e a versão mínima do PHP, framework ou dependências quando afetarem o exemplo. Para blocos PHP que exigem versão superior ao baseline, coloque imediatamente antes do bloco `<!-- php-min: X.Y -->` (por exemplo, `<!-- php-min: 8.4 -->`). Não apresente preferências como regras universais nem prometa que um trecho é “blindado”, infalível ou pronto para produção sem contexto. Preserve os exemplos em português brasileiro e explique as limitações relevantes.

## Alterações nos testes

Os casos em `tests/evals/` são verificações textuais simples. Ao acrescentar um caso, descreva o que o padrão efetivamente verifica; não o trate como prova semântica de segurança. O runner também analisa a sintaxe dos blocos PHP em Markdown com `token_get_all(..., TOKEN_PARSE)`, sem executá-los. O CI executa apenas o caso funcional explicitamente allowlistado em `tests/functional/json-object-decoder.php`, que carrega um helper puro sem I/O; não execute fixtures nem outros blocos de exemplo durante o CI. Novos testes funcionais devem apontar para caminhos fixos revisados, rodar em processo separado e declarar os efeitos que podem ocorrer. Isso não constitui sandbox.

Execute antes de submeter:

```bash
php tests/evals/run-evals.php --validate-only
php tests/evals/run-evals.php
php tests/evals/test-runner.php
php tests/evals/validate-links.php
```

## Pull requests

Descreva o problema, a solução, as fontes consultadas e os comandos de teste executados. Se uma alteração não puder ser validada localmente, indique isso. Não inclua credenciais, dados pessoais ou segredos.
