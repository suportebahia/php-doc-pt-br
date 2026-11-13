# Exemplo didático: autenticação e acesso ao banco

O primeiro trecho contém padrões que não devem ser reutilizados. O segundo mostra algumas correções para PHP 8.2+, mas é deliberadamente parcial: não é uma implementação pronta para produção nem uma auditoria de segurança.

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
        'SELECT id, nome, email, senha_hash FROM usuarios WHERE email = :email LIMIT 1'
    );
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!is_array($usuario) || !password_verify($senha, $usuario['senha_hash'])) {
        return null;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    session_regenerate_id(true);

    return [
        'id' => (int) $usuario['id'],
        'nome' => (string) $usuario['nome'],
        'email' => (string) $usuario['email'],
    ];
}
```

Este exemplo ilustra parâmetros SQL e verificação de hash com a API nativa. A aplicação ainda precisa integrar a conexão e as opções de cookie de sessão, verificar autorização em cada operação, aplicar limites contra tentativas automatizadas, tratar erros e testar os seus requisitos. A configuração de cookies e CSRF depende do fluxo e do ambiente; não existe uma combinação única adequada a todos os sites.

- [Prepared statements do PDO](https://www.php.net/manual/pt_BR/pdo.prepared-statements.php)
- [Funções de senha](https://www.php.net/manual/pt_BR/book.password.php)
- [Segurança de sessões](https://www.php.net/manual/pt_BR/security.sessions.php)
- [Declarações de tipo](https://www.php.net/manual/pt_BR/language.types.declarations.php)
