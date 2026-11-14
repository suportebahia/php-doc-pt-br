<?php

declare(strict_types=1);

/**
 * Valida a estrutura e a atualidade do inventário da matriz de cobertura.
 * Não pesquisa na rede, não executa exemplos e não certifica a qualidade do conteúdo.
 */
final class CoverageMatrixValidator
{
    private const STATUSES = ['coberto', 'parcial', 'ausente', 'desatualizado'];

    /** @return list<string> */
    public function validate(string $root): array
    {
        $errors = [];
        $rootPath = realpath($root);
        if ($rootPath === false) {
            return ['A raiz do repositório não existe.'];
        }

        $matrixPath = $rootPath . '/docs/MATRIZ-COBERTURA.md';
        $matrix = is_file($matrixPath) ? file_get_contents($matrixPath) : false;
        if ($matrix === false) {
            return ['Não foi possível ler docs/MATRIZ-COBERTURA.md.'];
        }
        $matrixHeader = strtok($matrix, "\r\n");
        if (!is_string($matrixHeader)
            || preg_match('/\A<!-- coverage-matrix: verified_on=(\d{4})-(\d{2})-(\d{2}) schema=1 -->\z/', $matrixHeader, $dateMatch) !== 1
            || !checkdate((int) $dateMatch[2], (int) $dateMatch[3], (int) $dateMatch[1])) {
            $errors[] = 'A primeira linha da matriz precisa declarar uma data de verificação válida e o schema suportado.';
        }

        $inventoryHeading = "## Inventário de referências e hashes";
        $inventoryOffset = strpos($matrix, $inventoryHeading);
        if ($inventoryOffset === false) {
            return [...$errors, 'Falta a secção de inventário de referências e hashes.'];
        }
        $coverageText = substr($matrix, 0, $inventoryOffset);

        $referencesDirectory = $rootPath . '/references';
        if (!is_dir($referencesDirectory)) {
            return [...$errors, 'A pasta references/ não existe.'];
        }
        $referencePaths = [];
        foreach (new DirectoryIterator($referencesDirectory) as $entry) {
            if (!$entry->isDot() && $entry->isFile() && $entry->getExtension() === 'md') {
                $referencePaths[] = $entry->getPathname();
            }
        }
        sort($referencePaths, SORT_STRING);
        $actualByRelativePath = [];
        foreach ($referencePaths as $path) {
            if (is_file($path)) {
                $relative = 'references/' . basename($path);
                $actualByRelativePath[$relative] = $path;
            }
        }

        $mappedReferences = [];
        $sectionIds = [];
        $rowCount = 0;
        foreach (explode("\n", $coverageText) as $line) {
            if (!str_starts_with(trim($line), '|')) {
                continue;
            }

            $cells = array_map('trim', explode('|', trim(trim($line), '|')));
            if (count($cells) !== 5 || preg_match('/\A`([A-Za-z0-9._-]+\.php)`\z/', $cells[0], $idMatch) !== 1) {
                continue;
            }

            $rowCount++;
            $sectionId = $idMatch[1];
            if (isset($sectionIds[$sectionId])) {
                $errors[] = "ID de secção duplicado na matriz: {$sectionId}.";
            }
            $sectionIds[$sectionId] = true;

            if (!in_array($cells[2], self::STATUSES, true)) {
                $errors[] = "Estado inválido na linha {$sectionId}: {$cells[2]}.";
            }

            if (preg_match('/\A\[.+\]\((https:\/\/www\.php\.net\/manual\/pt_BR\/[^)]+)\)\z/u', $cells[1], $urlMatch) !== 1) {
                $errors[] = "URL oficial inválido ou ausente na linha {$sectionId}.";
            } else {
                $parts = parse_url($urlMatch[1]);
                if (($parts['host'] ?? null) !== 'www.php.net'
                    || !str_starts_with($parts['path'] ?? '', '/manual/pt_BR/')) {
                    $errors[] = "URL fora do manual oficial pt_BR na linha {$sectionId}.";
                }
            }

            preg_match_all('/`(references\/[A-Za-z0-9._-]+\.md)`/', $cells[3], $matches);
            foreach ($matches[1] as $relative) {
                $mappedReferences[$relative] = true;
                if (!isset($actualByRelativePath[$relative])) {
                    $errors[] = "A matriz aponta para uma referência inexistente: {$relative}.";
                }
            }
        }
        if ($rowCount === 0) {
            $errors[] = 'A matriz não contém linhas de secções reconhecíveis.';
        }

        foreach (array_keys($actualByRelativePath) as $relative) {
            if (!isset($mappedReferences[$relative])) {
                $errors[] = "Referência sem mapeamento temático: {$relative}.";
            }
        }

        $inventoryText = substr($matrix, $inventoryOffset);
        $visibleEntries = [];
        preg_match_all(
            '/^\|\s*`(references\/[A-Za-z0-9._-]+\.md)`\s*\|\s*`([a-f0-9]{64})`\s*\|$/m',
            $inventoryText,
            $visibleMatches,
            PREG_SET_ORDER
        );
        foreach ($visibleMatches as $match) {
            $relative = $match[1];
            if (isset($visibleEntries[$relative])) {
                $errors[] = "Entrada duplicada na tabela visível SHA-256: {$relative}.";
            }
            $visibleEntries[$relative] = $match[2];
        }

        $manifestEntries = [];
        preg_match_all(
            '/^<!-- source-manifest: (references\/[A-Za-z0-9._-]+\.md) sha256=([a-f0-9]{64}) -->$/m',
            $matrix,
            $manifestMatches,
            PREG_SET_ORDER
        );
        foreach ($manifestMatches as $match) {
            $relative = $match[1];
            if (isset($manifestEntries[$relative])) {
                $errors[] = "Entrada duplicada no inventário SHA-256: {$relative}.";
            }
            $manifestEntries[$relative] = $match[2];
        }

        if (array_diff_assoc($visibleEntries, $manifestEntries) !== []) {
            $errors[] = 'Hash visível e marcador do inventário divergem; reveja a tabela da matriz.';
        }
        if (array_diff_assoc($manifestEntries, $visibleEntries) !== []) {
            $errors[] = 'Marcador SHA-256 sem linha correspondente na tabela visível.';
        }
        foreach (array_diff(array_keys($actualByRelativePath), array_keys($visibleEntries)) as $relative) {
            $errors[] = "Referência ausente da tabela visível SHA-256: {$relative}.";
        }
        foreach (array_diff(array_keys($visibleEntries), array_keys($actualByRelativePath)) as $relative) {
            $errors[] = "Tabela visível contém uma referência inexistente: {$relative}.";
        }
        foreach (array_diff(array_keys($actualByRelativePath), array_keys($manifestEntries)) as $relative) {
            $errors[] = "Referência ausente do inventário SHA-256: {$relative}.";
        }
        foreach (array_diff(array_keys($manifestEntries), array_keys($actualByRelativePath)) as $relative) {
            $errors[] = "Inventário contém uma referência inexistente: {$relative}.";
        }
        foreach ($visibleEntries as $relative => $expectedHash) {
            if (!isset($actualByRelativePath[$relative])) {
                continue;
            }
            $actualHash = hash_file('sha256', $actualByRelativePath[$relative]);
            if ($actualHash === false || !hash_equals($expectedHash, $actualHash)) {
                $errors[] = "Hash SHA-256 divergente; reveja a matriz antes de atualizar o inventário: {$relative}.";
            }
        }

        return $errors;
    }

