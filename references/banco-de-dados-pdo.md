# Acesso a bancos de dados com PDO

PDO é uma API de acesso a bancos disponível no PHP. Use-a quando fizer sentido para a arquitetura existente; MySQLi, DBAL e ORMs são opções distintas, não camadas intercambiáveis por definição. Confira o driver, o SGBD, as extensões instaladas e a versão mínima do projeto.

## Conexão e consultas

Mantenha credenciais fora do código e do repositório. Configure explicitamente o tratamento de erros e o modo de busca que a aplicação espera. A disponibilidade e o comportamento de atributos variam por driver.

```php
<?php

declare(strict_types=1);

$pdo = new PDO($dsn, $usuario, $senha, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
```

Use placeholders para valores. Eles não servem para nomes de tabelas, colunas, direções de ordenação ou palavras-chave. Para esses identificadores dinâmicos, escolha entre opções permitidas pelo código.

```php
$stmt = $pdo->prepare(
    'SELECT id, nome, email FROM usuarios WHERE email = :email'
);
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch();
```

Em preparação nativa, cada ocorrência de valor deve ter um marcador apropriado; não reutilize um placeholder nomeado várias vezes na mesma consulta se precisar de portabilidade entre modos e drivers. Confira `PDO::ATTR_EMULATE_PREPARES` na documentação do driver: sua configuração afeta o comportamento, mas não substitui a vinculação correta.

- Fontes: [PDO::prepare](https://www.php.net/manual/pt_BR/pdo.prepare.php), [prepared statements](https://www.php.net/manual/pt_BR/pdo.prepared-statements.php), [erros do PDO](https://www.php.net/manual/pt_BR/pdo.error-handling.php)

## Transações e concorrência

Use transações para operações que precisam ser confirmadas ou revertidas em conjunto. Confirme se o driver, o SGBD e o mecanismo das tabelas oferecem a semântica esperada. Níveis de isolamento, locks, comportamento de DDL e deadlocks dependem do SGBD; uma transação não resolve sozinha todas as disputas concorrentes.

O exemplo seguinte é apenas um esqueleto para uma transferência em uma tabela transacional. Usa centavos inteiros para evitar `float` em valores monetários e placeholders distintos. A aplicação ainda deve autorizar a operação, verificar limites do domínio e definir a política de idempotência. `rowCount()` deve ser interpretado conforme o driver e o contrato testado.

```php
<?php

declare(strict_types=1);

function transferir(PDO $pdo, int $origemId, int $destinoId, int $valorCentavos): void
{
    if ($valorCentavos <= 0 || $origemId === $destinoId) {
        throw new InvalidArgumentException('Dados da transferência inválidos.');
    }

    $pdo->beginTransaction();
    try {
        $debito = $pdo->prepare(
            'UPDATE contas
             SET saldo_centavos = saldo_centavos - :valor_debito
             WHERE id = :origem_id AND saldo_centavos >= :saldo_minimo'
        );
        $debito->execute([
            'valor_debito' => $valorCentavos,
            'origem_id' => $origemId,
            'saldo_minimo' => $valorCentavos,
        ]);
        if ($debito->rowCount() !== 1) {
            throw new DomainException('Conta de origem inexistente ou sem saldo suficiente.');
        }

        $credito = $pdo->prepare(
            'UPDATE contas SET saldo_centavos = saldo_centavos + :valor_credito WHERE id = :destino_id'
        );
        $credito->execute([
            'valor_credito' => $valorCentavos,
            'destino_id' => $destinoId,
        ]);
        if ($credito->rowCount() !== 1) {
            throw new DomainException('Conta de destino inexistente.');
        }

        $pdo->commit();
    } catch (Throwable $erro) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $erro;
    }
}
```

Essa amostra não define lock/isolation, autorização, limites máximos, auditoria, retry após deadlock nem idempotência de uma solicitação repetida. Esses requisitos precisam ser desenhados e testados com o SGBD e o domínio reais. Não repita automaticamente a operação inteira se ela puder produzir efeitos externos não idempotentes.

`rowCount()` pode ter diferenças entre drivers e configurações; não o trate como uma garantia universal nem o use para consultas `SELECT` sem confirmar a documentação específica. DDL pode confirmar implicitamente uma transação em alguns SGBDs.

- Fontes: [transações PDO](https://www.php.net/manual/pt_BR/pdo.transactions.php), [rowCount](https://www.php.net/manual/pt_BR/pdostatement.rowcount.php), [SQL injection e banco de dados](https://www.php.net/manual/pt_BR/security.database.sql-injection.php)

## Migrações, erros e abstrações

Versione a estrutura do banco por migrações revisáveis. Teste a criação em banco vazio e a atualização a partir de uma versão anterior. O rollback de DDL não é garantido por todos os SGBDs. Considere constraints e índices no banco, além de validações da aplicação.

Decida entre PDO/MySQLi, DBAL e ORM com base nas necessidades do projeto, não por uma regra universal. Não devolva SQL, credenciais ou mensagens internas do driver a clientes. Registre contexto suficiente para investigar o erro sem incluir segredos ou dados pessoais desnecessários.

- Veja também [schema e migrações](schema-e-migrations.md) e [testes automatizados](testes-e-analise-estatica.md).
