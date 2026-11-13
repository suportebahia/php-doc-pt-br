# APIs RESTful e Manipulação de JSON no PHP

Guia técnico para construção de endpoints REST modernos, serialização segura de dados e consumo de APIs externas utilizando recursos nativos do PHP.

---

## 1. Construção de um Endpoint RESTful
Ao construir uma API em PHP puro ou microframeworks, estabeleça os cabeçalhos de resposta HTTP corretos antes de enviar qualquer corpo:

```php
<?php

declare(strict_types=1);

// 1. Cabeçalho obrigatório indicando conteúdo JSON:
header('Content-Type: application/json; charset=UTF-8');

// 2. Cabeçalhos de CORS (Cross-Origin Resource Sharing) se consumido por SPAs/Apps:
header('Access-Control-Allow-Origin: https://meuapp.com');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 3. Tratamento de requisições OPTIONS (Pre-flight CORS):
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit;
}
```

---

## 2. Leitura Segura do Corpo da Requisição (JSON Payload)
Em requisições `POST`, `PUT` ou `PATCH`, o payload JSON não vem na superglobal `$_POST`. Ele deve ser lido do stream de entrada `php://input`:

```php
// 1. Ler o corpo bruto da requisição:
$corpoBruto = file_get_contents('php://input');

if ($corpoBruto === false || trim($corpoBruto) === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Corpo da requisição ausente.'], JSON_THROW_ON_ERROR);
    exit;
}

// 2. Validação nativa com json_validate() (PHP 8.3+):
if (!json_validate($corpoBruto)) {
    http_response_code(422); // Unprocessable Content
    echo json_encode([
        'erro'    => 'JSON malformado ou inválido.',
        'detalhe' => json_last_error_msg()
    ], JSON_THROW_ON_ERROR);
    exit;
}

// 3. Decodificação segura:
try {
    $dados = json_decode($corpoBruto, associative: true, flags: JSON_THROW_ON_ERROR);
} catch (\JsonException $e) {
    http_response_code(400);
    echo json_encode(['erro' => 'Falha ao processar JSON.'], JSON_THROW_ON_ERROR);
    exit;
}
```
- Fontes: [json_validate](https://www.php.net/manual/pt_BR/function.json-validate.php) e [json_decode](https://www.php.net/manual/pt_BR/function.json-decode.php)

---

## 3. Serialização e Respostas JSON Padronizadas
Ao serializar dados para JSON, use as flags nativas que garantem integridade de caracteres acentuados, barras de URLs e tratamento de erros:

```php
function responderJson(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    
    // JSON_UNESCAPED_UNICODE: Mantém acentuação sem escapar como \u00e1
    // JSON_UNESCAPED_SLASHES: Não escapa barras / em URLs (ex: https://)
    // JSON_THROW_ON_ERROR: Lança JsonException caso haja erro de serialização
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    exit;
}

// Exemplo de resposta bem-sucedida (201 Created):
responderJson(201, [
    'sucesso' => true,
    'mensagem' => 'Registro criado com êxito.',
    'dados'    => ['id' => 42, 'nome' => 'Maria Silva']
]);
```
- Fonte: [json_encode](https://www.php.net/manual/pt_BR/function.json-encode.php) e [http_response_code](https://www.php.net/manual/pt_BR/function.http-response-code.php)

---

## 4. Códigos de Status HTTP Essenciais

| Código | Significado | Quando Usar |
| :--- | :--- | :--- |
| **200 OK** | Sucesso | Consultas (`GET`) ou atualizações bem-sucedidas. |
| **201 Created** | Criado | Criação com sucesso de um novo recurso (`POST`). |
| **204 No Content** | Sem Conteúdo | Exclusão bem-sucedida (`DELETE`) ou pre-flight `OPTIONS`. |
| **400 Bad Request** | Requisição Inválida | Sintaxe inválida ou falta de parâmetros essenciais. |
| **401 Unauthorized** | Não Autenticado | Token JWT / Bearer ausente ou inválido. |
| **403 Forbidden** | Proibido | Usuário autenticado, mas sem permissão para este recurso. |
| **404 Not Found** | Não Encontrado | O registro solicitado não existe no banco. |
| **422 Unprocessable** | Entidade Não Processável | Falha de validação de regras de negócio (ex: e-mail já em uso). |
| **500 Internal Error** | Erro Interno | Falha não esperada no servidor (banco fora do ar, etc.). |

---

## 5. Consumindo APIs Externas com cURL Nativo
Para integrar sua aplicação PHP com outros serviços ou microsserviços externos de forma rápida e segura:

```php
<?php

declare(strict_types=1);

function chamarApiExterna(string $url, string $token): array
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'User-Agent: MeuAppPHP/1.0',
        ],
        // Segurança obrigatória: verificar certificado SSL/TLS
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $resposta = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erro = curl_error($ch);
    curl_close($ch);

    if ($resposta === false) {
        throw new \RuntimeException("Erro de rede na requisição cURL: {$erro}");
    }

    if ($httpCode >= 400) {
        throw new \RuntimeException("A API externa retornou o status HTTP {$httpCode}");
    }

    return json_decode((string) $resposta, associative: true, flags: JSON_THROW_ON_ERROR);
}
```
- Fonte: [Extensão cURL](https://www.php.net/manual/pt_BR/book.curl.php)