    /** Executa testes sintéticos do verificador em diretório temporário. */
    public static function selfTest(): int
    {
        $base = sys_get_temp_dir() . '/php-doc-coverage-validator-' . getmypid();
        if (file_exists($base)) {
            fwrite(STDERR, "O diretório temporário do teste já existe: {$base}\n");
            return 1;
        }

        $root = $base . '/repo';
        if (!mkdir($root . '/references', 0777, true) || !mkdir($root . '/docs', 0777, true)) {
            fwrite(STDERR, "Não foi possível criar os diretórios temporários do teste.\n");
            return 1;
        }

        $referencePath = $root . '/references/exemplo.md';
        file_put_contents($referencePath, "conteúdo sintético\n");
        $hash = hash_file('sha256', $referencePath);
        if ($hash === false) {
            self::removeTree($base);
            fwrite(STDERR, "Não foi possível calcular o hash do fixture sintético.\n");
            return 1;
        }
        $matrixPath = $root . '/docs/MATRIZ-COBERTURA.md';
        $matrix = self::fixtureMatrix('exemplo.md', $hash);
        file_put_contents($matrixPath, $matrix);

        $validator = new self();
        $errors = $validator->validate($root);
        if ($errors !== []) {
            self::removeTree($base);
            fwrite(STDERR, 'Falhou o caso válido: ' . implode(' | ', $errors) . "\n");
            return 1;
        }
        echo "✓ Matriz sintética válida aprovada.\n";

        $invalidDateMatrix = str_replace('verified_on=2026-11-13', 'verified_on=2026-99-99', $matrix);
        file_put_contents($matrixPath, $invalidDateMatrix);
        $errors = $validator->validate($root);
        if (!self::hasErrorContaining($errors, 'data de verificação válida')) {
            self::removeTree($base);
            fwrite(STDERR, "Falhou o caso de data de verificação impossível.\n");
            return 1;
        }
        echo "✓ Data de verificação impossível é rejeitada.\n";
        file_put_contents($matrixPath, $matrix);

        file_put_contents(
            $matrixPath,
            str_replace("| `references/exemplo.md` | `{$hash}` |", "| `references/exemplo.md` | `" . str_repeat('a', 64) . "` |", $matrix)
        );
        $errors = $validator->validate($root);
        if (!self::hasErrorContaining($errors, 'Hash visível e marcador')) {
            self::removeTree($base);
            fwrite(STDERR, "Falhou o caso de divergência entre hash visível e marcador.\n");
            return 1;
        }
        echo "✓ Hash visível divergente do marcador é rejeitado.\n";
        file_put_contents($matrixPath, $matrix);

        $hiddenReferencePath = $root . '/references/.oculta.md';
        file_put_contents($hiddenReferencePath, "referência oculta sintética\n");
        $errors = $validator->validate($root);
        if (!self::hasErrorContaining($errors, 'sem mapeamento temático: references/.oculta.md')) {
            self::removeTree($base);
            fwrite(STDERR, "Falhou o caso de referência Markdown oculta não mapeada.\n");
            return 1;
        }
        echo "✓ Referência Markdown oculta sem mapeamento é rejeitada.\n";
        unlink($hiddenReferencePath);

        file_put_contents($root . '/references/sem-mapeamento.md', "novo guia sintético\n");
        $errors = $validator->validate($root);
        if (!self::hasErrorContaining($errors, 'sem mapeamento temático')) {
            self::removeTree($base);
            fwrite(STDERR, "Falhou o caso de referência não mapeada.\n");
            return 1;
        }
        echo "✓ Referência nova sem mapeamento é rejeitada.\n";
        unlink($root . '/references/sem-mapeamento.md');

        file_put_contents($referencePath, "conteúdo alterado\n");
        $errors = $validator->validate($root);
        if (!self::hasErrorContaining($errors, 'Hash SHA-256 divergente')) {
            self::removeTree($base);
            fwrite(STDERR, "Falhou o caso de hash desatualizado.\n");
            return 1;
        }
        echo "✓ Referência alterada com hash antigo é rejeitada.\n";

        self::removeTree($base);
        echo "Testes sintéticos do verificador aprovados.\n";
        return 0;
    }

