# Aplicações web PHP e HTTP

Este guia separa três camadas: semântica do protocolo HTTP, APIs/SAPI do PHP e configuração do servidor. O método e o status pertencem ao contrato HTTP; funções como `header()` e `http_response_code()` são APIs PHP; limites de upload e timeouts também dependem do servidor e da implantação.

## Entrada da requisição

Selecione a fonte de entrada conforme o método e o `Content-Type`: `$_GET` representa parâmetros de query; `$_POST` cobre formulários urlencoded e multipart; `$_FILES` contém metadados de upload; `php://input` expõe o corpo bruto, por exemplo JSON. Não use `$_REQUEST` como substituto neutro, pois sua composição depende de configuração e mistura origens diferentes.

Todos esses dados podem ser controlados pelo cliente. Verifique método, estrutura, tipo, limite e regra de domínio antes de usá-los. Autenticação não substitui autorização sobre o recurso solicitado.

- Fontes: [`$_POST`](https://www.php.net/manual/pt_BR/reserved.variables.post.php), [`$_REQUEST`](https://www.php.net/manual/pt_BR/reserved.variables.request.php), [upload de arquivos](https://www.php.net/manual/pt_BR/features.file-upload.php)

## Resposta HTTP

Defina status e cabeçalhos antes de enviar corpo ou saída. `header()` não deve ser chamada depois que bytes já foram enviados. Defina `Content-Type` de forma coerente com o corpo e não exponha mensagens internas.

```php
<?php

declare(strict_types=1);

http_response_code(303);
header('Location: /pedidos');
exit;
```

Use o status apropriado ao contrato. Por exemplo, 405 indica método não permitido e deve anunciar métodos aceitos quando aplicável. Uma resposta a `HEAD`, ou status como 204/304, não deve carregar conteúdo conforme a semântica HTTP; verifique o comportamento observável da aplicação/servidor.

Cookies também são enviados em cabeçalhos. Configure-os antes da saída e de acordo com HTTPS, domínio, caminho, expiração e fluxo do produto. Consulte o guia de segurança para sessões, autorização e CSRF.

- Fontes: [`header`](https://www.php.net/manual/pt_BR/function.header.php), [`http_response_code`](https://www.php.net/manual/pt_BR/function.http-response-code.php), [`setcookie`](https://www.php.net/manual/pt_BR/function.setcookie.php), [RFC 9110](https://www.rfc-editor.org/rfc/rfc9110.html)

## Formulários, sessões e servidor

O corpo de uma requisição pode ter limites em mais de uma camada. `post_max_size`, `upload_max_filesize`, limites do proxy e do servidor não são intercambiáveis. Para APIs JSON, aplique um limite no código antes de decodificar e configure também os limites anteriores do servidor.

Configure a sessão antes de iniciá-la. O cookie deve refletir o ambiente HTTPS e o modelo de autenticação; `HttpOnly`, `Secure` e `SameSite` têm funções diferentes. SameSite não é controle de autorização e não substitui automaticamente CSRF.

Não assuma que Apache, PHP-FPM, servidor embutido e plataforma gerenciada têm as mesmas diretivas ou ciclo de processo. Registre a SAPI e teste o fluxo através da configuração que será implantada.

- Veja também [APIs e JSON](apis-rest-e-json.md), [uploads e downloads](uploads-e-downloads.md), [segurança](seguranca-essencial.md) e [runtime/CLI/FPM](runtime-cli-fpm.md).
