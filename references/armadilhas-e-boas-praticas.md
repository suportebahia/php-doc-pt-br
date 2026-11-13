# Armadilhas e boas práticas em PHP

Exemplos de comportamentos que merecem atenção. Escolha a operação conforme os tipos e as regras de domínio do projeto; uma recomendação geral não substitui a definição do contrato.

## Comparação estrita e comparação frouxa

`==` pode converter tipos antes de comparar; `===` também exige que os tipos coincidam. Para identificadores, tokens e valores cujo tipo faz parte do contrato, a comparação estrita costuma ser a escolha mais clara. Não a aplique cegamente a números de tipos diferentes ou a valores de ponto flutuante sem definir a regra pretendida.

```php
$tokenRecebido = 'abc123';
$tokenEsperado = 'abc123';

if ($tokenRecebido === $tokenEsperado) {
    // Os dois valores e tipos coincidem.
}
```

- Fonte: [Operadores de comparação](https://www.php.net/manual/pt_BR/language.operators.comparison.php)

## `empty()` e a string `"0"`

`empty()` considera vazios valores como `0`, `false`, `null`, `''`, `[]` e a string `'0'`. Se `'0'` for uma entrada válida, verifique presença e conteúdo de acordo com o formato esperado:

```php
if (!isset($_POST['quantidade']) || $_POST['quantidade'] === '') {
    // O campo está ausente ou vazio; valide o tipo/formato separadamente.
}
```

- Fonte: [empty](https://www.php.net/manual/pt_BR/function.empty.php)

## Posição encontrada igual a zero

`strpos()` retorna um índice, que pode ser `0`, ou `false` quando não encontra. Compare explicitamente com `false` se precisar do índice. `str_contains()` é opção a partir do PHP 8.0 quando só interessa saber se existe ocorrência.

```php
if (strpos($texto, 'PHP') !== false) {
    // Encontrado, inclusive quando está no início.
}
```

- Fontes: [strpos](https://www.php.net/manual/pt_BR/function.strpos.php), [str_contains](https://www.php.net/manual/pt_BR/function.str-contains.php)

## `isset()` e `array_key_exists()`

`isset($array['chave'])` retorna `false` quando a chave está ausente ou o valor é `null`. `array_key_exists('chave', $array)` distingue chave ausente de chave presente com valor `null`.

- Fontes: [isset](https://www.php.net/manual/pt_BR/function.isset.php), [array_key_exists](https://www.php.net/manual/pt_BR/function.array-key-exists.php)

## Valores monetários e `float`

Valores de ponto flutuante são aproximações binárias. Para dinheiro, evite depender de igualdade exata ou somas repetidas com `float` sem uma política de arredondamento. Unidades inteiras menores, tipos decimais do banco ou aritmética decimal podem ser opções; escolha considerando moeda, escala, arredondamento, limites e ferramentas disponíveis.

- Fontes: [Números de ponto flutuante](https://www.php.net/manual/pt_BR/language.types.float.php), [BCMath](https://www.php.net/manual/pt_BR/book.bc.php)
