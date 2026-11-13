<?php

declare(strict_types=1);

require_once __DIR__ . '/../../examples/strict-date-validation.php';

use function PhpDocPtBr\Examples\parseStrictIsoDate;

function expectStrictDate(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$date = parseStrictIsoDate('2026-02-28');
expectStrictDate(
    $date->format('Y-m-d') === '2026-02-28' && $date->getTimezone()->getName() === 'UTC',
    'data válida preservada exatamente em UTC'
);

foreach (['2026-02-30', '2026-2-3'] as $invalidDate) {
    try {
        parseStrictIsoDate($invalidDate);
        throw new RuntimeException('data inválida deveria ser rejeitada');
    } catch (InvalidArgumentException) {
        // Datas impossíveis ou fora do formato exato são rejeitadas.
    }
}

echo "Teste funcional de validação estrita de datas passou.\n";
