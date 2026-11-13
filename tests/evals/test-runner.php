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
    [$missingCommandCode] = runProcess([PHP_BINARY, $runner, '--live']);
    check($missingCommandCode === 2, 'modo live exige configuração explícita do executável');

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
} catch (Throwable $exception) {
    fwrite(STDERR, "Falha no teste do runner: {$exception->getMessage()}\n");
    exit(1);
}

exit(0);
