<?php

declare(strict_types=1);

$runner = __DIR__ . '/run-evals.php';
$casesDirectory = __DIR__ . '/cases';

/** @return array{int, string} */
function runProcess(array $command): array
{
    $process = proc_open(
        $command,
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes,
        null,
        null,
        ['bypass_shell' => true]
    );
    if (!is_resource($process)) {
        throw new RuntimeException('Não foi possível iniciar o processo de teste.');
    }
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    return [proc_close($process), $output];
}

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
    echo "✓ {$message}\n";
}

try {
    $runLiveRegressionTests = getenv('CI') !== 'true';
    if ($runLiveRegressionTests) {
        [$missingCommandCode] = runProcess([PHP_BINARY, $runner, '--live']);
        check($missingCommandCode === 2, 'modo live exige configuração explícita do executável');
    }

    $functionalTests = [
        'decoder JSON' => [
            'path' => __DIR__ . '/../functional/json-object-decoder.php',
            'success' => 'Testes funcionais do decoder JSON passaram.',
        ],
        'validação estrita de datas' => [
            'path' => __DIR__ . '/../functional/strict-date-validation.php',
            'success' => 'Teste funcional de validação estrita de datas passou.',
        ],
        'preg_match' => [
            'path' => __DIR__ . '/../functional/preg-match-result.php',
            'success' => 'Teste funcional de preg_match passou.',
        ],
    ];
    foreach ($functionalTests as $name => $functionalTest) {
        [$functionalCode, $functionalOutput] = runProcess([PHP_BINARY, $functionalTest['path']]);
        check(
            $functionalCode === 0 && str_contains($functionalOutput, $functionalTest['success']),
            "o teste funcional {$name} allowlistado passa em processo PHP separado"
        );
    }

    $casePath = $casesDirectory . '/zz-runner-path-' . bin2hex(random_bytes(6)) . '.json';
    $pathTraversalCase = [
        'id' => 'teste-path-traversal',
        'titulo' => 'Teste de contenção de caminho',
        'prompt' => 'Teste',
        'fixture' => '../README.md',
        'must_contain' => ['x'],
        'must_not_contain' => [],
        'patterns_not_match' => [],
    ];
    $written = file_put_contents($casePath, json_encode($pathTraversalCase, JSON_THROW_ON_ERROR));
    check($written !== false, 'fixture temporária de traversal criada');
    try {
        [$traversalCode, $traversalOutput] = runProcess([PHP_BINARY, $runner, '--validate-only']);
        check(
            $traversalCode !== 0 && str_contains($traversalOutput, 'Fixture deve ser apenas um nome de arquivo PHP'),
            'caminho de fixture fora da pasta permitida é rejeitado'
        );
    } finally {
        if (is_file($casePath)) {
            unlink($casePath);
        }
    }

    $snippetPath = dirname(__DIR__, 2) . '/docs/.snippet-linter-' . bin2hex(random_bytes(6)) . '.md';
    $snippetContent = "```php\n<?php\necho 'NAO_EXECUTAR_SNIPPET';\n```\n\n```php\n<?php\n\$broken = ;\n```\n";
    $snippetWritten = file_put_contents($snippetPath, $snippetContent);
    check($snippetWritten !== false, 'fixture temporária de snippets criada');
    try {
        [$snippetCode, $snippetOutput] = runProcess([PHP_BINARY, $runner]);
        check(
            $snippetCode !== 0
                && str_contains($snippetOutput, 'Sintaxe PHP inválida')
                && !str_contains($snippetOutput, 'NAO_EXECUTAR_SNIPPET'),
            'sintaxe de snippets é verificada sem executar o código documentado'
        );
    } finally {
        if (is_file($snippetPath)) {
            unlink($snippetPath);
        }
    }

    if ($runLiveRegressionTests) {
        $marker = sys_get_temp_dir() . '/php-doc-pt-br-shell-' . bin2hex(random_bytes(6));
        $commandJson = json_encode(
            [PHP_BINARY, '-r', 'echo $argv[1];', 'texto literal; touch ' . $marker],
            JSON_THROW_ON_ERROR
        );
        [$liveCode] = runProcess([
            PHP_BINARY,
            $runner,
            '--live',
            '--case=api-rest-json',
            '--agent-command-json=' . $commandJson,
        ]);
        $markerCreated = file_exists($marker);
        if ($markerCreated) {
            unlink($marker);
        }
        check($liveCode === 1 && !$markerCreated, 'metacaracteres são passados como argumento literal, sem execução pelo shell');

        $largeOutputCommand = json_encode(
            [PHP_BINARY, '-r', 'fwrite(STDOUT, str_repeat("o", 700000)); fwrite(STDERR, str_repeat("e", 400000));'],
            JSON_THROW_ON_ERROR
        );
        [$outputLimitCode, $outputLimitText] = runProcess([
            PHP_BINARY,
            $runner,
            '--live',
            '--case=api-rest-json',
            '--agent-command-json=' . $largeOutputCommand,
        ]);
        check(
            $outputLimitCode === 1 && str_contains($outputLimitText, 'O agente excedeu o limite de saída de 1 MiB.'),
            'stdout e stderr compartilham um limite de saída de 1 MiB'
        );

        $timeoutMarker = sys_get_temp_dir() . '/php-doc-pt-br-timeout-' . bin2hex(random_bytes(6));
        $timeoutCommand = json_encode(
            [PHP_BINARY, '-r', 'usleep(2500000); file_put_contents($argv[1], "alive");', $timeoutMarker],
            JSON_THROW_ON_ERROR
        );
        try {
            [$timeoutCode, $timeoutText] = runProcess([
                PHP_BINARY,
                $runner,
                '--live',
                '--case=api-rest-json',
                '--timeout-seconds=1',
                '--agent-command-json=' . $timeoutCommand,
            ]);
            usleep(1_700_000);
            check(
                $timeoutCode === 1
                    && str_contains($timeoutText, 'O agente excedeu o limite de 1 segundo.')
                    && !file_exists($timeoutMarker),
                'o processo live é encerrado ao atingir o timeout reduzido'
            );
        } finally {
            if (is_file($timeoutMarker)) {
                unlink($timeoutMarker);
            }
        }
    }
} catch (Throwable $exception) {
    fwrite(STDERR, "Falha no teste do runner: {$exception->getMessage()}\n");
    exit(1);
}

exit(0);
