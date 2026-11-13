<?php

declare(strict_types=1);

require_once __DIR__ . '/../../examples/json-object-decoder.php';

use function PhpDocPtBr\Examples\decodeJsonObject;

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$object = decodeJsonObject('{"name":"PHP","enabled":true}');
expect($object->name === 'PHP' && $object->enabled === true, 'objeto JSON válido decodificado');

foreach (['[]', 'null', '"texto"', '42'] as $nonObjectJson) {
    try {
        decodeJsonObject($nonObjectJson);
        throw new RuntimeException('valor de topo que não é objeto deveria ser rejeitado');
    } catch (InvalidArgumentException) {
        // O helper deve rejeitar arrays, escalares e null no topo.
    }
}

try {
    decodeJsonObject('{JSON inválido');
    throw new RuntimeException('JSON malformado deveria ser rejeitado');
} catch (InvalidArgumentException $exception) {
    expect($exception->getPrevious() instanceof JsonException, 'erro de parsing preservado como causa');
}

echo "Testes funcionais do decoder JSON passaram.\n";
