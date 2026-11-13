# Verificações e testes

Este diretório contém casos JSON, verificações textuais, análise sintática e uma allowlist de testes funcionais. São camadas distintas: uma não substitui a outra, e nenhuma prova por si só a correção ou a segurança de uma aplicação.

## Tipos de verificação

- **Validação e verificações textuais:** `run-evals.php --validate-only` valida o formato dos casos e os nomes/caminhos das fixtures. O modo padrão compara fixtures com requisitos textuais e padrões definidos nos casos; esses resultados são heurísticos e podem ter falsos positivos ou negativos.
- **Análise sintática:** o modo padrão também analisa os blocos PHP dos arquivos Markdown versionados com `token_get_all(..., TOKEN_PARSE)`. O parser verifica sintaxe, mas não executa os snippets.
- **Testes funcionais:** `test-runner.php` chama apenas os caminhos fixos listados no array `$functionalTests`. Cada teste funcional é iniciado por uma invocação própria de `PHP_BINARY`, num processo separado. Atualmente, a allowlist contém:
  - `tests/functional/json-object-decoder.php`
  - `tests/functional/strict-date-validation.php`
  - `tests/functional/preg-match-result.php`

Os testes funcionais carregam somente helpers PHP puros selecionados em `examples/`. Não se descobrem testes por glob, não se executam fixtures de `tests/evals/` nem blocos PHP em Markdown, e os exemplos cobertos não usam rede, banco de dados, credenciais ou dados reais. Processos separados reduzem interferências entre testes, mas **não constituem uma sandbox**.

## Execução local

Requer PHP 8.2 ou posterior e não instala dependências:

```bash
php tests/evals/run-evals.php --validate-only
php tests/evals/run-evals.php
php tests/evals/test-runner.php
php tests/evals/validate-links.php
```

O CI mantém a matriz PHP 8.2–8.5 e executa a validação, as verificações textuais e sintáticas, os testes funcionais allowlistados e a validação de links. O CI não executa snippets Markdown nem uma avaliação `--live`. Os testes de regressão que exercitam a opção `--live` com comandos PHP sintéticos e fixos são omitidos quando `CI=true`; localmente, eles não usam agente externo. O próprio modo `--live` executa um programa local escolhido pelo usuário, não é sandbox e não deve ser usado como fronteira de segurança.

Os filtros `--case=...` são convenientes durante o desenvolvimento; o CI executa o conjunto textual completo. Blocos que requerem versão posterior ao baseline devem ser precedidos por `<!-- php-min: X.Y -->` e são analisados nas versões compatíveis da matriz.

Cada arquivo `cases/*.json` contém `id`, `titulo`, `prompt`, `fixture`, `must_contain` e `must_not_contain`; `patterns_not_match` é opcional. Os campos de requisitos são listas de strings, e as expressões regulares devem ser válidas. `fixture` deve ser apenas o nome de um arquivo PHP dentro de `fixtures/`; o runner valida e lê o conteúdo para as verificações textuais, mas não executa a fixture.

## Avaliação opcional com programa local

O modo `--live` executa o programa local escolhido pelo usuário. O runner recebe o comando como um array JSON de argumentos e inicia o executável sem shell, o que reduz a interpretação de metacaracteres, mas não torna confiável um executável desconhecido: ele poderá ter as permissões do usuário, incluindo ler arquivos ou acessar a rede. Use apenas programas conhecidos e confiáveis. Este modo não faz parte do CI.

Exemplo local para Claude Code:

```bash
php tests/evals/run-evals.php --live --agent-command-json='["claude","-p"]'
```

Também é possível configurar `AI_EVAL_COMMAND_JSON` com o mesmo array JSON. O runner limita a execução a 60 segundos por padrão; `--timeout-seconds=N` pode reduzir o limite para 1–60 segundos, nunca aumentá-lo. A saída conjunta de stdout e stderr é limitada a 1 MiB. O runner encerra o processo direto ao atingir o limite de saída ou o timeout, mas não gere grupos de processos nem garante encerrar subprocessos que o programa iniciado possa criar. Use supervisor ou ambiente isolado adequado quando houver processos filhos. `--live` não é uma sandbox nem uma fronteira de segurança.

`validate-links.php` verifica o formato e o domínio dos links nas referências; não faz pedidos HTTP para provar que cada página continua disponível. As verificações textuais podem sinalizar palavras em comentários e não detectar código equivalente com outra forma. Use revisão e testes adequados ao sistema quando necessário.
