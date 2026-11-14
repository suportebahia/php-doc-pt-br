<?php

declare(strict_types=1);

namespace PhpDocPtBr\Examples;

/**
 * Verifica duas letras ASCII maiúsculas e seis dígitos decimais Unicode,
 * preservando 1, 0 ou false de preg_match().
 *
 * O padrão é fixo e revisado; nenhum I/O ou configuração externa é usado.
 *
 * @return 0|1|false
 */
function matchReferenceCode(string $code): int|false
{
    // Com /u, \d aceita dígitos decimais Unicode; [A-Z] permanece ASCII.
    return preg_match('/\A[A-Z]{2}\d{6}\z/u', $code);
}