    private static function fixtureMatrix(string $file, string $hash): string
    {
        return "<!-- coverage-matrix: verified_on=2026-11-13 schema=1 -->\n\n"
            . "# Matriz sintética\n\n"
            . "| ID | Secção oficial | Estado | Referências locais | Fundamentação |\n"
            . "|---|---|---|---|---|\n"
            . "| `sample.php` | [Secção](https://www.php.net/manual/pt_BR/sample.php) | coberto | `references/{$file}` | Conteúdo sintético. |\n\n"
            . "## Inventário de referências e hashes\n\n"
            . "| Ficheiro | SHA-256 |\n|---|---|\n"
            . "| `references/{$file}` | `{$hash}` |\n"
            . "<!-- source-manifest: references/{$file} sha256={$hash} -->\n";
    }

    /** @param list<string> $errors */
    private static function hasErrorContaining(array $errors, string $needle): bool
    {
        foreach ($errors as $error) {
            if (str_contains($error, $needle)) {
                return true;
            }
        }
        return false;
    }

    private static function removeTree(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        rmdir($path);
    }
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    if (($argv[1] ?? null) === '--self-test') {
        exit(CoverageMatrixValidator::selfTest());
    }
    if (count($argv) !== 1) {
        fwrite(STDERR, "Uso: php tests/evals/validate-coverage-matrix.php [--self-test]\n");
        exit(2);
    }

    $errors = (new CoverageMatrixValidator())->validate(dirname(__DIR__, 2));
    if ($errors !== []) {
        fwrite(STDERR, implode("\n", $errors) . "\n");
        exit(1);
    }
    echo "Matriz de cobertura válida: todas as referências estão mapeadas e os hashes coincidem.\n";
}
