# Padrões Oficiais de Desenvolvimento PHP

Diretrizes obrigatórias para escrita de código PHP moderno, seguro, manutenível e fundamentado no manual oficial.

---

## 1. Tipagem Estrita e Abertura de Arquivo
- Todo arquivo com código PHP puro **deve** iniciar obrigatoriamente com a declaração de tipagem estrita na primeira linha:
  ```php
  <?php

  declare(strict_types=1);
  ```
- **Nunca** utilize a tag de fechamento `?>` em arquivos contendo apenas código PHP. Isso previne o envio acidental de espaços em branco antes do envio de cabeçalhos HTTP.
  - Fonte: [Sintaxe Básica - Tags PHP](https://www.php.net/manual/pt_BR/language.basic-syntax.phptags.php)

---

## 2. Tipagem Completa de Parâmetros, Propriedades e Retornos
O PHP moderno suporta tipagem forte em todos os níveis. Não deixe declarações sem tipo explícito.

- Tipos primitivos: `int`, `float`, `string`, `bool`.
- Tipos especiais: `void` (sem retorno), `never` (funções que encerram ou sempre lançam exceção), `mixed` (quando estritamente necessário).
- Tipos anuláveis (*nullable*): `?string` ou `string|null`.
- Tipos de união e interseção: `int|float`, `Traversable&Countable`.
- Exemplo:
  ```php
  <?php

  declare(strict_types=1);

  namespace App\Service;

  class Calculadora
  {
      public function dividir(int|float $dividendo, int|float $divisor): float
      {
          if ($divisor === 0) {
              throw new \InvalidArgumentException('O divisor não pode ser zero.');
          }

          return $dividendo / $divisor;
      }
  }
  ```
- Fonte: [Tipos de Declaração](https://www.php.net/manual/pt_BR/language.types.declarations.php)

---

## 3. Padrão de Estilo de Código (PSR-12 / PER Coding Style)
- **Indentação:** 4 espaços (nunca tabulações).
- **Nomenclatura:**
  - Classes, Enums e Interfaces: `PascalCase` (ex: `GerenciadorUsuario`, `StatusPedido`).
  - Métodos e Funções: `camelCase` (ex: `buscarPorId()`, `autenticar()`).
  - Constantes e Casos de Enums: `UPPER_SNAKE_CASE` (ex: `LIMITE_TENTATIVAS`, `case ATIVO`).
  - Variáveis e Propriedades: `camelCase` (ex: `$usuarioId`, `$dataCriacao`).
- **Visibilidade Explícita:** Toda propriedade, método e constante de classe **deve** declarar explicitamente sua visibilidade (`public`, `protected`, `private`).
  - Fonte: [Classes e Objetos - Visibilidade](https://www.php.net/manual/pt_BR/language.oop5.visibility.php)

---

## 4. Retorno Antecipado (*Early Return*) e Código Limpo
Evite aninhamentos profundos de condicionais (`if` dentro de `if`). Trate casos de erro, exceção ou condições de parada imediatamente e retorne cedo:

```php
// Inadequado:
if ($usuario !== null) {
    if ($usuario->estaAtivo()) {
        // lógica complexa
    }
}

// Recomendado:
if ($usuario === null || !$usuario->estaAtivo()) {
    return false;
}

// fluxo principal limpo
return true;
```

---

## 5. Tratamento Robusto de Erros e Exceções
- Utilize o modelo nativo de exceções (`Throwable`, `Exception`, `RuntimeException`, `InvalidArgumentException`).
- Nunca silencie erros com o operador `@` em tempo de execução.
- Em ambientes de produção:
  - `display_errors = Off` (não expor trace para o usuário final).
  - `log_errors = On` (gravar erros em log seguro).
- Fonte: [Tratamento de Erros e Exceções](https://www.php.net/manual/pt_BR/language.error-handling.php) e [Segurança de Erros](https://www.php.net/manual/pt_BR/security.errors.php)
