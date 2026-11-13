# Orientação a Objetos e Recursos Modernos do PHP

Referência canônica de Programação Orientada a Objetos e recursos do PHP 8.1, 8.2 e 8.3+.

---

## 1. Promoção de Propriedades no Construtor (*Constructor Property Promotion*)
Elimina a repetição de declarar atributos e atribuí-los dentro do construtor:

```php
<?php

declare(strict_types=1);

namespace App\Model;

class Cliente
{
    public function __construct(
        public readonly int $id,
        public string $nome,
        public private(set) string $email,
        private string $senhaHash
    ) {}
}
```
- Fonte: [Promoção de Propriedades no Construtor](https://www.php.net/manual/pt_BR/language.oop5.decon.php#language.oop5.decon.constructor.promotion)

---

## 2. Modificador `readonly`
- **Propriedades `readonly`:** Podem ser inicializadas apenas uma única vez (geralmente no construtor) e nunca mais alteradas.
- **Classes `readonly` (PHP 8.2+):** Tornam automaticamente todas as propriedades declaradas como `readonly` e impedem a criação de propriedades dinâmicas.

```php
<?php

declare(strict_types=1);

namespace App\ValueObject;

readonly class Endereco
{
    public function __construct(
        public string $logradouro,
        public string $numero,
        public string $cidade,
        public string $cep
    ) {}
}
```
- Fonte: [Modificador readonly](https://www.php.net/manual/pt_BR/language.oop5.basic.php#language.oop5.basic.class.readonly)

---

## 3. Enumerações (*Enums*)
Substituem constantes dispersas por conjuntos tipados e seguros. Podem ser *Pure Enums* ou *Backed Enums* (com valor associado `int` ou `string`):

```php
<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusPedido: string
{
    case PENDENTE   = 'pendente';
    case PROCESSANDO = 'processando';
    case CONCLUIDO  = 'concluido';
    case CANCELADO  = 'cancelado';

    public function rotulo(): string
    {
        return match ($this) {
            self::PENDENTE   => 'Aguardando Pagamento',
            self::PROCESSANDO => 'Em Separação',
            self::CONCLUIDO  => 'Entregue com Sucesso',
            self::CANCELADO  => 'Cancelado pelo Cliente',
        };
    }
}
```
- Métodos nativos de Backed Enums:
  - `StatusPedido::from('pendente')` (lança `ValueError` se inválido).
  - `StatusPedido::tryFrom('invalido')` (retorna `null` se inválido).
- Fonte: [Enumerações](https://www.php.net/manual/pt_BR/language.enumerations.php)

---

## 4. Expressão `match`
Uma alternativa moderna ao `switch`:
- Comparações estritas (`===`), sem coerção de tipo inesperada.
- Retorna valor diretamente (é uma expressão).
- Não necessita da palavra-chave `break` (não ocorre *fall-through* indesejado).
- Lança `UnhandledMatchError` caso nenhum caso seja satisfeito e não haja cláusula `default`.

```php
$resultado = match ($codigoStatus) {
    200, 201 => 'Sucesso',
    400, 422 => 'Erro de Validação',
    404      => 'Não Encontrado',
    500      => 'Falha no Servidor',
    default  => 'Status Desconhecido',
};
```
- Fonte: [Expressões match](https://www.php.net/manual/pt_BR/control-structures.match.php)

---

## 5. Geradores (*Generators* e `yield`)
Permitem iterar sobre grandes volumes de dados (milhares de linhas de arquivo ou consultas de banco) sem carregar tudo na memória RAM:

```php
<?php

declare(strict_types=1);

function lerLinhasArquivo(string $caminho): \Generator
{
    $arquivo = fopen($caminho, 'rb');
    if ($arquivo === false) {
        throw new \RuntimeException("Não foi possível abrir o arquivo: {$caminho}");
    }

    try {
        while (($linha = fgets($arquivo)) !== false) {
            yield trim($linha);
        }
    } finally {
        fclose($arquivo);
    }
}

// Uso econômico de memória:
foreach (lerLinhasArquivo('log_grande.txt') as $linha) {
    // Processa uma linha de cada vez
}
```
- Fonte: [Geradores](https://www.php.net/manual/pt_BR/language.generators.php)
