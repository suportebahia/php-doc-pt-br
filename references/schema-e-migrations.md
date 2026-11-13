# Schema de banco e migrações

O schema é parte do contrato de dados. Constraints e tipos adequados no banco complementam validações da aplicação. Índices e relacionamentos devem refletir consultas e regras reais; cada escolha tem custo de escrita, armazenamento e manutenção.

## Migrações versionadas

Registre mudanças de schema como migrações ordenadas e revisáveis. Uma sequência comum é: aplicar mudanças compatíveis, atualizar código, migrar dados quando necessário e remover estruturas antigas depois que consumidores deixarem de usá-las. O procedimento exato depende de disponibilidade, volume e SGBD.

Teste a criação em banco vazio e atualização de versões existentes. Simule falha no meio de cada etapa. Uma migração reversível é útil quando tecnicamente possível, mas nem toda mudança de dados ou DDL pode ser revertida sem perda.

## DDL e transações

Não presuma que DDL participa de uma transação como uma alteração DML. Comportamento e bloqueio variam por SGBD, engine e operação. Por exemplo, MySQL pode confirmar implicitamente algumas instruções DDL. Consulte documentação do banco e ferramenta de migração escolhidos e ensaie em cópia representativa.

Considere estratégia expand/contract para mudanças que precisam coexistir com versões antigas e novas da aplicação. Defina lock, tempo de execução, backup/restore, monitoramento e rollback antes do deploy de mudanças de alto risco.

## Ferramentas

Doctrine Migrations e ferramentas de framework são opções, não requisitos universais. Verifique compatibilidade, semântica transacional e suporte ao SGBD usado. Não execute migrações em produção sem revisão, backup e procedimento de recuperação adequados ao impacto.

- Fontes: [transações PDO](https://www.php.net/manual/pt_BR/pdo.transactions.php), [transações MySQL/InnoDB](https://dev.mysql.com/doc/refman/8.4/en/innodb-transaction-isolation-levels.html), [DDL online MySQL](https://dev.mysql.com/doc/refman/8.4/en/innodb-online-ddl.html), [Doctrine Migrations](https://www.doctrine-project.org/projects/doctrine-migrations/en/3.9/reference/migration-classes.html)
