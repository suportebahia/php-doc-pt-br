<?php

declare(strict_types=1);

/**
 * Verificador estático de fixtures e, opcionalmente, respostas de um agente.
 * As verificações de texto não executam o PHP analisado nem provam segurança.
 */
final class SkillEvalRunner
{
    private const LIVE_TIMEOUT_SECONDS = 60;
    private const MAX_OUTPUT_BYTES = 1_048_576;

    private int $passed = 0;
    private int $failed = 0;
    private array $errors = [];

    public function run(array $argv): int
    {
        try {
            $options = $this->parseArguments(array_slice($argv, 1));
            if ($options['live']) {
                $options['agent_command'] = $this->parseAgentCommand(
                    $options['agent_command_json'] ?? getenv('AI_EVAL_COMMAND_JSON') ?: null
                );
            }
        } catch (InvalidArgumentException $exception) {
            fwrite(STDERR, "Erro de argumentos: {$exception->getMessage()}\n");
            return 2;
        }

        echo "\n======================================================\n";
        echo " Verificador de padrões da skill php-doc-pt-br\n";
        echo $options['live']
            ? " Modo: resposta de agente (comando local configurado pelo usuário)\n"
            : ($options['validate_only'] ? " Modo: validação de casos e fixtures\n" : " Modo: fixtures locais (sem executar o código)\n");
        echo "======================================================\n\n";

        $caseFiles = glob(__DIR__ . '/cases/*.json') ?: [];
        sort($caseFiles);
        if ($caseFiles === []) {
            fwrite(STDERR, "Nenhum caso JSON foi encontrado.\n");
            return 1;
        }

        $seenIds = [];
        $matchingCases = 0;
        foreach ($caseFiles as $caseFile) {
            try {
                $contents = file_get_contents($caseFile);
                if ($contents === false) {
                    throw new RuntimeException('não foi possível ler o arquivo');
                }
                $case = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException|RuntimeException $exception) {
                $this->fail("JSON inválido em " . basename($caseFile) . ': ' . $exception->getMessage());
                continue;
            }

            if (!is_array($case) || array_is_list($case)) {
                $this->fail('O caso ' . basename($caseFile) . ' deve conter um objeto JSON.');
                continue;
            }

            $schemaErrors = $this->validateCase($case, basename($caseFile));
            if ($schemaErrors !== []) {
                foreach ($schemaErrors as $error) {
                    $this->fail($error);
                }
                continue;
            }

            if (isset($seenIds[$case['id']])) {
                $this->fail("ID duplicado '{$case['id']}' em " . basename($caseFile));
                continue;
            }
            $seenIds[$case['id']] = true;

            $fixturePath = $this->resolveFixture($case['fixture']);
            if ($fixturePath === null) {
                $this->fail("Fixture inválida ou inexistente para o caso '{$case['id']}'.");
                continue;
            }

            if ($options['validate_only']) {
                $this->passed++;
                echo " ✓ Caso e fixture válidos: {$case['id']}\n";
                continue;
            }

            if ($options['case_filter'] !== null && $case['id'] !== $options['case_filter']) {
                continue;
            }
            $matchingCases++;
            $this->runCase($case, $fixturePath, $options);
        }

        if ($options['case_filter'] !== null && $matchingCases === 0) {
            $this->fail("Nenhum caso corresponde ao filtro '{$options['case_filter']}'.");
        }

        echo "\n------------------------------------------------------\n";
        echo "Resultado: {$this->passed} verificações aprovadas; {$this->failed} falharam.\n";
        if ($this->failed > 0) {
            foreach ($this->errors as $error) {
                echo " - {$error}\n";
            }
            return 1;
        }

        echo "As verificações estáticas passaram. Isto não demonstra ausência de vulnerabilidades.\n";
        return 0;
    }

