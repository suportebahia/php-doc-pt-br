# Como contribuir

Contribuições que melhorem clareza, correção técnica e compatibilidade são bem-vindas. Antes de abrir uma alteração, confira se o conteúdo não duplica uma referência existente e se as afirmações dependentes de versão apontam para documentação primária.

## Alterações em guias e exemplos

Inclua o contexto e a versão mínima do PHP, framework ou dependências quando afetarem o exemplo. Para blocos PHP que exigem versão superior ao baseline, coloque imediatamente antes do bloco `<!-- php-min: X.Y -->` (por exemplo, `<!-- php-min: 8.4 -->`). Não apresente preferências como regras universais nem prometa que um trecho é “blindado”, infalível ou pronto para produção sem contexto. Preserve os exemplos em português brasileiro e explique as limitações relevantes.

## Alterações nos testes

Separe três camadas ao descrever resultados: as verificações em `tests/evals/` comparam conteúdo textual e são heurísticas; `token_get_all(..., TOKEN_PARSE)` analisa a sintaxe dos blocos PHP em Markdown sem executá-los; e testes funcionais exercitam comportamento de fontes PHP selecionadas. O `tests/evals/test-runner.php` mantém uma allowlist explícita de testes funcionais e inicia cada caminho permitido num processo separado. Não acrescente descoberta por glob, execução de fixtures ou execução de snippets Markdown. Os testes atuais cobrem `json-object-decoder.php`, `strict-date-validation.php` e `preg-match-result.php`, usando apenas helpers puros e dados sintéticos. O CI não executa snippets Markdown nem uma avaliação `--live`; testes locais da opção `--live` usam apenas comandos sintéticos fixos e são omitidos com `CI=true`. Processos separados não constituem uma sandbox.

Ao alterar `references/*.md`, reveja também as seções afetadas em `docs/MATRIZ-COBERTURA.md`; não atualize os hashes sem rever os estados e as justificações. Uma nova referência deve aparecer na matriz. `GOVERNANCE.md` descreve os critérios e a revisão esperada.

Execute antes de submeter:

```bash
php tests/evals/run-evals.php --validate-only
php tests/evals/run-evals.php
php tests/evals/test-runner.php
php tests/evals/validate-links.php
php tests/evals/validate-coverage-matrix.php
php tests/evals/test-coverage-matrix.php
```

## Pull requests

Descreva o problema, a solução, as fontes consultadas e os comandos de teste executados. Se uma alteração não puder ser validada localmente, indique isso. Não inclua credenciais, dados pessoais ou segredos.
