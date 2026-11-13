# Armadilhas Frequentes e Boas Práticas no PHP

Guia de prevenção contra erros lógicos clássicos e comportamentos contra-intuitivos documentados no manual do PHP.

---

## 1. Comparação Estrita (`===`) vs Comparação Frouxa (`==`)
A comparação frouxa (`==`) realiza coerção de tipos silenciosa, o que frequentemente causa vulnerabilidades de autenticação e lógica:

```php
// Armadilha clássica:
0 == 'texto'      // No PHP 7 era true! No PHP 8 é false, mas ainda perigoso
'0e123' == '0e456' // true (ambos interpretados como notação científica 0 * 10^x = 0)

// Padrão obrigatório:
// SEMPRE use comparação estrita (=== e !==):
if ($senhaHash === $hashEsperado) { ... }
```
- Fonte: [Operadores de Comparação](https://www.php.net/manual/pt_BR/language.operators.comparison.php)

---

## 2. A Pegadinha do `empty()` com a String `"0"`
A função nativa `empty()` considera valores falsos como vazios: `""`, `0`, `0.0`, `null`, `false`, `[]` e **a string `"0"`**.

- Se um usuário preencher um campo de formulário com o número zero (`"0"`), `empty("0")` retornará `true`!
- **Como resolver:**
  ```php
  // Inadequado:
  if (empty($_POST['quantidade'])) { ... } // Rejeitará o valor legítimo "0"

  // Recomendado:
  if (!isset($_POST['quantidade']) || $_POST['quantidade'] === '') {
      // Realmente vazio
  }
  ```
- Fonte: [empty](https://www.php.net/manual/pt_BR/function.empty.php)

---

## 3. Retorno Falso em Funções de Busca (`strpos` vs `str_contains`)
Funções como `strpos()` retornam a posição do caractere (índice `0` para o início da string) ou `false` se não encontrar:

```php
$texto = "PHP moderno";

// Inadequado:
if (strpos($texto, "PHP") == false) {
    // ERRO! strpos retorna 0, e 0 == false avalia como true!
}

// Padrão PHP 8+ recomendado:
if (str_contains($texto, "PHP")) {
    // Retorna booleano puro (true/false) de forma inequívoca
}

// Se precisar da posição numérica:
if (strpos($texto, "PHP") !== false) {
    // Encontrado corretamente
}
```
- Fonte: [str_contains](https://www.php.net/manual/pt_BR/function.str-contains.php) e [strpos](https://www.php.net/manual/pt_BR/function.strpos.php)

---

## 4. `isset()` vs `array_key_exists()`
- `isset($array['chave'])`: Retorna `false` se a chave não existir **ou** se o valor for explicitamente `null`.
- `array_key_exists('chave', $array)`: Retorna `true` se a chave existir no array, mesmo que seu valor armazenado seja `null`.

```php
$config = ['usuario' => null];

isset($config['usuario']);            // false
array_key_exists('usuario', $config); // true
```
- Fonte: [array_key_exists](https://www.php.net/manual/pt_BR/function.array-key-exists.php) e [isset](https://www.php.net/manual/pt_BR/function.isset.php)

---

## 5. Precisão de Ponto Flutuante (`float`)
Valores numéricos de ponto flutuante têm imprecisão inerente de representação binária IEEE 754. **Nunca** use `float` para cálculos financeiros ou de moeda:

```php
// Inadequado:
$a = 0.1 + 0.2; // 0.30000000000000004
if ($a == 0.3) { ... } // Pode falhar!

// Padrão recomendado:
// 1. Armazene valores monetários em centavos (inteiros: 100 para R$ 1,00)
// 2. Ou utilize a extensão nativa BCMath para aritmética de precisão arbitrária:
$resultado = bcadd('0.1', '0.2', 2); // '0.30'
```
- Fonte: [Números de Ponto Flutuante](https://www.php.net/manual/pt_BR/language.types.float.php) e [BCMath](https://www.php.net/manual/pt_BR/book.bc.php)
