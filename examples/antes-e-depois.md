# Exemplo didático: autenticação e acesso ao banco

O primeiro trecho contém padrões que não devem ser reutilizados. O segundo mostra algumas correções para PHP 8.2+, mas permanece parcial: não é uma implementação pronta para produção nem uma auditoria de segurança.

## Trecho com problemas

```php
$email = $_POST['email'];
$senha = md5($_POST['senha']);
$sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
$resultado = mysqli_query($conexao, $sql);
```

O valor de entrada é incorporado à instrução SQL, o que pode permitir injeção SQL conforme o contexto. MD5 não é apropriado para armazenar senhas, e `$_POST` deve ser validado antes de ser usado. `mysqli` por si só não é inseguro: a API oferece consultas preparadas, mas o trecho acima não as usa.

## Exemplo parcial com parâmetros e API de senha

```php
<?php

declare(strict_types=1);

function autenticar(PDO $pdo, string $email, string $senha): ?array
{
    $stmt = $pdo->prepare(
        'SELECT id, nome, email, senha_hash FROM usuarios WHERE email = :email'
    );
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!is_array($usuario) || !password_verify($senha, $usuario['senha_hash'])) {
        return null;
    }

    return [
        'id' => (int) $usuario['id'],
        'nome' => (string) $usuario['nome'],
        'email' => (string) $usuario['email'],
    ];
}
```

A função verifica credenciais e devolve dados necessários ao fluxo, mas não autentica uma sessão sozinha. O chamador ainda precisa estabelecer estado autenticado depois de confirmar a identidade, regenerar o ID no ponto correto e aplicar autorização em cada operação. A política de exclusão da sessão anterior depende do handler, da concorrência e do fluxo; não copie `session_regenerate_id(true)` como regra universal.

Antes de iniciar a sessão, configure opções compatíveis com o ambiente. O manual recomenda `session.use_strict_mode`; em produção com HTTPS, use cookies seguros e teste atributos, CSRF, expiração, login, elevação de privilégio e logout. Proteja ainda contra tentativas automatizadas e não revele detalhes internos ao cliente.

Este trecho não mostra validação de entrada, resposta HTTP, limitação de tentativas, tratamento de falhas, conexão configurada nem autorização. Ele serve somente para ilustrar prepared statements e as funções nativas de hash de senha.

- [Prepared statements do PDO](https://www.php.net/manual/pt_BR/pdo.prepared-statements.php)
- [Funções de senha](https://www.php.net/manual/pt_BR/book.password.php)
- [Segurança de sessões](https://www.php.net/manual/pt_BR/security.sessions.php)
- [Parâmetros de segurança da sessão](https://www.php.net/manual/pt_BR/session.security.ini.php)
