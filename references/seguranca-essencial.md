# Guia essencial de segurança em PHP

Este guia reúne controles frequentes; não é uma auditoria completa nem uma garantia de que uma aplicação esteja protegida. A solução depende do framework, da configuração do servidor, do modelo de ameaças e do fluxo real. Diferencie comportamento documentado, recomendação de segurança e suposição sobre o ambiente.

## Comece pelo modelo de ameaças

Para cada fluxo sensível, identifique os ativos, quem pode interagir com eles, as fronteiras de confiança, o que pode dar errado, os controles escolhidos e como serão testados. Reavalie quando arquitetura, dependências ou exposição mudarem. Um checklist geral não substitui essa análise.

## SQL, saída HTML e dados externos

Vincule valores em consultas preparadas de PDO, MySQLi ou da abstração já usada pelo projeto. Placeholders representam valores, não nomes de tabelas, colunas ou palavras-chave. Para identificadores dinâmicos, selecione opções de uma allowlist e teste o driver/configuração.

```php
$stmt = $pdo->prepare('SELECT id, nome FROM usuarios WHERE email = :email');
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch();
```

Codifique a saída conforme o contexto. `htmlspecialchars()` com codificação UTF-8 é comum para texto e atributos HTML citados; não é escape universal para JavaScript, CSS, URL ou HTML não confiável.

```php
echo htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
```

Não passe dados não confiáveis a `unserialize()`, mesmo usando `allowed_classes`. Prefira um formato de dados apropriado, como JSON, seguido de validação explícita do tipo, estrutura e regras do domínio.

- Fontes: [SQL injection](https://www.php.net/manual/pt_BR/security.database.sql-injection.php), [htmlspecialchars](https://www.php.net/manual/pt_BR/function.htmlspecialchars.php), [unserialize](https://www.php.net/manual/pt_BR/function.unserialize.php)

## Senhas, autenticação e autorização

Para armazenar senhas, use `password_hash()` e `password_verify()`; considere `password_needs_rehash()` para atualizar parâmetros após uma autenticação válida. Planeje o tamanho do campo conforme o algoritmo. Proteja login e recuperação contra abuso de acordo com o risco, sem revelar desnecessariamente se uma conta existe.

Autenticação confirma identidade; autorização decide se a identidade pode executar uma operação sobre aquele recurso. Verifique autorização em cada requisição e para cada objeto acessado. Não confie em um identificador recebido do cliente como prova de propriedade. Negar por padrão é uma estratégia útil quando compatível com o sistema.

- Fontes: [API de senhas PHP](https://www.php.net/manual/pt_BR/book.password.php), [OWASP Authorization Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html), [OWASP Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html)

## Sessões e CSRF

Configure as opções de sessão antes de `session_start()`. O manual PHP recomenda `session.use_strict_mode`; o valor efetivo pode ser controlado por `php.ini` ou pelo provedor. Em produção com HTTPS, use cookie `Secure`; `HttpOnly` reduz leitura por JavaScript, mas não elimina XSS. `SameSite` ajuda em alguns fluxos, mas não substitui autorização nem uma defesa CSRF apropriada.
Em produção HTTPS, parâmetros equivalentes podem ser definidos no `php.ini`/pool do runtime e precisam ser verificados no SAPI real. Estes valores ilustram opções, não uma configuração universal:

```ini
session.use_strict_mode = 1
session.use_only_cookies = 1
session.cookie_secure = 1
session.cookie_httponly = 1
session.cookie_samesite = Lax
```

`session.cookie_secure = 1` pressupõe HTTPS; trate desenvolvimento local e terminação TLS no proxy conforme a plataforma.

Depois de autenticar ou elevar privilégios, considere a regeneração do identificador. A política de remoção da sessão antiga depende do handler, concorrência e fluxo; `session_regenerate_id(true)` não deve ser apresentado como solução universal para toda situação. Frameworks podem fornecer mecanismos próprios.

Em aplicações autenticadas por cookie, proteja operações que alteram estado com CSRF do framework ou token imprevisível validado no servidor. Não use GET para alterar estado. CORS e `SameSite` são controles diferentes e não substituem a validação do token quando o fluxo precisa dela.

Exemplo mínimo de token sincronizador, supondo que a sessão já tenha sido iniciada com configuração adequada:

```php
<?php

declare(strict_types=1);

if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$tokenEsperado = $_SESSION['csrf_token'];

$tokenRecebido = $_POST['_csrf'] ?? null;
if (!is_string($tokenEsperado)
    || !is_string($tokenRecebido)
    || !hash_equals($tokenEsperado, $tokenRecebido)) {
    http_response_code(403);
    exit('Solicitação não autorizada.');
}

// Execute a operação mutável somente depois de validar sessão e autorização.
```

Inclua o token no formulário usando escape HTML e valide-o em toda operação protegida. Teste token ausente, incorreto e correto. Para APIs com outros modelos de credencial, avalie o risco conforme o modo como os navegadores enviam essas credenciais.

- Fontes: [segurança de sessões PHP](https://www.php.net/manual/pt_BR/security.sessions.php), [parâmetros de segurança da sessão](https://www.php.net/manual/pt_BR/session.security.ini.php), [CSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)

## Uploads e SSRF

Em uploads, verifique códigos de erro e limites. Não confie no nome, extensão ou MIME informados pelo cliente. Inspecione o conteúdo, aplique tipos permitidos para o produto, gere um nome interno e prefira armazenamento sem execução pública. Inspeção MIME não prova que o arquivo é benigno; o destino e a autorização também importam.

Para requisições a destinos informados pelo usuário, trate SSRF como risco separado. A política depende do uso; pode exigir allowlist, bloqueio de redes internas e redirects, controle de DNS e restrições de egress. Teste IPv4, IPv6, loopback, endereços privados, nomes com resolução interna e redirecionamentos conforme o desenho concreto.

- Fontes: [uploads PHP](https://www.php.net/manual/pt_BR/features.file-upload.php), [move_uploaded_file](https://www.php.net/manual/pt_BR/function.move-uploaded-file.php), [OWASP File Upload Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html), [OWASP SSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Server_Side_Request_Forgery_Prevention_Cheat_Sheet.html)

## Segredos, erros, TLS e dependências

Mantenha segredos fora do código, histórico Git e logs; use armazenamento de segredos adequado ao ambiente, acesso mínimo e procedimento de rotação/revogação. Não exponha stack traces nem mensagens internas em respostas públicas. Em produção, configure registro de erros protegido e desative exibição detalhada conforme o SAPI.

Mantenha validação de certificado e nome do host TLS ativadas nos clientes HTTP. Para dependências Composer, revise `composer.lock`, alertas de segurança, plugins e scripts que executam código. Essas práticas não substituem revisão de rede, infraestrutura e threat model.

- Fontes: [segurança de erros PHP](https://www.php.net/manual/pt_BR/security.errors.php), [TLS/cURL](https://www.php.net/manual/pt_BR/curl.constants.php), [Composer audit](https://getcomposer.org/doc/03-cli.md), [Composer allow-plugins](https://getcomposer.org/doc/06-config.md)

## Como validar

Para cada controle, teste um caso permitido e um caso negado. Revise a configuração efetiva no SAPI de produção, dependências, permissões e fronteiras de rede. Lint, links, análise estática e verificações textuais ajudam a encontrar classes de problemas, mas não demonstram que a aplicação inteira está segura.
