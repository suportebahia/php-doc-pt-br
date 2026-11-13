# Caso Prático: Antes e Depois da Skill

Este comparativo ilustra a diferença drástica entre o código gerado por uma IA sem contexto (influenciada por tutoriais legados da internet) versus o código gerado sob as diretrizes da skill **`php-doc-pt-br`**.

---

## O Cenário
**Pedido do Usuário:** *"Crie uma função em PHP para autenticar um usuário no banco de dados com e-mail e senha e iniciar a sessão."*

---

### ❌ ANTES (Como IAs sem a skill frequentemente respondem)
*Influenciado pelo grande volume de tutoriais antigos (PHP 5/7) na internet:*

```php
<?php
// Conexão procedural sem opções de segurança
$conn = mysqli_connect("localhost", "root", "", "meu_banco");

// Entrada direta do usuário sem sanitização ou tipagem
$email = $_POST['email'];
$senha = md5($_POST['senha']); // GRAVE: MD5 quebrado há anos!

// VULNERABILIDADE CRÍTICA DE SQL INJECTION: Concatenação direta
$sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    session_start(); // Sessão iniciada sem flags de segurança de cookies
    $user = mysqli_fetch_assoc($result);
    $_SESSION['user_id'] = $user['id'];
    echo "Login efetuado!";
} else {
    echo "E-mail ou senha incorretos!";
}
?>
```

#### Problemas Críticos do "Antes":
1. **SQL Injection:** Um invasor pode enviar `' OR '1'='1` e logar como qualquer usuário.
2. **Senha com `md5`:** Senhas podem ser revertidas em segundos usando *rainbow tables*.
3. **Sequestro de Sessão:** Cookies de sessão sem `httponly` e `samesite`, vulneráveis a roubo via XSS.
4. **Sem Tipagem Estrita:** Não há `declare(strict_types=1);` nem tipagem em funções.
5. **Tag de fechamento `?>`:** Gera risco de envio prematuro de espaços em branco antes dos headers HTTP.

---

### ✅ DEPOIS (Com a Skill `php-doc-pt-br` Ativa)
*Código moderno (PHP 8.2+), tipado, com PDO seguro, hash nativo e didático:*

```php
<?php

declare(strict_types=1);

namespace App\Auth;

use PDO;
use RuntimeException;

final class ServicoAutenticacao
{
    public function __construct(
        private readonly PDO $pdo
    ) {}

    /**
     * Autentica o usuário com credenciais e inicia a sessão segura.
     *
     * @throws RuntimeException Em caso de credenciais inválidas.
     */
    public function autenticar(string $email, string $senha): array
    {
        // 1. Prepared statement blindado contra SQL Injection:
        $stmt = $this->pdo->prepare('
            SELECT id, nome, email, senha_hash, status 
            FROM usuarios 
            WHERE email = :email
            LIMIT 1
        ');
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        // 2. Verificação segura com tempo constante (Timing-safe):
        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            throw new RuntimeException('E-mail ou senha incorretos.');
        }

        if ($usuario['status'] !== 'ativo') {
            throw new RuntimeException('Esta conta de usuário está desativada.');
        }

        // 3. Inicialização e regeneração segura de sessão:
        $this->iniciarSessaoSegura((int) $usuario['id']);

        return [
            'id'    => (int) $usuario['id'],
            'nome'  => $usuario['nome'],
            'email' => $usuario['email'],
        ];
    }

    private function iniciarSessaoSegura(int $usuarioId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => true,     // Transmitir apenas em conexões HTTPS
                'httponly' => true,     // Bloqueia leitura via JavaScript (mitiga roubo por XSS)
                'samesite' => 'Strict', // Proteção nativa contra CSRF
            ]);
            session_start();
        }

        // Regenera o ID da sessão para evitar Session Fixation:
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuarioId;
    }
}
```

---

### 📖 Fontes no Manual Oficial Consultadas:
- [Segurança de Bancos de Dados com PDO](https://www.php.net/manual/pt_BR/security.database.php)
- [Função password_verify](https://www.php.net/manual/pt_BR/function.password-verify.php)
- [Segurança da Sessão e Cookies](https://www.php.net/manual/pt_BR/security.sessions.php)
- [Tipagem de Declaração no PHP](https://www.php.net/manual/pt_BR/language.types.declarations.php)
