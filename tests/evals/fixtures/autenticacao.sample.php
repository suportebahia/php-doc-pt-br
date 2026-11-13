<?php

declare(strict_types=1);

namespace App\Auth;

use PDO;
use RuntimeException;

class Autenticador
{
    public function __construct(private readonly PDO $pdo) {}

    public function login(string $email, string $senha): array
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, email, senha_hash FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            throw new RuntimeException('Credenciais inválidas.');
        }

        return $usuario;
    }
}
