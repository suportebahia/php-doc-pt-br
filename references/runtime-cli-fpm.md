# Runtime, CLI, configuração e PHP-FPM

PHP pode executar sob diferentes SAPI. O CLI usado pelo desenvolvedor não garante que FPM ou outro servidor carregue a mesma versão, arquivo `php.ini`, extensões, variáveis ou diretivas. Confirme cada runtime efetivamente usado.

## Diagnóstico da plataforma

No ambiente de destino, verifique a versão, o arquivo principal de configuração, arquivos adicionais e extensões carregadas. Comandos CLI como `php -v`, `php --ini` e `php -m` ajudam a diagnosticar o binário CLI; não provam o estado do FPM. Evite publicar saídas completas de `phpinfo()` porque podem expor detalhes de ambiente.

`php.ini`, diretórios de scan e overrides podem variar por SAPI. Inventarie extensões e versões exigidas em desenvolvimento, CI, workers e produção. Um `composer.json` pode declarar requisitos, mas a verificação no runtime real continua necessária.

- Fontes: [opções CLI](https://www.php.net/manual/pt_BR/features.commandline.options.php), [arquivos de configuração](https://www.php.net/manual/pt_BR/configuration.file.php), [diretivas centrais](https://www.php.net/manual/pt_BR/ini.core.php)

## Variáveis de ambiente

Um arquivo `.env` não é carregado automaticamente pelo PHP. Use o mecanismo do framework/plataforma escolhido e valide configuração obrigatória no início da aplicação. Converta strings para booleanos, inteiros e outros tipos com regras explícitas; não trate qualquer string não vazia como `true` sem decidir o contrato.

No PHP-FPM, o pool controla quais variáveis chegam aos workers; `clear_env` é habilitado por padrão na documentação do FPM. Teste valores no SAPI web, não apenas no CLI. Não imprima segredos em logs, comandos, páginas de diagnóstico ou saídas do CI.

- Fontes: [variáveis de ambiente](https://www.php.net/manual/pt_BR/reserved.variables.environment.php), [`getenv`](https://www.php.net/manual/pt_BR/function.getenv.php), [configuração FPM](https://www.php.net/manual/pt_BR/install.fpm.configuration.php)

## PHP-FPM e deployment

Trate o socket/listener FastCGI como interface privilegiada: não o exponha a redes não confiáveis. Configure usuário/grupo, permissões, pool e logs para a plataforma. `pm.max_children`, modo de gerenciamento, `pm.max_requests` e timeouts influenciam capacidade e reciclagem, mas valores apropriados dependem de memória, carga, latência e limites do host.

Mantenha endpoints de status e slowlog protegidos. Coordenar timeout do FPM, proxy, cliente e dependências evita que uma camada espere indefinidamente enquanto outra encerra o trabalho. Planeje reload/restart e rollback segundo o mecanismo de deploy. Não copie números de capacidade sem medições.

- Fontes: [instalação/configuração PHP-FPM](https://www.php.net/manual/pt_BR/install.fpm.php), [diretivas de configuração FPM](https://www.php.net/manual/pt_BR/install.fpm.configuration.php)

## CLI, workers e streams

Comandos CLI devem ter códigos de saída previsíveis, limites de tempo e mensagens em stdout/stderr adequadas ao supervisor. Workers de longa duração precisam planejar memória, sinais, drenagem de tarefas e tratamento de duplicatas conforme a fila/broker. `pcntl` é uma extensão com disponibilidade dependente da plataforma; não é um supervisor.

Streams e wrappers permitem I/O de diferentes fontes e protocolos. Use esquemas conhecidos e opções de contexto explícitas; não monte URLs ou caminhos sensíveis diretamente com entrada não confiável.

- Fontes: [controle de processos](https://www.php.net/manual/pt_BR/book.pcntl.php), [wrappers](https://www.php.net/manual/pt_BR/wrappers.php), [contextos de streams](https://www.php.net/manual/pt_BR/stream.contexts.php)

## Checklist de verificação

Registre a versão/SAPI e extensões necessárias em cada ambiente. Compare CLI e FPM separadamente. Valide o `php.ini` efetivo, as permissões e a origem das variáveis, limites de upload e timeout. Faça isso sem expor segredos nem criar uma página pública de diagnóstico.
