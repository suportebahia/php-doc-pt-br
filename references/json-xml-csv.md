# JSON, XML e CSV

Escolha o parser conforme o contrato e o formato esperado. Limite o tamanho antes de processar dados não confiáveis, trate erros de parsing e valide estrutura e regras de domínio. Parser bem-sucedido não significa que os dados sejam válidos para a aplicação.

## JSON

Use `json_decode()` com `JSON_THROW_ON_ERROR` e profundidade adequada. Decodificar em objetos (`associative: false`) pode ajudar a distinguir objeto de lista no nível superior; escolha deliberadamente como representar valores ausentes, `null`, inteiros grandes e números.

A implementação selecionada [`decodeJsonObject()`](../examples/json-object-decoder.php) rejeita JSON malformado e valores de topo que não sejam objetos. O CI executa apenas um teste funcional específico dessa implementação, em processo PHP separado; não executa arbitrariamente blocos de Markdown nem as demais fixtures. O chamador ainda deve limitar o tamanho do corpo e validar schema e regras de domínio.

No baseline PHP 8.2, `json_validate()` não está disponível nativamente. Em PHP 8.3+, ela pode verificar sintaxe sem produzir o valor, mas não valida o schema nem o domínio.

- Fontes: [json_decode](https://www.php.net/manual/pt_BR/function.json-decode.php), [novidades PHP 8.3](https://www.php.net/releases/8.3/en.php)

## XML

XML pode incluir entidades e referências externas. Não habilite expansão de entidades para dados não confiáveis sem compreender o comportamento exato do parser/libxml e as proteções aplicadas. Opções seguras variam por parser e versão; mantenha dependências atualizadas e teste referências locais e de rede em ambiente isolado. Não confie somente numa expressão regular para validar XML.

- Fontes: [extensão libxml](https://www.php.net/manual/pt_BR/book.libxml.php), [`libxml_set_external_entity_loader`](https://www.php.net/manual/pt_BR/function.libxml-set-external-entity-loader.php)

## CSV

CSV não tem um único dialeto universal. Defina separador, delimitador de texto e escape explicitamente conforme o produtor/consumidor; trate codificação, quebras de linha, colunas ausentes e campos longos. Teste round-trip com os dados reais.

```php
<?php

declare(strict_types=1);

$handle = fopen($caminho, 'rb');
if ($handle === false) {
    throw new RuntimeException('Não foi possível abrir o CSV.');
}

try {
    while (($linha = fgetcsv($handle, null, ',', '"', '')) !== false) {
        processarLinha($linha);
    }
} finally {
    fclose($handle);
}
```

Quando CSV é aberto por planilhas, valores iniciados por caracteres interpretados como fórmula podem ser executados pelo aplicativo consumidor. `fputcsv()` produz um formato CSV, mas não neutraliza universalmente esse risco. A política de exportação depende do consumidor e precisa de testes próprios.

- Fontes: [`fgetcsv`](https://www.php.net/manual/pt_BR/function.fgetcsv.php), [`fputcsv`](https://www.php.net/manual/pt_BR/function.fputcsv.php), [OWASP CSV Injection](https://owasp.org/www-community/attacks/CSV_Injection)
