<?php

declare(strict_types=1);

namespace PhpDocPtBr\Examples;

/**
 * Converte uma data civil ISO estrita para UTC sem aceitar normalizações.
 *
 * Este helper é puro em relação ao ambiente: não lê arquivos nem acessa serviços.
 */
function parseStrictIsoDate(string $text): \DateTimeImmutable
{
    $date = \DateTimeImmutable::createFromFormat(
        '!Y-m-d',
        $text,
        new \DateTimeZone('UTC')
    );
    $errors = \DateTimeImmutable::getLastErrors();

    if ($date === false
        || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
        || $date->format('Y-m-d') !== $text) {
        throw new \InvalidArgumentException('Data inválida ou fora do formato esperado.');
    }

    return $date;
}
