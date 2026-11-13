# Banco de Dados com PDO

Padrão arquitetural e de segurança para acesso a bancos de dados relacionais utilizando a extensão nativa **PDO (PHP Data Objects)**.

---

## 1. Conexão Resiliente e Configuração Recomendada
Sempre configure os atributos obrigatórios de tratamento de erros, emulação e fetch padrão logo na instanciação:

```php
<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

final class Conexao
{
    private static ?PDO $instancia = null;

    public static function obter(): PDO
    {
        if (self::$instancia === null) {
            $dsn = 'mysql:host=localhost;dbname=meu_banco;charset=utf8mb4';
            $usuario = 'usuario_db';
            $senha = 'senha_secreta';

            $opcoes = [
                // Lança PDOException em qualquer falha de SQL:
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Retorna arrays associativos puros por padrão:
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Garante prepared statements reais no servidor do banco:
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instancia = new PDO($dsn, $usuario, $senha, $opcoes);
            } catch (PDOException $e) {
                // Nunca exponha $e->getMessage() com senhas para o usuário final
                throw new \RuntimeException('Falha ao conectar com o banco de dados.', 0, $e);
            }
        }

        return self::$instancia;
    }
}
```
- Fonte: [PDO::__construct](https://www.php.net/manual/pt_BR/pdo.construct.php)

---

## 2. Consultas Parametrizadas Seguras (*Prepared Statements*)
Nunca insira variáveis dentro da string SQL. Use marcadores nomeados (`:param`) ou posicionais (`?`):

### Inserção com obtenção de ID gerado:
```php
$stmt = $pdo->prepare('
    INSERT INTO usuarios (nome, email, senha_hash, criado_em)
    VALUES (:nome, :email, :senha_hash, NOW())
');

$stmt->execute([
    ':nome'       => $nome,
    ':email'      => $email,
    ':senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
]);

$idCriado = (int) $pdo->lastInsertId();
```

### Consulta com múltiplos resultados:
```php
$stmt = $pdo->prepare('SELECT id, nome, email FROM usuarios WHERE status = :status ORDER BY nome ASC');
$stmt->execute([':status' => 'ativo']);

// Itera sem carregar todo o resultado em memória se houver muitos registros:
while ($usuario = $stmt->fetch()) {
    echo $usuario['nome'];
}
```
- Fonte: [PDO::prepare](https://www.php.net/manual/pt_BR/pdo.prepare.php)

---

## 3. Transações Atômicas (ACID)
Para operações que envolvem mais de uma tabela ou múltiplos passos que precisam ser concluídos com sucesso absoluto ou revertidos:

```php
try {
    $pdo->beginTransaction();

    // 1. Debitar da conta de origem
    $stmtDebito = $pdo->prepare('UPDATE contas SET saldo = saldo - :valor WHERE id = :id AND saldo >= :valor');
    $stmtDebito->execute([':valor' => $valor, ':id' => $contaOrigemId]);

    if ($stmtDebito->rowCount() === 0) {
        throw new \DomainException('Saldo insuficiente ou conta de origem inexistente.');
    }

    // 2. Creditar na conta de destino
    $stmtCredito = $pdo->prepare('UPDATE contas SET saldo = saldo + :valor WHERE id = :id');
    $stmtCredito->execute([':valor' => $valor, ':id' => $contaDestinoId]);

    // Confirma todas as operações:
    $pdo->commit();
} catch (\Throwable $e) {
    // Reverte em caso de qualquer exceção ou erro:
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    throw $e;
}
```
- Fonte: [Transações PDO](https://www.php.net/manual/pt_BR/pdo.transactions.php)
