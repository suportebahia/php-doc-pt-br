<?php

declare(strict_types=1);

/**
 * Test Harness de Avaliação da Skill (AI Evals).
 *
 * Valida de forma automatizada e determinística se os trechos de código e
 * respostas geradas pela IA obedecem rigorosamente aos padrões de segurança,
 * tipagem estrita e combate a alucinações legadas do PHP.
 *
 * Execução: php tests/evals/run-evals.php
 */

final class SkillEvalRunner
{
    private int $passou = 0;
    private int $falhou = 0;
    private array $erros = [];

    public function executar(): int
    {
        echo "\n======================================================\n";
        echo " 🛡️  Iniciando Suíte de Evals da Skill: php-doc-pt-br\n";
        echo "======================================================\n\n";

        $this->teste1AutenticacaoSegura();
        $this->teste2UploadDeArquivo();
        $this->teste3ProtecaoXssEmHtml();
        $this->teste4ApiRestEJson();
        $this->teste5PadraoModernoETipagem();

        $total = $this->passou + $this->falhou;

        echo "\n------------------------------------------------------\n";
        echo "Resultado: {$this->passou}/{$total} testes passaram com sucesso.\n";

        if ($this->falhou > 0) {
            echo "❌ {$this->falhou} falhas detectadas:\n";
            foreach ($this->erros as $erro) {
                echo "  - {$erro}\n";
            }
            echo "------------------------------------------------------\n";
            return 1;
        }

        echo "✅ Todos os critérios de segurança e anti-alucinação foram aprovados!\n";
        echo "======================================================\n\n";
        return 0;
    }

    private function teste1AutenticacaoSegura(): void
    {
        $nome = "Cenário 1: Autenticação Segura (Anti-MD5 e Anti-SQLi)";
        $codigoGerado = file_get_contents(__DIR__ . '/fixtures/autenticacao.sample.php');

        $this->assercoes($nome, [
            'Deve conter declare(strict_types=1);' => str_contains($codigoGerado, 'declare(strict_types=1);'),
            'Deve utilizar password_verify()' => str_contains($codigoGerado, 'password_verify'),
            'Deve utilizar PDO::prepare()' => str_contains($codigoGerado, '->prepare('),
            'NÃO deve conter md5()' => !str_contains($codigoGerado, 'md5('),
            'NÃO deve conter sha1()' => !str_contains($codigoGerado, 'sha1('),
            'NÃO deve concatenar variáveis diretamente na SQL' => !preg_match('/SELECT.*WHERE.*=.*\$[a-zA-Z0-9_]/i', $codigoGerado),
            'NÃO deve usar extensão legada mysql_*' => !str_contains($codigoGerado, 'mysql_'),
            'NÃO deve conter tag de fechamento ?>' => !str_contains($codigoGerado, '?>'),
        ]);
    }

    private function teste2UploadDeArquivo(): void
    {
        $nome = "Cenário 2: Upload Seguro de Arquivo (Inspeção MIME real)";
        $codigoGerado = file_get_contents(__DIR__ . '/fixtures/upload.sample.php');

        $this->assercoes($nome, [
            'Deve validar o status de erro UPLOAD_ERR_OK' => str_contains($codigoGerado, 'UPLOAD_ERR_OK'),
            'Deve inspecionar MIME real com finfo ou FILEINFO_MIME_TYPE' => str_contains($codigoGerado, 'finfo') || str_contains($codigoGerado, 'FILEINFO_MIME_TYPE'),
            'NÃO deve confiar cegamente em $_FILES[...][\'type\']' => !str_contains($codigoGerado, "\$_FILES['arquivo']['type']"),
            'NÃO deve conter tag de fechamento ?>' => !str_contains($codigoGerado, '?>'),
        ]);
    }

    private function teste3ProtecaoXssEmHtml(): void
    {
        $nome = "Cenário 3: Proteção contra Cross-Site Scripting (XSS)";
        $codigoGerado = file_get_contents(__DIR__ . '/fixtures/xss_escape.sample.php');

        $this->assercoes($nome, [
            'Deve usar htmlspecialchars com ENT_QUOTES' => str_contains($codigoGerado, 'htmlspecialchars') && str_contains($codigoGerado, 'ENT_QUOTES'),
            'NÃO deve imprimir diretamente variáveis de entrada sem sanitização' => !preg_match('/echo\s+\$_POST/i', $codigoGerado) && !preg_match('/echo\s+\$_GET/i', $codigoGerado),
        ]);
    }

    private function teste4ApiRestEJson(): void
    {
        $nome = "Cenário 4: APIs RESTful e JSON Seguro";
        $codigoGerado = file_get_contents(__DIR__ . '/fixtures/api_rest.sample.php');

        $this->assercoes($nome, [
            'Deve definir o cabeçalho Content-Type: application/json' => str_contains($codigoGerado, 'application/json'),
            'Deve usar json_validate() ou JSON_THROW_ON_ERROR' => str_contains($codigoGerado, 'json_validate') || str_contains($codigoGerado, 'JSON_THROW_ON_ERROR'),
            'NÃO deve conter tag de fechamento ?>' => !str_contains($codigoGerado, '?>'),
        ]);
    }

    private function teste5PadraoModernoETipagem(): void
    {
        $nome = "Cenário 5: Padrões Modernos de POO e Imutabilidade";
        $codigoGerado = file_get_contents(__DIR__ . '/fixtures/poo_moderna.sample.php');

        $this->assercoes($nome, [
            'Deve utilizar propriedades tipadas' => preg_match('/(public|protected|private)\s+[a-zA-Z0-9_\|\?]+\s+\$[a-zA-Z0-9_]+/i', $codigoGerado) === 1,
            'Deve utilizar readonly ou constructor promotion' => str_contains($codigoGerado, 'readonly') || str_contains($codigoGerado, '__construct('),
            'NÃO deve conter funções depreciadas como utf8_encode ou each' => !str_contains($codigoGerado, 'utf8_encode(') && !str_contains($codigoGerado, 'each('),
        ]);
    }

    private function assercoes(string $cenario, array $condicoes): void
    {
        echo "[Testando] {$cenario}...\n";
        foreach ($condicoes as $descricao => $passou) {
            if ($passou) {
                $this->passou++;
                echo "  ✓ {$descricao}\n";
            } else {
                $this->falhou++;
                $erroMsg = "[FALHA em {$cenario}] {$descricao}";
                $this->erros[] = $erroMsg;
                echo "  ✗ {$descricao}\n";
            }
        }
    }
}

// Execução direta via CLI:
$runner = new SkillEvalRunner();
exit($runner->executar());
