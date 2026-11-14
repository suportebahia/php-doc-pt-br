# Orientação a objetos e recursos modernos do PHP

Os exemplos executáveis deste guia têm como base PHP 8.2. Cada recurso citado indica a versão em que foi introduzido; confirme também a compatibilidade das extensões e do framework do projeto. Este material resume decisões comuns e não substitui o manual.

## Classes, contratos e composição

Use classes para agrupar estado e comportamento quando isso representar o domínio ou uma fronteira útil. Interfaces definem contratos; traits reutilizam implementação, mas não substituem automaticamente uma interface nem resolvem problemas de acoplamento. Prefira composição quando a relação entre objetos não for realmente uma especialização. Essas são decisões de design, não regras universais do PHP.

```php
<?php

declare(strict_types=1);

interface Notificador
{
    public function enviar(string $destinatario, string $mensagem): void;
}

final class ServicoDeCadastro
{
    public function __construct(private Notificador $notificador)
    {
    }

    public function cadastrar(string $email): void
    {
        $this->notificador->enviar($email, 'Cadastro recebido.');
    }
}
```

A injeção pelo construtor torna a dependência visível e facilita testes, mas a forma de criar e configurar os objetos depende da arquitetura e do framework adotados.

- Fontes: [interfaces](https://www.php.net/manual/pt_BR/language.oop5.interfaces.php), [visibilidade](https://www.php.net/manual/pt_BR/language.oop5.visibility.php)

## Promoção de propriedades no construtor

A promoção de propriedades foi introduzida no PHP 8.0. Ela reduz repetição, mas não elimina a necessidade de escolher visibilidade adequada. A sintaxe de visibilidade assimétrica, como `private(set)`, é posterior ao baseline deste repositório; não a use em exemplos PHP 8.2.

```php
<?php

declare(strict_types=1);

final class Cliente
{
    public function __construct(
        public readonly int $id,
        public string $nome,
        private string $email,
        private string $senhaHash,
    ) {
    }

    public function email(): string
    {
        return $this->email;
    }
}
```

- Fonte: [promoção de propriedades no construtor](https://www.php.net/manual/pt_BR/language.oop5.decon.php#language.oop5.decon.constructor.promotion)

## Propriedades `readonly`

Propriedades `readonly` surgiram no PHP 8.1; classes `readonly`, no PHP 8.2. Uma propriedade `readonly` não pode ser reatribuída depois de inicializada, mas isso não torna profundamente imutável um objeto armazenado nela. Um objeto interno ainda pode ter estado mutável, conforme sua própria API.

```php
<?php

declare(strict_types=1);

readonly class Endereco
{
    public function __construct(
        public string $logradouro,
        public string $numero,
        public string $cidade,
        public string $cep,
    ) {
    }
}
```

Não confunda a restrição de escrita de uma propriedade com imutabilidade profunda de todo o grafo de objetos. Verifique ainda as mudanças da versão-alvo: a semântica de `readonly` recebeu evoluções depois do PHP 8.2.

- Fonte: [propriedades e classes `readonly`](https://www.php.net/manual/pt_BR/language.oop5.basic.php#language.oop5.basic.class.readonly)

## Enums e `match`

Enums foram introduzidos no PHP 8.1. Use-os quando o domínio tiver um conjunto fechado de alternativas; para valores externos, trate explicitamente o caso inválido. A expressão `match`, disponível desde PHP 8.0, compara estritamente e retorna um valor.

```php
<?php

declare(strict_types=1);

enum StatusPedido: string
{
    case PENDENTE = 'pendente';
    case PROCESSANDO = 'processando';
    case CONCLUIDO = 'concluido';
}

function rotulo(StatusPedido $status): string
{
    return match ($status) {
        StatusPedido::PENDENTE => 'Aguardando pagamento',
        StatusPedido::PROCESSANDO => 'Em separação',
        StatusPedido::CONCLUIDO => 'Concluído',
    };
}
```

`StatusPedido::from()` lança `ValueError` para um valor sem caso correspondente; `tryFrom()` retorna `null`. Escolha a estratégia conforme a validação e o tratamento de erro do domínio.

- Fontes: [enumerações](https://www.php.net/manual/pt_BR/language.enumerations.php), [expressão `match`](https://www.php.net/manual/pt_BR/control-structures.match.php)

## Atributos, generators e fibers

Atributos, disponíveis desde PHP 8.0, permitem associar metadados estruturados a declarações. Eles não executam comportamento automaticamente; uma biblioteca ou aplicação precisa lê-los, geralmente por Reflection.

Generators com `yield` permitem produzir valores progressivamente. Eles podem reduzir o uso de memória quando o produtor e o consumidor processam dados de forma incremental; a palavra-chave, por si só, não garante que outras partes do sistema não armazenem o conjunto inteiro.

```php
<?php

declare(strict_types=1);

function lerLinhas(string $caminho): Generator
{
    $arquivo = fopen($caminho, 'rb');
    if ($arquivo === false) {
        throw new RuntimeException('Não foi possível abrir o arquivo.');
    }

    try {
        while (($linha = fgets($arquivo)) !== false) {
            yield rtrim($linha, "\r\n");
        }
    } finally {
        fclose($arquivo);
    }
}
```

Fibers, introduzidas no PHP 8.1, são primitivas de suspensão e retomada de execução. Não constituem, sozinhas, um scheduler, sistema de I/O assíncrono ou fila; esses comportamentos dependem de bibliotecas e do runtime usados.

- Fontes: [atributos](https://www.php.net/manual/pt_BR/language.attributes.php), [generators](https://www.php.net/manual/pt_BR/language.generators.overview.php), [Fibers](https://www.php.net/manual/pt_BR/language.fibers.php)

## Erros e exceções

`Throwable` é a interface-base dos objetos que podem ser lançados, incluindo `Error` e `Exception`. Capture uma falha apenas quando puder tratá-la, traduzi-la ou acrescentar contexto; uma fronteira HTTP pode converter uma exceção conhecida em resposta, enquanto código que não sabe recuperá-la deve permitir que o tratamento central atue.

Evite capturar `Throwable` indiscriminadamente e continuar como se a operação tivesse sido concluída. Exceções de domínio, tipos de erro e política de registro dependem do sistema.

- Fontes: [hierarquia de erros](https://www.php.net/manual/pt_BR/language.error-handling.php), [`Throwable`](https://www.php.net/manual/pt_BR/class.throwable.php)

## Interoperabilidade

Namespaces organizam nomes de tipos; o mapeamento de namespaces para arquivos costuma ser configurado por Composer usando PSR-4. A sensibilidade a maiúsculas/minúsculas e a estabilidade de assinaturas podem afetar portabilidade. Consulte [Composer e autoloading](composer-e-autoloading.md) e [padrões do ecossistema](frameworks-e-padroes-ecossistema.md) para orientações específicas.
