<?php

declare(strict_types=1);

namespace App\Storage;

use finfo;
use RuntimeException;

class UploadSeguro
{
    public function salvar(array $arquivoHttp): string
    {
        if (!isset($arquivoHttp['error']) || $arquivoHttp['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Falha no upload do arquivo.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($arquivoHttp['tmp_name']);

        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            throw new RuntimeException('Tipo de arquivo inválido.');
        }

        $destino = '/uploads/' . bin2hex(random_bytes(16)) . '.bin';
        if (!move_uploaded_file($arquivoHttp['tmp_name'], $destino)) {
            throw new RuntimeException('Não foi possível armazenar o arquivo.');
        }

        return $destino;
    }
}
