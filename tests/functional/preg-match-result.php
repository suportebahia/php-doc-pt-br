<?php

declare(strict_types=1);

require_once __DIR__ . '/../../examples/preg-match-result.php';

use function PhpDocPtBr\Examples\matchReferenceCode;

function expectPregMatch(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

expectPregMatch(matchReferenceCode('AB123456') === 1, 'código correspondente retorna 1');
expectPregMatch(matchReferenceCode('AB12345X') === 0, 'código sem correspondência retorna 0');

$warnings = [];
set_error_handler(static function (int $severity, string $message) use (&$warnings): bool {
    $warnings[] = [$severity, $message];
    return true;
});

try {
    $errorResult = matchReferenceCode("\xFF");
    $errorCode = preg_last_error();
} finally {
    restore_error_handler();
}

expectPregMatch(
    $errorResult === false && $errorCode === PREG_BAD_UTF8_ERROR,
    'entrada UTF-8 inválida produz false e código de erro PCRE'
);

echo "Teste funcional de preg_match passou.\n";