    private function validateCase(array $case, string $file): array
    {
        $errors = [];
        foreach (['id', 'titulo', 'prompt', 'fixture'] as $field) {
            if (!isset($case[$field]) || !is_string($case[$field]) || trim($case[$field]) === '') {
                $errors[] = "Campo '{$field}' deve ser uma string não vazia em {$file}.";
            }
        }

        if (isset($case['id']) && is_string($case['id'])
            && preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/D', $case['id']) !== 1) {
            $errors[] = "ID deve estar em kebab-case em {$file}.";
        }

        if (isset($case['fixture']) && is_string($case['fixture'])
            && preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9._-]*\.php\z/D', $case['fixture']) !== 1) {
            $errors[] = "Fixture deve ser apenas um nome de arquivo PHP em {$file}.";
        }

        foreach (['must_contain', 'must_not_contain'] as $field) {
            if (!isset($case[$field]) || !is_array($case[$field]) || !array_is_list($case[$field])) {
                $errors[] = "Campo '{$field}' deve ser uma lista em {$file}.";
                continue;
            }
            foreach ($case[$field] as $index => $value) {
                if (!is_string($value) || $value === '') {
                    $errors[] = "Item {$index} de '{$field}' deve ser uma string não vazia em {$file}.";
                }
            }
        }

        if (isset($case['patterns_not_match'])) {
            if (!is_array($case['patterns_not_match']) || !array_is_list($case['patterns_not_match'])) {
                $errors[] = "Campo 'patterns_not_match' deve ser uma lista em {$file}.";
            } else {
                foreach ($case['patterns_not_match'] as $index => $pattern) {
                    if (!is_string($pattern) || $pattern === '' || preg_match($pattern, '') === false) {
                        $errors[] = "Regex inválida no item {$index} de '{$file}'.";
                    }
                }
            }
        }

