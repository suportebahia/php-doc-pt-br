# Recursos e compatibilidade entre versões do PHP

Esta lista não é exaustiva nem substitui as notas de migração. Confirme cada recurso na documentação oficial e valide o alvo real do projeto. A situação de suporte de uma versão muda com o tempo; consulte [versões suportadas](https://www.php.net/supported-versions.php) antes de tomar decisões de manutenção.

## Exemplos de marcos de versão

- **PHP 8.0:** `str_contains()`, `str_starts_with()`, `str_ends_with()`, argumentos nomeados e expressão `match`.
- **PHP 8.1:** enums, propriedades `readonly` e tipo de retorno `never`.
- **PHP 8.2:** classes `readonly` e depreciação da criação de propriedades dinâmicas em classes que não a permitem.
- **PHP 8.3:** `json_validate()`, constantes de classe tipadas e atributo `#[Override]`.

`json_validate()` está disponível nativamente a partir do PHP 8.3. Para validar e já converter JSON em PHP 8.2, use `json_decode()` com `JSON_THROW_ON_ERROR` e trate `JsonException`; fazer `json_decode()` após `json_validate()` também pode significar analisar o mesmo texto duas vezes.

- Fontes: [Novos recursos do PHP 8.3](https://www.php.net/releases/8.3/en.php), [json_decode](https://www.php.net/manual/pt_BR/function.json-decode.php)

## Funções removidas ou depreciadas

Alguns exemplos históricos:

- `each()` e `create_function()` foram removidas no PHP 8.0.
- `utf8_encode()` e `utf8_decode()` foram depreciadas no PHP 8.2; a alternativa depende da codificação de origem e destino. Não substitua mecanicamente sem conhecer os dados.
- `money_format()` foi removida no PHP 8.0; formatação monetária deve considerar locale e requisitos da aplicação, por exemplo com `NumberFormatter` quando adequado.

Consulte o manual e o guia de migração da versão correspondente antes de atualizar código. Uma função marcada como depreciada pode continuar funcionando numa versão específica, mas não deve ser presumida disponível indefinidamente.
