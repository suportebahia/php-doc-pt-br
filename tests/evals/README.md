# Verificações estáticas da skill

Este diretório contém casos JSON, fixtures textuais e um runner simples. As verificações de conteúdo procuram requisitos ou padrões específicos; no modo padrão o runner também analisa a sintaxe dos blocos PHP em Markdown com o parser do PHP. O CI não executa as fixtures nem os blocos PHP dos documentos. A exceção explicitamente allowlistada é `tests/functional/json-object-decoder.php`, executada em um processo PHP separado e limitada a uma função pura. Nenhum desses testes verifica a aplicação em produção ou prova correção/segurança; o processo separado não é uma sandbox.

## Execução local

O runner requer PHP 8.2 ou posterior e não instala dependências:

```bash
php tests/evals/run-evals.php --validate-only
php tests/evals/run-evals.php
php tests/evals/run-evals.php --case=autenticacao-segura
php tests/evals/test-runner.php
php tests/evals/validate-links.php
```

`--validate-only` valida o formato dos casos e a existência das fixtures dentro da pasta esperada. O modo padrão compara cada fixture com os requisitos definidos no JSON e analisa os blocos `php` dos arquivos Markdown versionados. Blocos com requisito superior ao PHP em execução devem ser precedidos por `<!-- php-min: X.Y -->`; são verificados nas versões compatíveis da matriz. `token_get_all(..., TOKEN_PARSE)` analisa sintaxe sem executar o código. Os filtros são convenientes para desenvolvimento; o CI executa o conjunto completo.

`test-runner.php` verifica limites do runner, incluindo rejeição de caminhos de fixture fora da pasta, passagem literal de argumentos, timeout do processo direto, limite combinado de saída e validação de snippets sem execução. Também executa somente o teste funcional explicitamente indicado no próprio script; não descobre testes por glob. `validate-links.php` verifica o formato e o domínio dos links nas referências; não faz pedidos HTTP para provar que cada página continua disponível.

## Avaliação opcional com um agente local

O modo `--live` executa um programa local escolhido pelo usuário. O runner recebe a configuração como um array JSON de argumentos e inicia o executável sem passar por um shell. Isso reduz riscos de interpretação de metacaracteres, mas não torna confiável um executável desconhecido: esse programa pode ter permissões, ler arquivos e acessar à rede conforme o ambiente local. Use apenas programas que conhece e confia. O modo não é executado no CI.

O prompt do caso é acrescentado como último argumento. Exemplo para Claude Code:

```bash
php tests/evals/run-evals.php --live --agent-command-json='["claude","-p"]'
```

Ou configure a variável de ambiente:

```bash
export AI_EVAL_COMMAND_JSON='["claude","-p"]'
php tests/evals/run-evals.php --live
```

O runner limita a execução a 60 segundos por padrão. `--timeout-seconds=N` pode reduzir esse limite para um valor entre 1 e 60, mas não aumentá-lo. O runner captura stdout e stderr em blocos limitados e encerra o processo direto se a saída conjunta exceder 1 MiB ou se o tempo acabar. Ele não implementa gestão de grupos de processos e não garante encerrar subprocessos que o executável iniciado possa criar; para comandos que criam processos filhos, use uma sandbox ou supervisor externo apropriado. O modo `--live` executa um programa escolhido pelo usuário e não é uma fronteira de segurança.

## Formato dos casos

Cada arquivo `cases/*.json` contém `id`, `titulo`, `prompt`, `fixture`, `must_contain` e `must_not_contain`; `patterns_not_match` é opcional. Os campos de requisitos são listas de strings e as expressões regulares devem ser válidas. A fixture deve ser um arquivo PHP dentro de `fixtures/`; o runner não executa o seu conteúdo.

As verificações por substring e regex são heurísticas simples. Podem produzir falsos positivos e falsos negativos: por exemplo, uma palavra proibida em comentário pode falhar e código inseguro equivalente pode não conter a sequência procurada. Acrescente testes funcionais, análise estática e revisão adequada ao sistema quando necessário.
