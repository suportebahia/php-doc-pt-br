<?php

declare(strict_types=1);

/**
 * Test Harness Dinâmico de Avaliação da Skill (AI Evals).
 *
 * Suporta:
 *  1. Validação de Schema: php tests/evals/run-evals.php --validate-only
 *  2. Modo Fixture (padrão): php tests/evals/run-evals.php
 *  3. Filtro por Caso: php tests/evals/run-evals.php --case=autenticacao-segura
 *  4. Modo Live com IA: php tests/evals/run-evals.php --live [--agent-cmd="claude -p"]
 */

final class DynamicSkillEvalRunner
{
    private int $passou = 0;
    private int $falhou = 0;
    private array $erros = [];

    public function executar(array $argv): int
    {
        $opcoes = $this->parseArgumentos($argv);

        echo "\n======================================================\n";
        echo " 🛡️  Suíte de Evals da Skill: php-doc-pt-br\n";
        if ($opcoes['validate_only']) {
            echo " Modo: Validação de Schema dos Casos (JSON)\n";
        } elseif ($opcoes['live']) {
            echo " Modo: LIVE (Execução em Tempo Real com IA)\n";
        } else {
            echo " Modo: Fixtures Canônicas (Offline / CI)\n";
        }
        echo "======================================================\n\n";

        $casosDir = __DIR__ . '/cases';
        $arquivosCasos = glob($casosDir . '/*.json');

        if (empty($arquivosCasos)) {
            echo "❌ Nenhum caso de teste encontrado em {$casosDir}.\n";
            return 1;
        }

        sort($arquivosCasos);

        foreach ($arquivosCasos as $caminhoCaso) {
            $conteudo = (string) file_get_contents($caminhoCaso);
            $dados = json_decode($conteudo, true);

            if (!is_array($dados)) {
                echo "❌ JSON malformado no arquivo: " . basename($caminhoCaso) . "\n";
                $this->falhou++;
                $this->erros[] = "JSON inválido: " . basename($caminhoCaso);
                continue;
            }

            // Validação de Schema Obrigatório:
            $errosSchema = $this->validarSchema($dados, basename($caminhoCaso));
            if (!empty($errosSchema)) {
                foreach ($errosSchema as $erro) {
                    echo "  ✗ [Schema] {$erro}\n";
                    $this->erros[] = $erro;
                    $this->falhou++;
                }
                continue;
            }

            // Se for modo apenas validação de schema:
            if ($opcoes['validate_only']) {
                $this->passou++;
                echo "  ✓ [Schema Válido] " . basename($caminhoCaso) . " ({$dados['id']})\n";
                continue;
            }

            // Filtro por caso específico:
            if ($opcoes['case_filter'] !== null && $dados['id'] !== $opcoes['case_filter']) {
                continue;
            }

            $this->executarCaso($dados, $opcoes);
        }

        $total = $this->passou + $this->falhou;
        echo "\n------------------------------------------------------\n";
        echo "Resultado: {$this->passou}/{$total} asserções/validações aprovadas.\n";

        if ($this->falhou > 0) {
            echo "❌ {$this->falhou} falhas detectadas:\n";
            foreach ($this->erros as $erro) {
                echo "  - {$erro}\n";
            }
            echo "------------------------------------------------------\n";
            return 1;
        }

        echo "✅ Todos os critérios avaliados foram aprovados com sucesso!\n";
        echo "======================================================\n\n";
        return 0;
    }

    private function validarSchema(array $caso, string $arquivo): array
    {
        $erros = [];
        $camposObrigatorios = ['id', 'titulo', 'prompt', 'fixture', 'must_contain', 'must_not_contain'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($caso[$campo])) {
                $erros[] = "Campo obrigatório ausente '{$campo}' em {$arquivo}";
            }
        }

        if (isset($caso['must_contain']) && !is_array($caso['must_contain'])) {
            $erros[] = "'must_contain' deve ser um array em {$arquivo}";
        }

        if (isset($caso['must_not_contain']) && !is_array($caso['must_not_contain'])) {
            $erros[] = "'must_not_contain' deve ser um array em {$arquivo}";
        }

