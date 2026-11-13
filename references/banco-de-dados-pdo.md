# Acesso a banco de dados com PDO

Exemplos de uso do PDO para projetos que já o adotam. PDO é uma abstração de acesso a bancos disponível no PHP; não é obrigatório para todos os projetos. Considere a arquitetura existente e a documentação do driver usado.

## Configuração da conexão

Guarde credenciais fora do repositório e injete-as por configuração segura do ambiente. Escolha atributos compatíveis com o driver e com o comportamento necessário; nenhum atributo isolado garante a segurança de todas as consultas.

```php
<?php

declare(strict_types=1);

$pdo = new PDO($dsn, $usuario, $senha, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
```

Consulte a documentação do driver antes de definir `PDO::ATTR_EMULATE_PREPARES`. A emulação pode afetar tipos e comportamento; desativá-la pode ser apropriado em alguns drivers, mas não substitui o uso correto de parâmetros.

- Fonte: [PDO::__construct](https://www.php.net/manual/pt_BR/pdo.construct.php)

## Consultas parametrizadas

Separe os valores da instrução SQL com placeholders vinculados. Não use placeholders para nomes de tabelas ou colunas; se esses identificadores forem dinâmicos, selecione-os a partir de uma lista permitida.

```php
$stmt = $pdo->prepare(
    'SELECT id, nome, email FROM usuarios WHERE email = :email'
);
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch();
```

Prepared statements ajudam a proteger contra injeção de valores quando usados corretamente. Ainda é necessário validar autorização, regras de negócio e partes dinâmicas da consulta.

- Fontes: [PDO::prepare](https://www.php.net/manual/pt_BR/pdo.prepare.php), [prepared statements](https://www.php.net/manual/pt_BR/pdo.prepared-statements.php)

## Transações

Use transações quando vários passos precisarem ser confirmados ou revertidos em conjunto, e confirme se o mecanismo de armazenamento do banco suporta transações para as tabelas usadas. Trate falhas com rollback quando a transação ainda estiver ativa.

```php
$pdo->beginTransaction();
try {
    $debit = $pdo->prepare(
        'UPDATE contas SET saldo = saldo - :valor WHERE id = :id AND saldo >= :valor'
    );
    $debit->execute(['valor' => $valor, 'id' => $contaOrigemId]);

    if ($debit->rowCount() !== 1) {
        throw new DomainException('Não foi possível debitar a conta de origem.');
    }

    $credit = $pdo->prepare(
        'UPDATE contas SET saldo = saldo + :valor WHERE id = :id'
    );
    $credit->execute(['valor' => $valor, 'id' => $contaDestinoId]);

    if ($credit->rowCount() !== 1) {
        throw new DomainException('Não foi possível creditar a conta de destino.');
    }
    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    throw $exception;
}
```

A semântica de `rowCount()` pode variar conforme a consulta e o driver. Ajuste as verificações ao banco e aos requisitos de concorrência da aplicação.

- Fonte: [Transações PDO](https://www.php.net/manual/pt_BR/pdo.transactions.php)
