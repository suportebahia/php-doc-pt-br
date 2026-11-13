# Acesso MySQL com MySQLi

MySQLi é uma API PHP para MySQL. Ela não é um requisito para todos os projetos e não é intercambiável com PDO; escolha conforme arquitetura e dependências existentes. A extensão `mysqli` e o servidor MySQL/MariaDB compatível precisam estar disponíveis.

## Conexão e prepared statements

Defina charset e modo de erro de forma explícita para evitar depender de defaults de versões diferentes. Use prepared statements para separar valores da estrutura SQL.

```php
<?php

declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($host, $usuario, $senha, $banco, $porta);
$mysqli->set_charset('utf8mb4');

$stmt = $mysqli->prepare('SELECT id, nome FROM usuarios WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
```

Não concatene valores externos em SQL. Placeholders não representam nomes de colunas/tabelas; para identificadores dinâmicos, use allowlist. Confirme compatibilidade de `get_result()` e mysqlnd no ambiente ou use o mecanismo de resultado documentado para a instalação.

## Transação

Transações dependem de engine e SGBD. No MySQL, confirme que as tabelas usam engine transacional, como InnoDB. DDL pode causar commit implícito; não presuma que toda migração seja reversível pela transação.

```php
<?php

declare(strict_types=1);

$mysqli->begin_transaction();
try {
    $stmt = $mysqli->prepare('UPDATE pedidos SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $novoStatus, $pedidoId);
    $stmt->execute();

    if ($stmt->affected_rows !== 1) {
        throw new DomainException('Pedido não encontrado ou não alterado.');
    }
    $mysqli->commit();
} catch (Throwable $erro) {
    $mysqli->rollback();
    throw $erro;
}
```

`affected_rows` representa resultado da operação segundo o driver/configuração e não substitui a regra de domínio nem o controle de concorrência. Trate deadlocks conforme a documentação MySQL; repetir a transação inteira só é seguro quando seus efeitos são idempotentes.

- Fontes: [prepared statements MySQLi](https://www.php.net/manual/pt_BR/mysqli.quickstart.prepared-statements.php), [transações MySQLi](https://www.php.net/manual/pt_BR/mysqli.quickstart.transactions.php), [modo de erros MySQLi](https://www.php.net/manual/pt_BR/mysqli-driver.report-mode.php), [deadlocks MySQL](https://dev.mysql.com/doc/refman/8.4/en/innodb-deadlocks-handling.html)
