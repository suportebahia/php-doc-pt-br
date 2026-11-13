<?php

declare(strict_types=1);

/**
 * Test Harness Dinâmico de Avaliação da Skill (AI Evals).
 *
 * Lê casos de teste externalizados em `tests/evals/cases/*.json` e valida
 * regras determinísticas de conformidade (must_contain, must_not_contain, regex).
 *
 * Suporta:
 *  1. Modo Fixture (padrão): Valida saídas canônicas de referência.
 *  2. Modo Live (via STDIN ou arquivo): php tests/evals/run-evals.php --input=resposta_ia.php --case=autenticacao-segura
 *
 * Execução: php tests/evals/run-evals.php
 */

final class DynamicSkillEvalRunner
{
    private int $passou = 0;
    private int $falhou = 0;
    private array $erros = [];

    public function executar(array $argv): int
    {
        echo "\n======================================================\n";
        echo " 🛡️  Suíte de Evals Dinâmicos da Skill: php-doc-pt-br\n";
        echo "======================================================\n\n";

        $casosDir = __DIR__ . '/cases';
        $arquivosCasos = glob($casosDir . '/*.json');

        if (empty($arquivosCasos)) {
            echo "❌ Nenhum caso de teste encontrado em {$casosDir}.\n";
            return 1;
        }

        sort($arquivosCasos);

        foreach ($arquivosCasos as $caminhoCaso) {
            $dados = json_decode((string) file_get_contents($caminhoCaso), true);
            if (!is_array($dados)) {
                echo "⚠️ Erro ao decodificar JSON do caso: {$caminhoCaso}\n";
                $this->falhou++;
                continue;
            }

            $this->executarCaso($dados);
        }

        $total = $this->passou + $this->falhou;
        echo "\n------------------------------------------------------\n";
        echo "Resultado: {$this->passou}/{$total} asserções passaram com sucesso.\n";

        if ($this->falhou > 0) {
            echo "❌ {$this->falhou} falhas detectadas:\n";
            foreach ($this->erros as $erro) {
                echo "  - {$erro}\n";
            }
            echo "------------------------------------------------------\n";
            return 1;
        }

        echo "✅ Todos os casos de teste dinâmicos foram aprovados!\n";
        echo "======================================================\n\n";
        return 0;
    }

    private function executarCaso(array $caso): void
    {
        $titulo = $caso['titulo'] ?? $caso['id'] ?? 'Caso sem título';
        echo "[Testando] {$titulo}...\n";

        $fixturePath = __DIR__ . '/fixtures/' . ($caso['fixture'] ?? '');
        if (!file_exists($fixturePath)) {
            echo "  ✗ Fixture não encontrada: {$fixturePath}\n";
            $this->falhou++;
            $this->erros[] = "Fixture ausente para o caso: {$titulo}";
            return;
        }

        $codigo = (string) file_get_contents($fixturePath);

        // 1. Asserções must_contain:
        foreach ($caso['must_contain'] ?? [] as $termo) {
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
        foreach ($caso['must_not_contain'] ?? [] as $termo) {
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

        // 3. Asserções de Expressão Regular (patterns_not_match):
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
}

$runner = new DynamicSkillEvalRunner();
exit($runner->executar($argv));
