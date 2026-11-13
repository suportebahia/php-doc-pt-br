<?php

declare(strict_types=1);

$files = glob(__DIR__ . '/../../references/*.md') ?: [];
$errors = [];

foreach ($files as $file) {
    $contents = file_get_contents($file);
    if ($contents === false) {
        $errors[] = 'Não foi possível ler ' . basename($file);
        continue;
    }

    preg_match_all('~https://www\.php\.net/manual/pt_BR/[^\s)>\]]+~', $contents, $matches);
    foreach ($matches[0] as $url) {
        $url = rtrim($url, '.,;:!?');
        $parts = parse_url($url);
        if (filter_var($url, FILTER_VALIDATE_URL) === false
            || ($parts['scheme'] ?? null) !== 'https'
            || ($parts['host'] ?? null) !== 'www.php.net'
            || !str_starts_with($parts['path'] ?? '', '/manual/pt_BR/')) {
            $errors[] = basename($file) . ': URL fora do formato/domínio esperado: ' . $url;
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, implode("\n", $errors) . "\n");
    exit(1);
}

echo 'Formato e domínio dos links encontrados nas referências estão corretos; não foi verificada a disponibilidade HTTP de cada página.' . PHP_EOL;
