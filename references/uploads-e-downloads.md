# Uploads e downloads de arquivos

A segurança de arquivos depende do tipo de dado, do servidor, do armazenamento e de quem pode acessá-los. Os exemplos abaixo são esqueletos que exigem diretório existente, permissões apropriadas e testes no SAPI real.

## Receber um upload

Trate primeiro `$_FILES[*]['error']`; não presuma que todo envio chegou ao destino temporário. Os limites do PHP e do servidor podem rejeitar o corpo antes que a aplicação veja o arquivo.

```php
<?php

declare(strict_types=1);

$arquivo = $_FILES['imagem'] ?? null;
if (!is_array($arquivo)
    || !isset($arquivo['error'], $arquivo['tmp_name'], $arquivo['size'])
    || !is_int($arquivo['error'])
    || !is_string($arquivo['tmp_name'])
    || !is_int($arquivo['size'])) {
    throw new RuntimeException('Upload ausente ou malformado.');
}
if ($arquivo['error'] !== UPLOAD_ERR_OK) {
    throw new RuntimeException('O upload não foi concluído. Código: ' . $arquivo['error']);
}
$limiteBytes = 5_000_000; // Exemplo: alinhe ao contrato e aos limites do servidor.
if ($arquivo['size'] > $limiteBytes) {
    throw new RuntimeException('Arquivo acima do limite aceito.');
}
if (!is_uploaded_file($arquivo['tmp_name'])) {
    throw new RuntimeException('Origem de upload inválida.');
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
$extensoesPermitidas = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
];
if (!is_string($mime) || !isset($extensoesPermitidas[$mime])) {
    throw new RuntimeException('Formato não permitido.');
}

$nomeInterno = bin2hex(random_bytes(16)) . '.' . $extensoesPermitidas[$mime];
$diretorioPrivado = '/caminho/configurado/fora-da-raiz-publica';
$destino = $diretorioPrivado . DIRECTORY_SEPARATOR . $nomeInterno;
if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
    throw new RuntimeException('Não foi possível armazenar o arquivo.');
}
```

A extensão Fileinfo deve estar instalada. MIME é um sinal para uma allowlist restrita, não prova de que o conteúdo é benigno. Para formatos complexos, valide a estrutura ou reprocese em ambiente apropriado. Não use o nome enviado pelo navegador como caminho ou nome final.

`move_uploaded_file()` substitui um arquivo existente no destino. O nome aleatório reduz colisões, mas uma verificação prévia de `file_exists()` isolada não é uma garantia atômica contra corrida. Use armazenamento privado e uma estratégia de criação/identificação adequada ao backend.

- Fontes: [uploads PHP](https://www.php.net/manual/pt_BR/features.file-upload.php), [erros de upload](https://www.php.net/manual/pt_BR/features.file-upload.errors.php), [`move_uploaded_file`](https://www.php.net/manual/pt_BR/function.move-uploaded-file.php), [Fileinfo](https://www.php.net/manual/pt_BR/book.fileinfo.php)

## Limites e autorização

Confira limites de bytes e quantidade no PHP, proxy e servidor. `upload_max_filesize` limita cada arquivo, enquanto `post_max_size` limita o corpo multipart inteiro; não são o mesmo limite. Teste uploads acima e abaixo dos valores reais da implantação e trate códigos de erro individualmente.

Exija autenticação e autorização para o usuário e recurso corretos antes de aceitar, substituir, listar ou remover arquivos. Armazene fora da raiz web quando possível e evite permissões executáveis. Faça limpeza segura de temporários e defina retenção.

## Downloads

Associe um identificador opaco a um registro autorizado no servidor. Em cada download, autentique o usuário quando o fluxo exigir identidade e verifique sua autorização para aquele recurso; um identificador opaco não concede acesso. Não aceite um caminho arbitrário do cliente e não concatene entrada para formar um caminho de arquivo. Após autorizar o recurso, abra o arquivo de uma raiz controlada e defina cabeçalhos de tipo e disposição segundo o conteúdo e o contrato.

Para conteúdo não confiável, determine o tipo no servidor e escolha a disposição conforme o produto; quando apropriado, use `Content-Disposition: attachment` e `X-Content-Type-Options: nosniff`. Não reutilize diretamente o nome enviado pelo cliente: gere ou codifique um nome seguro para `Content-Disposition`, teste caracteres especiais e não exponha caminhos físicos. Para arquivos grandes, prefira fluxo/streaming em vez de carregar todo o conteúdo em memória. A autorização, os cabeçalhos efetivamente enviados, o streaming, a política de cache, range requests e expiração dependem da aplicação, do SAPI e do proxy; teste a combinação usada na implantação.

Para contexto de [autenticação e autorização](seguranca-essencial.md#senhas-autenticação-e-autorização), [sessões e CSRF](seguranca-essencial.md#sessões-e-csrf) e [processamento de uploads](#receber-um-upload) / [orientações de segurança para uploads](seguranca-essencial.md#uploads-e-ssrf), consulte também o guia de segurança.

- Fonte adicional: [OWASP File Upload Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html)
