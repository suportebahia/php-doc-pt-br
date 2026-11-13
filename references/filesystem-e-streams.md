# Filesystem e streams em PHP

Funções de I/O podem falhar por permissões, espaço, rede, caminho ou limite de recursos. Verifique retornos, feche recursos e defina se os dados são texto ou bytes. O uso seguro depende também do sistema de arquivos, do sistema operacional e do armazenamento remoto.

## Leitura limitada

Para arquivos que podem crescer, não leia todo o conteúdo sem um limite. Leia em blocos e trate o retorno de `fopen()`/`fread()`; o exemplo abaixo não valida o formato nem define a codificação.

```php
<?php

declare(strict_types=1);

$handle = fopen($caminho, 'rb');
if ($handle === false) {
    throw new RuntimeException('Não foi possível abrir o arquivo.');
}

try {
    while (!feof($handle)) {
        $bloco = fread($handle, 8192);
        if ($bloco === false) {
            throw new RuntimeException('Falha durante a leitura.');
        }
        processarBloco($bloco);
    }
} finally {
    fclose($handle);
}
```

Uma leitura em blocos só mantém o uso de memória limitado se `processarBloco()` também não acumular todos os dados. Se o tamanho esperado for pequeno e conhecido, funções como `file_get_contents()` podem ser adequadas; confira falha e aplique um limite quando a entrada não for confiável.

## Escrita e arquivos temporários

Verifique o número de bytes escritos por `file_put_contents()` e trate `false` como falha. Considere permissões mínimas, diretório controlado, limpeza e política de sobrescrita. `flock()` é um lock consultivo: só coordena processos que respeitam o mesmo lock e não substitui transação/coordenação distribuída.

Use arquivos temporários do sistema ou storage próprio quando apropriado. Não gere nomes finais com dados de usuário e não presuma que a operação de renomear tem as mesmas garantias em todos os volumes/hosts.

## Streams e wrappers

Streams permitem APIs de leitura e escrita uniformes sobre arquivos, streams `php://` e wrappers de protocolos. Um wrapper remoto não é um arquivo local. Restrinja esquema e host, use contexto explícito e não passe URLs construídas de entrada não confiável a funções de I/O. `allow_url_fopen` e extensões disponíveis podem mudar o comportamento.

- Fontes: [filesystem](https://www.php.net/manual/pt_BR/book.filesystem.php), [wrappers](https://www.php.net/manual/pt_BR/wrappers.php), [contextos de streams](https://www.php.net/manual/pt_BR/stream.contexts.php), [`file_put_contents`](https://www.php.net/manual/pt_BR/function.file-put-contents.php), [`flock`](https://www.php.net/manual/pt_BR/function.flock.php)

## Downloads e uploads

Use o guia [uploads e downloads](uploads-e-downloads.md) para tratar `$_FILES`, armazenamento privado, autorização e streaming de arquivos grandes. Para mensagens HTTP, a abstração de corpo de PSR-7 pode representar streams sem manter todo o conteúdo na memória, mas sua aplicação depende da biblioteca adotada.

- Fonte: [PHP-FIG PSR-7](https://www.php-fig.org/psr/psr-7/)
