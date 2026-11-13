# Verificações estáticas da skill

Este diretório contém casos JSON, fixtures textuais e um runner simples. As verificações de conteúdo procuram requisitos ou padrões específicos; no modo padrão o runner também analisa a sintaxe dos blocos PHP em Markdown com o parser do PHP. Nenhum desses modos executa as fixtures ou exemplos, testa a aplicação em produção ou prova correção/segurança. Um resultado aprovado significa apenas que os critérios implementados passaram.

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

`test-runner.php` verifica limites do runner, incluindo rejeição de caminhos de fixture fora da pasta, passagem literal de argumentos, timeout e encerramento do modo live, limite combinado de saída e validação de snippets sem execução. `validate-links.php` verifica o formato e o domínio dos links nas referências; não faz pedidos HTTP para provar que cada página continua disponível.

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

O runner limita a execução a 60 segundos por padrão. `--timeout-seconds=N` pode reduzir esse limite para um valor entre 1 e 60, mas não aumentá-lo. O runner captura stdout e stderr em blocos limitados e encerra o processo se a saída conjunta exceder 1 MiB. A resposta é comparada com as mesmas verificações textuais e pode falhar por diferenças de redação, comentários ou formato. Uma aprovação tampouco constitui validação semântica do código.

## Formato dos casos

Cada arquivo `cases/*.json` contém `id`, `titulo`, `prompt`, `fixture`, `must_contain` e `must_not_contain`; `patterns_not_match` é opcional. Os campos de requisitos são listas de strings e as expressões regulares devem ser válidas. A fixture deve ser um arquivo PHP dentro de `fixtures/`; o runner não executa o seu conteúdo.

As verificações por substring e regex são heurísticas simples. Podem produzir falsos positivos e falsos negativos: por exemplo, uma palavra proibida em comentário pode falhar e código inseguro equivalente pode não conter a sequência procurada. Acrescente testes funcionais, análise estática e revisão adequada ao sistema quando necessário.