        return $errors;
    }

    private function resolveFixture(string $fixture): ?string
    {
        if (basename($fixture) !== $fixture) {
            return null;
        }
        $directory = realpath(__DIR__ . '/fixtures');
        $path = $directory === false ? false : realpath($directory . DIRECTORY_SEPARATOR . $fixture);
        if ($directory === false || $path === false || !is_file($path)
            || !str_starts_with($path, $directory . DIRECTORY_SEPARATOR)) {
            return null;
        }
        return $path;
    }

    private function runCase(array $case, string $fixturePath, array $options): void
    {
        echo "[Verificando] {$case['titulo']}...\n";
        if ($options['live']) {
            $code = $this->getLiveResponse($case, $options['agent_command']);
            if ($code === null) {
                $this->fail("O agente não produziu resposta válida para '{$case['id']}'.");
                return;
            }
        } else {
            $code = file_get_contents($fixturePath);
            if ($code === false) {
                $this->fail("Não foi possível ler a fixture de '{$case['id']}'.");
                return;
            }
        }

        foreach ($case['must_contain'] as $needle) {
            $this->assert(str_contains($code, $needle), "{$case['id']}: deve conter '{$needle}'.");
        }
        foreach ($case['must_not_contain'] as $needle) {
            $this->assert(!str_contains($code, $needle), "{$case['id']}: não deve conter '{$needle}'.");
        }
        foreach ($case['patterns_not_match'] ?? [] as $pattern) {
            $result = preg_match($pattern, $code);
            $this->assert(
                $result === 0,
                "{$case['id']}: não coincidiu com o padrão proibido.",
                "{$case['id']}: coincidiu com um padrão proibido ou houve falha de regex."
            );
        }
    }

    private function getLiveResponse(array $case, array $command): ?string
    {
        if (!function_exists('proc_open')) {
            fwrite(STDERR, "proc_open() não está disponível; modo live indisponível.\n");
            return null;
        }

        // O array de argumentos e bypass_shell impedem interpretação por shell.
        // O executável continua a ser escolhido pelo usuário e deve ser confiável.
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = proc_open(
            [...$command, $case['prompt']],
            $descriptors,
            $pipes,
            null,
            null,
            ['bypass_shell' => true]
        );
        if (!is_resource($process)) {
            fwrite(STDERR, "Não foi possível iniciar o executável local configurado.\n");
            return null;
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        $stdout = '';
        $stderr = '';
        $deadline = microtime(true) + self::LIVE_TIMEOUT_SECONDS;
        $exitCode = null;
        $timedOut = false;
        $tooMuchOutput = false;

        while (true) {
            $stdout .= stream_get_contents($pipes[1]) ?: '';
            $stderr .= stream_get_contents($pipes[2]) ?: '';
            if (strlen($stdout) + strlen($stderr) > self::MAX_OUTPUT_BYTES) {
                $tooMuchOutput = true;
                proc_terminate($process, 9);
                break;
            }

            $status = proc_get_status($process);
            if (!$status['running']) {
                $exitCode = $status['exitcode'];
                break;
            }
            if (microtime(true) >= $deadline) {
                $timedOut = true;
                proc_terminate($process, 15);
                usleep(200_000);
                $status = proc_get_status($process);
                if ($status['running']) {
                    proc_terminate($process, 9);
                }
                break;
            }
            usleep(50_000);
        }

        $stdout .= stream_get_contents($pipes[1]) ?: '';
        $stderr .= stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);
        $closeCode = proc_close($process);

        if ($timedOut) {
            fwrite(STDERR, "O agente excedeu o limite de 60 segundos.\n");
            return null;
        }
        if ($tooMuchOutput) {
            fwrite(STDERR, "O agente excedeu o limite de saída de 1 MiB.\n");
            return null;
        }
        if (($exitCode !== 0 && $exitCode !== -1) || ($exitCode === -1 && $closeCode !== 0)) {
            fwrite(STDERR, "O executável local terminou com erro.\n");
            return null;
        }
        return trim($stdout) === '' ? null : $stdout;
    }

    private function parseAgentCommand(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            throw new InvalidArgumentException(
                'Para --live, configure --agent-command-json com um array JSON de argumentos; consulte tests/evals/README.md.'
            );
        }
        try {
            $command = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('AI_EVAL_COMMAND_JSON não contém JSON válido.');
        }
        if (!is_array($command) || !array_is_list($command) || $command === [] || count($command) > 32) {
            throw new InvalidArgumentException('O comando deve ser uma lista JSON com 1 a 32 argumentos.');
        }
        foreach ($command as $argument) {
            if (!is_string($argument) || $argument === '' || str_contains($argument, "\0")) {
                throw new InvalidArgumentException('Cada argumento do comando deve ser uma string não vazia sem byte NUL.');
            }
        }
        return $command;
    }

    private function parseArguments(array $arguments): array
    {
        $options = [
            'validate_only' => false,
            'live' => false,
            'case_filter' => null,
            'agent_command_json' => null,
            'agent_command' => [],
        ];
        foreach ($arguments as $argument) {
            if ($argument === '--validate-only') {
                $options['validate_only'] = true;
            } elseif ($argument === '--live') {
                $options['live'] = true;
            } elseif (str_starts_with($argument, '--case=')) {
                $options['case_filter'] = substr($argument, 7);
            } elseif (str_starts_with($argument, '--agent-command-json=')) {
                $options['agent_command_json'] = substr($argument, strlen('--agent-command-json='));
            } elseif (str_starts_with($argument, '--')) {
                throw new InvalidArgumentException("Opção desconhecida: {$argument}");
            }
        }
        if ($options['validate_only'] && $options['live']) {
            throw new InvalidArgumentException('--validate-only e --live não podem ser usados em conjunto.');
        }
        return $options;
    }

    private function assert(bool $condition, string $successMessage, ?string $failureMessage = null): void
    {
        if ($condition) {
            $this->passed++;
            echo " ✓ {$successMessage}\n";
            return;
        }
        $this->fail($failureMessage ?? $successMessage);
    }

    private function fail(string $message): void
    {
        $this->failed++;
        $this->errors[] = $message;
        echo " ✗ {$message}\n";
    }
}

exit((new SkillEvalRunner())->run($argv));
