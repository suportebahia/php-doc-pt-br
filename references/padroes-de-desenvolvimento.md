# Padrões de desenvolvimento PHP

Este guia reúne recomendações gerais. Adapte-as ao PHP mínimo, às dependências, ao framework e às convenções do projeto. Consulte as especificações PHP-FIG quando precisar de regras normativas, como PSR-12.

## Tipagem e declaração strict_types

Em código novo, tipos de parâmetros, propriedades e retornos tornam os contratos mais claros e ajudam ferramentas de análise estática. Escolha `mixed`, uniões e tipos anuláveis quando representarem corretamente o domínio; não adicione tipos apenas para satisfazer uma regra mecânica.

`declare(strict_types=1);` pode ser uma boa escolha para arquivos PHP novos. O seu efeito é local ao arquivo e relaciona-se sobretudo a chamadas de funções feitas a partir desse arquivo; não transforma automaticamente toda a aplicação em um sistema de tipos estrito. Ao alterar um sistema existente, considere a compatibilidade e os testes.

Como a diretiva afeta chamadas originadas no arquivo que a declara, confirme qual arquivo faz a chamada quando investigar coerção. Argumentos nomeados foram introduzidos no PHP 8.0 e usam os nomes dos parâmetros; mudar esses nomes em APIs públicas pode afetar chamadores que os utilizem. Essa preocupação é especialmente relevante ao implementar contratos compartilhados.

```php
<?php

declare(strict_types=1);

function dividir(int|float $dividendo, int|float $divisor): float
{
    if ($divisor == 0) {
        throw new InvalidArgumentException('O divisor não pode ser zero.');
    }

    return $dividendo / $divisor;
}
```

Use comparação estrita (`===`) sempre que a comparação pretendida for também de tipo. Para valores numéricos aceitando `int` ou `float`, defina a regra de domínio e compare os valores de forma apropriada; não copie `=== 0` para todos os casos sem considerar os tipos recebidos.

- Fonte: [Declarações de tipo](https://www.php.net/manual/pt_BR/language.types.declarations.php)

## Tags PHP

Em arquivos que contêm apenas PHP, omitir a tag de fechamento `?>` é uma convenção útil para evitar bytes acidentais depois dela. Em arquivos mistos de PHP e HTML, siga a estrutura apropriada para esse formato.

- Fonte: [Tags PHP](https://www.php.net/manual/pt_BR/language.basic-syntax.phptags.php)

## Estilo e interoperabilidade

Aplique o padrão de estilo já usado pelo projeto. Se não houver um, PSR-12 é uma escolha comum; confirme a versão e o escopo da especificação PHP-FIG. Ferramentas como formatadores ajudam a manter consistência, mas não substituem decisões de design.

Declare a visibilidade das propriedades e dos métodos de forma explícita quando isso tornar o contrato mais claro. Use nomenclatura consistente com o projeto, inclusive quando a convenção diferir de exemplos genéricos.

- Fonte: [PHP-FIG: PSR-12](https://www.php-fig.org/psr/psr-12/)
- Fonte: [Visibilidade](https://www.php.net/manual/pt_BR/language.oop5.visibility.php)

## Fluxo e tratamento de erros

Retornos antecipados podem reduzir aninhamento, mas não são obrigatórios em todo método. Use exceções quando uma condição excepcional deva ser propagada; capture-as apenas quando houver uma ação útil, como converter a falha em resposta apropriada ou adicionar contexto. Evite capturar e ignorar erros.

Em produção, configure a exibição e o registro de erros de acordo com o ambiente, sem expor detalhes internos ao usuário. Proteja os logs e evite incluir segredos ou dados pessoais desnecessários.

- Fonte: [Tratamento de erros e exceções](https://www.php.net/manual/pt_BR/language.error-handling.php)
- Fonte: [Segurança e tratamento de erros](https://www.php.net/manual/pt_BR/security.errors.php)
