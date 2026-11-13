# Performance, concorrência e operação PHP

Tuning começa com uma hipótese e uma carga representativa. Meça baseline, CPU, memória, latência e dependências antes de alterar código. Profilers podem exigir extensões e causar overhead; registre ferramenta, versão, ambiente e cenário. Benchmark isolado não representa necessariamente produção.

## OPcache, cache de dados e FPM

OPcache guarda bytecode compilado; não é o mesmo que cache de dados da aplicação. APCu e caches distribuídos têm escopos e garantias diferentes. Em produção, a estratégia de validação/invalidação do OPcache deve acompanhar o processo de deploy; com verificação de timestamps desativada, alterações podem exigir invalidação ou restart.

No PHP-FPM, `pm.max_children` limita requisições concorrentes do pool. Sizing depende da memória por worker, outras cargas, latência e recursos do host. `pm.max_requests`, status, slowlog e timeouts são ferramentas operacionais, não valores universais.

- Fontes: [configuração OPcache](https://www.php.net/manual/pt_BR/opcache.configuration.php), [configuração PHP-FPM](https://www.php.net/manual/pt_BR/install.fpm.configuration.php), [APCu](https://www.php.net/manual/pt_BR/book.apcu.php)

## Cache e filas

Um cache exige política explícita de chave, TTL, invalidação, comportamento em miss e tratamento de falha do backend. PSR-6 define uma interface, não consistência distribuída nem persistência garantida.

Filas e workers normalmente dependem de broker, biblioteca ou framework. Documente semântica de entrega, duplicatas, retry, backpressure, fila de falha e idempotência. Limite tentativas e tempo; coordene shutdown com supervisor/orquestrador para concluir ou reencaminhar trabalho sem perder estado.

- Fontes: [PHP-FIG PSR-6](https://www.php-fig.org/psr/psr-6/), [mensageria Symfony](https://symfony.com/doc/current/messenger.html)

## Memória, timeout e observabilidade

`memory_limit` limita alocações do script PHP, mas não é o orçamento total do processo, container ou host. Timeouts também podem existir no cliente, proxy, FPM, banco e broker. Defina deadline coerente e trate cancelamento/timeout em cada dependência.

Registre logs com contexto útil e sem segredos. Métricas de latência, erros, profundidade/idade de fila, memória e reinícios ajudam a distinguir causas. Proteja status pages, slowlogs e dashboards operacionais.

## Verificação antes do deploy

Teste em ambiente representativo: compare medição antes/depois, valide invalidação de OPcache, faça drain de workers, verifique health checks e ensaie rollback. Valores de capacity e tuning só são confiáveis para as condições medidas; não generalize números de uma máquina para outra.
