# APIs REST e JSON no PHP

Exemplos introdutórios para PHP 8.2+. Ajuste método HTTP, autenticação, validação, respostas e política de erros ao contrato real da API e ao framework do projeto. O manual do PHP descreve as funções da linguagem; a semântica HTTP deve ser verificada também na especificação e na documentação usadas pela API.

## Receber e decodificar JSON

O corpo de uma requisição JSON é normalmente lido de `php://input`. Limite o tamanho aceito conforme o endpoint e valide o tipo e as regras de domínio depois da decodificação. Para ler JSON uma única vez e tratar sintaxe inválida em PHP 8.2, use `json_decode()` com `JSON_THROW_ON_ERROR`:

```php
<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

$corpo = file_get_contents('php://input');
if ($corpo === false || trim($corpo) === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Corpo da requisição ausente.'], JSON_THROW_ON_ERROR);
    return;
}

try {
    $dados = json_decode($corpo, associative: true, flags: JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    http_response_code(400);
    echo json_encode(['erro' => 'JSON malformado.'], JSON_THROW_ON_ERROR);
    return;
}

// Confirme aqui o formato e as regras esperadas pelo endpoint.
if (!is_array($dados) || !isset($dados['nome']) || !is_string($dados['nome'])) {
    http_response_code(422);
    echo json_encode(['erro' => 'Dados não aceitos.'], JSON_THROW_ON_ERROR);
    return;
}

http_response_code(200);
echo json_encode(['recebido' => true], JSON_THROW_ON_ERROR);
```

JSON sintaticamente inválido costuma ser tratado como `400 Bad Request`; conteúdo sintaticamente válido que não satisfaz as regras do contrato pode ser `422 Unprocessable Content`. Escolha os códigos de acordo com a API existente.

`json_validate()` existe nativamente a partir do PHP 8.3 e serve para verificar a sintaxe sem produzir o valor decodificado. Se o endpoint precisa do valor, normalmente basta decodificar com `JSON_THROW_ON_ERROR`; validar e depois decodificar pode analisar o texto duas vezes. Em PHP 8.2, não chame a função nativa `json_validate()` sem uma implementação compatível.

- Fontes: [json_decode](https://www.php.net/manual/pt_BR/function.json-decode.php), [anúncio oficial do PHP 8.3](https://www.php.net/releases/8.3/en.php)

## Cabeçalhos e CORS

Defina o `Content-Type` apropriado. CORS só é necessário quando navegadores de outra origem precisam acessar a API. Não o adicione automaticamente: mantenha uma lista explícita de origens permitidas e não combine credenciais com `Access-Control-Allow-Origin: *`. Se houver preflight, responda de acordo com os métodos e cabeçalhos que a aplicação realmente aceita.

CORS é uma política de navegador, não autenticação nem autorização. Uma API ainda precisa autenticar e autorizar cada operação conforme o seu modelo de acesso.

## Respostas JSON

Use `json_encode()` e escolha flags conforme o formato e o tratamento de erros pretendidos:

```php
$resposta = json_encode(
    ['sucesso' => true, 'nome' => 'Maria'],
    JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
);
```

Defina o código HTTP e não devolva exceções, rastros ou segredos ao cliente. O formato do erro deve seguir o contrato da API.

- Fontes: [json_encode](https://www.php.net/manual/pt_BR/function.json-encode.php), [http_response_code](https://www.php.net/manual/pt_BR/function.http-response-code.php)

## Consumir APIs com cURL

Defina limites de tempo, verifique erros de transporte e de HTTP, e descodifique a resposta conforme o contrato do serviço. Mantenha a validação de certificado TLS habilitada. Se a URL vier de entrada não confiável, avalie SSRF e restrinja destinos, esquemas e endereços permitidos; não encaminhe tokens a um host arbitrário.

- Fonte: [Extensão cURL](https://www.php.net/manual/pt_BR/book.curl.php)
