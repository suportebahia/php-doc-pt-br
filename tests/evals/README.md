# Test Harness de Avaliação da Skill (AI Evals)

Esta pasta contém a suíte de testes determinísticos e avaliações de IA (**AI Evals**) para garantir que a skill `php-doc-pt-br` gere apenas código moderno, tipado, seguro e livre de alucinações legadas.

---

## 🚀 Como Executar

### 1. Modo Padrão (Validação de Fixtures)
Executa a validação offline rápida das fixtures canônicas. É o modo executado no pipeline de CI do GitHub Actions:
```bash
php tests/evals/run-evals.php
```

### 2. Modo Apenas Validação de Schema (`--validate-only`)
Verifica se todos os arquivos `.json` na pasta `cases/` seguem o schema obrigatório sem rodar o código:
```bash
php tests/evals/run-evals.php --validate-only
```

### 3. Executando um Caso Específico (`--case=<id>`)
Roda as asserções de apenas um caso de teste:
```bash
php tests/evals/run-evals.php --case=autenticacao-segura
```

### 4. Modo Live com IA em Tempo Real (`--live`)
Envia o `prompt` diretamente para um agente ou LLM e valida a resposta gerada em tempo real:
```bash
# Via comando CLI (Claude Code, Antigravity CLI, etc.):
export AI_EVAL_CMD="claude -p"
php tests/evals/run-evals.php --live

# Ou passando o comando diretamente via flag:
php tests/evals/run-evals.php --live --agent-cmd="claude -p"
```

---

## 📋 Schema dos Casos de Teste (`cases/*.json`)

Todo caso de teste na pasta `tests/evals/cases/` deve ser um arquivo JSON com a seguinte estrutura:

```json
{
  "id": "identificador-unico",
  "titulo": "Título descritivo do cenário",
  "prompt": "O prompt que é enviado para a IA",
  "fixture": "nome_do_arquivo.sample.php",
  "must_contain": [
    "trecho_obrigatorio_1",
    "trecho_obrigatorio_2"
  ],
  "must_not_contain": [
    "trecho_proibido_1",
    "trecho_proibido_2"
  ],
  "patterns_not_match": [
    "/expressao_regular_proibida/i"
  ]
}
```

### Descrição dos Campos:
* **`id`** *(string, obrigatório)*: Identificador único em kebab-case.
* **`titulo`** *(string, obrigatório)*: Nome legível do cenário de teste.
* **`prompt`** *(string, obrigatório)*: A pergunta ou comando enviado para a IA avaliar.
* **`fixture`** *(string, obrigatório)*: Nome do arquivo de referência em `tests/evals/fixtures/`.
* **`must_contain`** *(array de strings, obrigatório)*: Pedaços de código que OBRIGATORIAMENTE devem estar presentes na resposta.
* **`must_not_contain`** *(array de strings, obrigatório)*: Pedaços de código que NUNCA podem aparecer (ex: `md5`, `mysql_`, `?>`).
* **`patterns_not_match`** *(array de regex, opcional)*: Expressões regulares que falham o teste caso coincidam no código.

---

## 🛠️ Como Adicionar um Novo Caso de Teste

1. Crie um arquivo em `tests/evals/cases/XX-meu-cenario.json`.
2. Adicione a fixture esperada em `tests/evals/fixtures/meu_cenario.sample.php`.
3. Valide o schema: `php tests/evals/run-evals.php --validate-only`.
4. Execute o harness: `php tests/evals/run-evals.php`.
