<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

$corpoBruto = file_get_contents('php://input');
if ($corpoBruto === false || trim($corpoBruto) === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Corpo da requisição ausente.'], JSON_THROW_ON_ERROR);
    return;
}

try {
    // Compatível com PHP 8.2+: valida e decodifica numa única operação.
    $dados = json_decode($corpoBruto, associative: true, flags: JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    http_response_code(400);
    echo json_encode(['erro' => 'JSON inválido.'], JSON_THROW_ON_ERROR);
    return;
}

if (!is_array($dados)) {
    http_response_code(422);
    echo json_encode(['erro' => 'O JSON deve representar um objeto ou array.'], JSON_THROW_ON_ERROR);
    return;
}

echo json_encode(['dados' => $dados], JSON_THROW_ON_ERROR);
