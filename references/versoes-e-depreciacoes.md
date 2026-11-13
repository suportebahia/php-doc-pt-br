# Guia Rápido: Versões e Depreciações do PHP

Referência interna da skill `php-doc-pt-br`.

## PHP 8.0+
- `str_contains()`, `str_starts_with()`, `str_ends_with()`: novas funções nativas.
- Argumentos nomeados: `funcao(parametro: $valor)`.
- `match`: similar ao `switch`, mas retorna valor.

## PHP 8.1+
- Enums: `enum Status { case Active; }`.
- `readonly`: propriedades imutáveis após inicialização.
- `never`: tipo de retorno para funções que sempre lançam exceção.

## PHP 8.2+
- Classes `readonly`.
- Depreciação de propriedades dinâmicas.

## PHP 8.3+
- `json_validate()`: valida JSON sem decodificar.
- Constantes de classe tipadas.

## Funções Depreciadas/Removidas
- `utf8_encode()` / `utf8_decode()`: depreciadas no PHP 8.2. Use `mb_convert_encoding()`.
- `each()`: removida no PHP 8.0. Use `foreach`.
- `create_function()`: removida no PHP 8.0. Use funções anônimas.
- `money_format()`: removida no PHP 8.0. Use `NumberFormatter`.
