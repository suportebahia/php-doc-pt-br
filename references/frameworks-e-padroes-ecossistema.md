# Padrões PHP-FIG e frameworks

PSRs definem contratos ou convenções de interoperabilidade. O status `Accepted` no PHP-FIG não torna uma PSR obrigação universal nem garante que qualquer biblioteca a implemente. Verifique status, versão e documentação do mantenedor antes de depender de uma interface.

## PSRs úteis

- **[PSR-1](https://www.php-fig.org/psr/psr-1/) e [PSR-12](https://www.php-fig.org/psr/psr-12/):** regras de interoperabilidade/estilo. PSR-12 é uma convenção de formatação, não uma regra de execução.
- **[PSR-3](https://www.php-fig.org/psr/psr-3/):** interface comum de logger; não define backend, retenção ou política operacional.
- **[PSR-4](https://www.php-fig.org/psr/psr-4/):** mapeamento de namespaces para diretórios/arquivos; o case importa para portabilidade.
- **[PSR-7](https://www.php-fig.org/psr/psr-7/):** interfaces para mensagens HTTP e streams; não é a API nativa de todos os frameworks.
- **[PSR-11](https://www.php-fig.org/psr/psr-11/):** interface mínima de container. A especificação desencoraja usar o container como service locator dentro dos objetos.
- **[PSR-15](https://www.php-fig.org/psr/psr-15/):** contratos de handlers e middleware HTTP baseados em PSR-7; frameworks podem usar outros ciclos ou precisar de adaptadores.

- Fonte canônica e status: [índice PHP-FIG](https://www.php-fig.org/psr/)

## Middleware e injeção de dependências

Middleware PSR-15 não é automaticamente intercambiável com middleware do Laravel ou eventos/listeners do Symfony. Assinaturas, registro, ordem e ciclo de requisição dependem do framework e da versão. Consulte a documentação da versão que o projeto realmente instala.

A injeção pelo construtor deixa dependências explícitas. Containers podem construir os objetos e mapear interfaces para implementações; evite que classes de domínio consultem um container global sem necessidade. PSR-11 descreve uma interface, não a configuração completa do container.

Laravel tem APIs próprias de middleware, service container e configuração. Symfony usa, entre outros mecanismos, o HttpKernel, listeners e o service container. Mantenha o código de domínio separado quando isso trouxer valor, mas não crie abstrações genéricas sem necessidade.

- Fontes: [middleware Laravel 12.x](https://laravel.com/docs/12.x/middleware), [container Laravel 12.x](https://laravel.com/docs/12.x/container), [middleware Laravel 13.x](https://laravel.com/docs/13.x/middleware), [container Symfony (`current`)](https://symfony.com/doc/current/service_container.html), [event dispatcher Symfony (`current`)](https://symfony.com/doc/current/event_dispatcher.html), [PSR-15](https://www.php-fig.org/psr/psr-15/)

## Versão e configuração

As referências Laravel 12.x e 13.x acima são documentos de séries distintas. Na data contextual desta revisão (2026-11-14), o relatório registrou Laravel 13 como atual e Laravel 12 como antigo; os links 12.x continuam pertinentes a projetos que usam essa série. Isso é um registro datado, não uma recomendação universal de Laravel 12 ou 13. Os links Symfony usam `/doc/current/`: indicam o canal de documentação atual, não fixam por si só uma release específica. Consulte [releases do Symfony](https://symfony.com/releases) e a documentação correspondente à versão instalada.

A versão do framework e a compatibilidade com a versão do PHP são eixos distintos: a referência a uma série do framework não determina, sozinha, qual versão do PHP o projeto pode usar. Verifique os requisitos da série e das dependências realmente instaladas. Em caso de divergência, prevalecem a versão instalada no projeto e suas restrições de dependência. Comandos, diretórios e configuração também são versionados; não copie comandos de `current` para projeto legado sem essa conferência. Segredos e configuração por ambiente devem seguir a ferramenta adotada; `.env` não é um recurso carregado automaticamente pelo PHP core.

Se uma integração atravessa frameworks, identifique quais objetos são PSR, quais são tipos nativos do framework e onde ocorre o adaptador. Teste o fluxo real, não presuma compatibilidade pelo nome do padrão.

- Fontes: [migrações Laravel 12.x](https://laravel.com/docs/12.x/releases), [releases Symfony](https://symfony.com/releases), [PSR-7 no Symfony (`current`)](https://symfony.com/doc/current/components/psr7.html)