        if (isset($caso['patterns_not_match']) && !is_array($caso['patterns_not_match'])) {
            $erros[] = "'patterns_not_match' deve ser um array quando fornecido em {$arquivo}";
        }

        return $erros;
    }

    private function executarCaso(array $caso, array $opcoes): void
    {
        $titulo = $caso['titulo'] ?? $caso['id'];
        echo "[Testando] {$titulo}...\n";

        $codigo = '';

        if ($opcoes['live']) {
            $codigo = $this->obterRespostaLiveIA($caso, $opcoes['agent_cmd']);
            if ($codigo === null) {
                $this->falhou++;
                $this->erros[] = "Falha ao obter resposta ao vivo da IA para o caso: {$titulo}";
                return;
            }
        } else {
            $fixturePath = __DIR__ . '/fixtures/' . $caso['fixture'];
            if (!file_exists($fixturePath)) {
                echo "  ✗ Fixture não encontrada: {$fixturePath}\n";
                $this->falhou++;
                $this->erros[] = "Fixture ausente: {$fixturePath}";
                return;
            }
            $codigo = (string) file_get_contents($fixturePath);
        }

        // 1. Asserções must_contain:
        foreach ($caso['must_contain'] as $termo) {
            if (str_contains($codigo, $termo)) {
                $this->passou++;
                echo "  ✓ Deve conter: '{$termo}'\n";
            } else {
                $this->falhou++;
                $msg = "[{$titulo}] Faltou conter: '{$termo}'";
                $this->erros[] = $msg;
                echo "  ✗ {$msg}\n";
            }
        }

        // 2. Asserções must_not_contain:
        foreach ($caso['must_not_contain'] as $termo) {
            if (!str_contains($codigo, $termo)) {
                $this->passou++;
                echo "  ✓ NÃO deve conter: '{$termo}'\n";
            } else {
                $this->falhou++;
                $msg = "[{$titulo}] Não deveria conter: '{$termo}'";
                $this->erros[] = $msg;
                echo "  ✗ {$msg}\n";
            }
        }

        // 3. Asserções regex:
        foreach ($caso['patterns_not_match'] ?? [] as $regex) {
            if (preg_match($regex, $codigo) === 0) {
                $this->passou++;
                echo "  ✓ NÃO deve coincidir com regex: {$regex}\n";
            } else {
                $this->falhou++;
                $msg = "[{$titulo}] Coincidiu indevidamente com regex: {$regex}";
                $this->erros[] = $msg;
                echo "  ✗ {$msg}\n";
            }
        }
    }

    private function obterRespostaLiveIA(array $caso, ?string $cmd): ?string
    {
        $comando = $cmd ?? getenv('AI_EVAL_CMD');

        if (!$comando) {
            echo "  ⚠️ Modo Live ativado, mas nenhum comando de agente fornecido.\n";
            echo "     Configure a variável de ambiente AI_EVAL_CMD (ex: export AI_EVAL_CMD=\"claude -p\")\n";
            echo "     ou passe a flag --agent-cmd=\"claude -p\".\n";
            return null;
        }

        $promptEscapado = escapeshellarg($caso['prompt']);
        $comandoCompleto = "{$comando} {$promptEscapado}";

        echo "  ⚡ Consultando agente ao vivo: {$comando}...\n";
        $saida = shell_exec($comandoCompleto);

        return $saida !== null ? (string) $saida : null;
    }

    private function parseArgumentos(array $argv): array
    {
        $opcoes = [
            'validate_only' => false,
            'live'          => false,
            'case_filter'   => null,
            'agent_cmd'     => null,
        ];

        foreach ($argv as $arg) {
            if ($arg === '--validate-only') {
                $opcoes['validate_only'] = true;
            } elseif ($arg === '--live') {
                $opcoes['live'] = true;
            } elseif (str_starts_with($arg, '--case=')) {
                $opcoes['case_filter'] = substr($arg, 7);
            } elseif (str_starts_with($arg, '--agent-cmd=')) {
                $opcoes['agent_cmd'] = substr($arg, 12);
            }
        }

        return $opcoes;
    }
}

$runner = new DynamicSkillEvalRunner();
exit($runner->executar($argv));
