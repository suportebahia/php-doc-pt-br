# Guia de Segurança Essencial no PHP

Normas obrigatórias de segurança baseadas no manual oficial do PHP (`https://www.php.net/manual/pt_BR/security.php`).

---

## 1. Prevenção Contra Injeção de SQL (SQL Injection)
- **Regra:** Nunca concatene variáveis de entrada diretamente em strings SQL.
- **Padrão Oficial:** Use **PDO** ou **MySQLi** exclusivamente com consultas preparadas (*Prepared Statements*).
- Desative a emulação de prepared statements para garantir que o driver do banco faça a parametrização nativa:
  ```php
  <?php

  declare(strict_types=1);

  $pdo = new PDO($dsn, $usuario, $senha, [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
  ]);

  $stmt = $pdo->prepare('SELECT id, nome, email FROM usuarios WHERE email = :email AND status = :status');
  $stmt->execute([
      ':email'  => $emailFornecido,
      ':status' => 'ativo',
  ]);
  $usuario = $stmt->fetch();
  ```
- Fonte: [Segurança de Bancos de Dados](https://www.php.net/manual/pt_BR/security.database.php) e [PDO Prepared Statements](https://www.php.net/manual/pt_BR/pdo.prepared-statements.php)

---

## 2. Prevenção Contra Cross-Site Scripting (XSS)
- Qualquer dado exibido no HTML que venha de fontes externas ou entradas de usuários deve ser devidamente escapado utilizando `htmlspecialchars()`:
  ```php
  echo htmlspecialchars($textoDoUsuario, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  ```
- **Por que `ENT_QUOTES`?** Converte tanto aspas duplas quanto simples, evitando escape de atributos HTML.
- **Por que `ENT_SUBSTITUTE`?** Substitui sequências de bytes de codificação inválidas por um caractere de substituição Unicode, em vez de retornar uma string vazia.
- Fonte: [htmlspecialchars](https://www.php.net/manual/pt_BR/function.htmlspecialchars.php)

---

## 3. Armazenamento Seguro de Senhas
- Nunca armazene senhas em texto puro ou utilizando funções de hash rápido (como `md5()` ou `sha1()`).
- O PHP disponibiliza a API nativa de hashing seguro:
  ```php
  // Criação do hash da senha:
  $hashSeguro = password_hash($senhaDigitada, PASSWORD_DEFAULT);

  // Verificação no momento do login:
  if (password_verify($senhaDigitada, $hashArmazenado)) {
      // Senha válida!
      if (password_needs_rehash($hashArmazenado, PASSWORD_DEFAULT)) {
          // Atualiza o hash com novos custos ou algoritmos se necessário
          $novoHash = password_hash($senhaDigitada, PASSWORD_DEFAULT);
          // salvar $novoHash no banco
      }
  }
  ```
- Fonte: [Funções de Hash de Senha](https://www.php.net/manual/pt_BR/book.password.php)

---

## 4. Segurança de Sessões e Cookies
- Configure os parâmetros de cookies de sessão antes de iniciar `session_start()` para mitigar sequestro de sessão (*Session Hijacking*):
  ```php
  session_set_cookie_params([
      'lifetime' => 0,
      'path'     => '/',
      'domain'   => '',
      'secure'   => true,     // Enviar apenas via HTTPS
      'httponly' => true,     // Inacessível via JavaScript (mitiga XSS roubar sessão)
      'samesite' => 'Strict', // Mitiga CSRF (Cross-Site Request Forgery)
  ]);
  session_start();

  // Em logins bem-sucedidos ou elevação de privilégios, regenere o ID de sessão:
  session_regenerate_id(true);
  ```
- Fonte: [Segurança da Sessão](https://www.php.net/manual/pt_BR/security.sessions.php) e [session_set_cookie_params](https://www.php.net/manual/pt_BR/function.session-set-cookie-params.php)

---

## 5. Upload Seguro de Arquivos
- **Validação de Erro:** Verifique se `$_FILES['arquivo']['error'] === UPLOAD_ERR_OK`.
- **MIME Type Real:** Nunca confie no MIME type enviado pelo navegador (`$_FILES['arquivo']['type']`) nem na extensão do arquivo. Inspecione o conteúdo com a extensão nativa `Fileinfo`:
  ```php
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mimeReal = $finfo->file($_FILES['documento']['tmp_name']);

  $mimesPermitidos = ['image/jpeg', 'image/png', 'application/pdf'];
  if (!in_array($mimeReal, $mimesPermitidos, true)) {
      throw new \RuntimeException('Tipo de arquivo não permitido.');
  }
  ```
- **Nome Aleatório:** Gere um nome randômico criptograficamente seguro via `bin2hex(random_bytes(16))` e salve fora do diretório público (*DocumentRoot*).
- Fonte: [Gerenciamento de Envio de Arquivos](https://www.php.net/manual/pt_BR/features.file-upload.php) e [Fileinfo](https://www.php.net/manual/pt_BR/book.fileinfo.php)
