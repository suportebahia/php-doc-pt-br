# Testes Automatizados e Análise Estática no PHP

Padrão de engenharia de software para garantia de qualidade, prevenção de regressões e validação estática de tipos em projetos PHP.

---

## 1. Por que Análise Estática e Testes?
No PHP moderno, a garantia de que o sistema funciona corretamente antes de ir para produção é dividida em dois níveis:

1. **Análise Estática (ex: PHPStan):** Examina o código-fonte **sem executá-lo**, detectando incompatibilidades de tipos, chamadas a métodos inexistentes, variáveis não inicializadas e código morto.
2. **Testes Automatizados (ex: PHPUnit):** **Executam** cenários reais de uso, validando se as regras de negócio produzem os resultados esperados diante de entradas válidas e inválidas.

---

## 2. Análise Estática com PHPStan

### Instalação:
```bash
composer require --dev phpstan/phpstan
```

### Arquivo de Configuração (`phpstan.neon` na raiz do projeto):
```neon
parameters:
    level: 8          # Níveis de 0 (básico) a 9 (ultra-estrito). Nível 8 é o padrão ouro.
    paths:
        - src
        - tests
    checkGenericClassInNonGenericObjectType: false
```

### Execução:
```bash
vendor/bin/phpstan analyse
```
Se o seu código contiver um método que pode retornar `null`, mas você tentar chamá-lo sem verificar antes, o PHPStan bloqueará e apontará a linha exata antes de qualquer deploy.

---

## 3. Testes Automatizados com PHPUnit

### Instalação:
```bash
composer require --dev phpunit/phpunit
```

### Arquivo de Configuração (`phpunit.xml`):
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.0/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

---

## 4. Estrutura de um Teste Unitário Limpo

### A Classe a ser Testada (`src/Service/CalculadoraDesconto.php`):
```php
<?php

declare(strict_types=1);

namespace App\Service;

class CalculadoraDesconto
{
    public function calcular(float $valorTotal, float $porcentagem): float
    {
        if ($valorTotal < 0 || $porcentagem < 0 || $porcentagem > 100) {
            throw new \InvalidArgumentException('Valores de entrada inválidos para cálculo de desconto.');
        }

        return $valorTotal - ($valorTotal * ($porcentagem / 100));
    }
}
```

### A Classe de Teste (`tests/Unit/CalculadoraDescontoTest.php`):
```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\CalculadoraDesconto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CalculadoraDescontoTest extends TestCase
{
    private CalculadoraDesconto $calculadora;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculadora = new CalculadoraDesconto();
    }

    public function testDeveCalcularDescontoComSucesso(): void
    {
        // Cenário: R$ 100,00 com 15% de desconto deve resultar em R$ 85,00
        $resultado = $this->calculadora->calcular(100.0, 15.0);

        $this->assertSame(85.0, $resultado);
    }

    public function testDeveLancarExcecaoQuandoPorcentagemMaiorQueCem(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Valores de entrada inválidos');

        $this->calculadora->calcular(100.0, 110.0);
    }
}
```

---

## 5. Principais Asserções do PHPUnit

| Asserção | O que Valida |
| :--- | :--- |
| `$this->assertSame($esperado, $atual)` | Valida valor e tipo estrito (equivalente a `===`). |
| `$this->assertTrue($condicao)` | Valida se a condição é estritamente `true`. |
| `$this->assertFalse($condicao)` | Valida se a condição é estritamente `false`. |
| `$this->assertCount($quantidade, $array)` | Valida se o array contém o número exato de elementos. |
| `$this->assertNull($valor)` | Valida se a variável é estritamente `null`. |
| `$this->expectException(Classe::class)` | Valida se o método lança a exceção esperada. |

### Executando os Testes:
```bash
vendor/bin/phpunit
```
- Site oficial do PHPUnit: [phpunit.de](https://phpunit.de/)
