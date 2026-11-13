# APIs HTTP e JSON no PHP

Os exemplos deste guia têm como base PHP 8.2. Ajuste método, autenticação, validação, respostas e política de erros ao contrato real da API e ao framework do projeto. A semântica HTTP deve ser conferida na especificação; o comportamento de servidor e SAPI depende do ambiente.

## Receber JSON com limite

`php://input` permite ler o corpo bruto. O exemplo aplica um teto antes de decodificar, mas o proxy/web server também deve limitar o tamanho da requisição. `post_max_size`, limites do servidor e limites do endpoint são camadas diferentes.

```php
<?php

declare(strict_types=1);

function responderJson(int $status, array $payload): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    responderJson(405, ['erro' => 'Método não permitido.']);
}

$tipoConteudo = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '', 2)[0]));
if ($tipoConteudo !== 'application/json') {
    responderJson(415, ['erro' => 'Envie conteúdo application/json.']);
}

$limiteBytes = 1_048_576;
$entrada = fopen('php://input', 'rb');
if ($entrada === false) {
    responderJson(400, ['erro' => 'Não foi possível ler a requisição.']);
}

$corpo = stream_get_contents($entrada, $limiteBytes + 1);
fclose($entrada);
if ($corpo === false) {
    responderJson(400, ['erro' => 'Não foi possível ler a requisição.']);
}
if (strlen($corpo) > $limiteBytes) {
    responderJson(413, ['erro' => 'Corpo acima do limite aceito.']);
}

try {
    $dados = json_decode($corpo, false, 64, JSON_THROW_ON_ERROR);
} catch (JsonException $erro) {
    responderJson(400, ['erro' => 'JSON malformado.']);
}

if (!$dados instanceof stdClass
    || !property_exists($dados, 'nome')
    || !is_string($dados->nome)
    || trim($dados->nome) === '') {
    responderJson(422, ['erro' => 'Dados fora do contrato.']);
}

// Autenticação, autorização e regras de domínio ainda precisam ser aplicadas.
responderJson(200, ['recebido' => true]);
```

O limite no código evita decodificar um corpo maior que o teto definido pelo endpoint, mas não substitui limites anteriores no proxy, servidor e SAPI. Ajuste o valor e a profundidade ao contrato, ao uso esperado e aos recursos do ambiente.

Usar `stdClass` aqui preserva a distinção de um objeto JSON no nível superior. `property_exists()` diferencia uma propriedade presente com valor `null` de uma propriedade ausente; as regras de tipo e domínio devem decidir o que aceitar.

`json_decode()` com `JSON_THROW_ON_ERROR` funciona no baseline PHP 8.2 e converte JSON. `json_validate()` só existe nativamente desde PHP 8.3 e é útil quando se quer verificar sintaxe sem decodificar; validar e depois decodificar o mesmo corpo pode analisá-lo duas vezes.

- Fontes: [json_decode](https://www.php.net/manual/pt_BR/function.json-decode.php), [anúncio do PHP 8.3](https://www.php.net/releases/8.3/en.php), [stream_get_contents](https://www.php.net/manual/pt_BR/function.stream-get-contents.php)

## Status, erros e CORS

`400` para sintaxe inválida e `422` para conteúdo válido que não atende ao contrato são escolhas comuns, não uma regra obrigatória para toda API. Siga o contrato existente. Não devolva exceções, rastros, SQL, tokens ou segredos ao cliente; mantenha detalhes de diagnóstico apenas em logs protegidos.

CORS é uma política aplicada pelo navegador, não autenticação nem autorização. Só configure quando clientes de outras origens precisarem acessar a API. Use origens explícitas e responda ao preflight conforme métodos e cabeçalhos aceitos; não combine credenciais com origem curinga.

Para uma API pública ou interoperável, RFC 9457 (`application/problem+json`) é uma opção de formato de erro. Se adotada, mantenha coerência entre o status HTTP e o campo `status` do documento.

- Fontes: [semântica HTTP, RFC 9110](https://www.rfc-editor.org/rfc/rfc9110.html), [Problem Details, RFC 9457](https://www.rfc-editor.org/rfc/rfc9457.html), [Fetch/CORS](https://fetch.spec.whatwg.org/)

## Consumir APIs com cURL

O erro de transporte (DNS, conexão ou timeout) é diferente de uma resposta HTTP 4xx/5xx. Não interprete `curl_exec()` sem erro de transporte como prova de sucesso de negócio; verifique status, cabeçalhos e corpo conforme o contrato.

```php
<?php

declare(strict_types=1);

// $url precisa vir de configuração/contrato validado, não de destino arbitrário.
$handle = curl_init($url);
if ($handle === false) {
    throw new RuntimeException('Não foi possível inicializar cURL.');
}

$optionsConfiguradas = curl_setopt_array($handle, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);
if (!$optionsConfiguradas) {
    unset($handle);
    throw new RuntimeException('Não foi possível configurar cURL.');
}

$corpoResposta = curl_exec($handle);
if ($corpoResposta === false) {
    $mensagem = curl_error($handle);
    unset($handle);
    throw new RuntimeException('Falha de transporte cURL: ' . $mensagem);
}

$statusHttp = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
unset($handle);

if ($statusHttp < 200 || $statusHttp >= 300) {
    throw new RuntimeException('A API respondeu com status HTTP não aceito.');
}

// Decodifique e valide $corpoResposta conforme o contrato da API.
```

A extensão cURL e algumas opções dependem da instalação de PHP/libcurl. Não desative verificação TLS para contornar erro de certificado. Se ativar redirects, valide cada destino e não encaminhe credenciais a um host diferente sem política explícita.

Retries precisam respeitar a semântica da operação. O fato de uma chamada ter falhado não prova que o servidor não a processou; repita operações não idempotentes apenas com garantia contratual e mecanismo de idempotência apropriado. Limite tentativas e tempo total, e considere `Retry-After` quando aplicável.

## SSRF e integrações recebidas

Se uma URL for controlada total ou parcialmente por entrada não confiável, use uma política de destinos permitidos. Considere esquemas, IPv4 e IPv6, endereços internos, redirects e DNS rebinding; uma validação textual ou resolução DNS feita antes da conexão não basta em todos os cenários. Restrições de egress na rede podem complementar os controles da aplicação.

Para webhooks, valide assinatura sobre os bytes do corpo segundo o provedor e proteja contra replay/duplicatas conforme o contrato. Para SOAP/XML, declare a dependência da extensão e consulte a documentação do parser/cliente adotado.

- Fontes: [extensão cURL](https://www.php.net/manual/pt_BR/book.curl.php), [opções cURL e TLS](https://www.php.net/manual/pt_BR/curl.constants.php), [SSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Server_Side_Request_Forgery_Prevention_Cheat_Sheet.html)
