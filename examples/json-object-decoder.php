<?php

declare(strict_types=1);

namespace PhpDocPtBr\Examples;

/**
 * Decodifica um JSON cujo valor de topo precisa ser um objeto.
 *
 * O chamador deve limitar o tamanho do corpo e validar schema e regras de domínio.
 */
function decodeJsonObject(string $body): \stdClass
{
    try {
        $value = json_decode($body, false, 64, JSON_THROW_ON_ERROR);
    } catch (\JsonException $exception) {
        throw new \InvalidArgumentException('JSON inválido.', previous: $exception);
    }

    if (!$value instanceof \stdClass) {
        throw new \InvalidArgumentException('Era esperado um objeto JSON.');
    }

    return $value;
}
