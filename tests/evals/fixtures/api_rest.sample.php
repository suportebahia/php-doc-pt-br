<?php

declare(strict_types=1);

namespace App\Api;

class EndpointProduto
{
    public function responder(string $payloadJson): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (!json_validate($payloadJson)) {
            http_response_code(422);
            echo json_encode(['erro' => 'JSON inválido'], JSON_THROW_ON_ERROR);
            return;
        }

        echo json_encode(['status' => 'sucesso'], JSON_THROW_ON_ERROR);
    }
}
