# Test Harness de Avaliação da Skill (AI Evals)

Esta pasta contém a suíte de testes determinísticos e avaliações de IA (**AI Evals**) para garantir que a skill `php-doc-pt-br` gere apenas código moderno, tipado, seguro e livre de alucinações legadas.

---

## 🎯 Filosofia de Execução e CI

Para garantir previsibilidade e evitar custos com tokens e problemas de flakiness (não-determinismo) em Pull Requests:
1. **No CI do GitHub Actions:** O harness executa exclusivamente em modo de **Fixtures Canônicas** (`php tests/evals/run-evals.php`). Isso garante 100% de estabilidade determinística, tempo de execução inferior a 2 segundos e custo zero de API.
2. **Em Desenvolvimento Local ou Pré-Release:** O desenvolvedor pode rodar no modo **Live** (`--live`) para avaliar modelos de linguagem em tempo real contra a suíte.

---

## 🚀 Como Executar

### 1. Modo Padrão (Validação de Fixtures / CI)
Validação offline rápida e determinística das fixtures canônicas:
```bash
php tests/evals/run-evals.php
```

### 2. Modo Apenas Validação de Schema (`--validate-only`)
Verifica se todos os arquivos `.json` na pasta `cases/` atendem estritamente aos tipos e campos obrigatórios sem rodar o código:
```bash
php tests/evals/run-evals.php --validate-only
```

### 3. Executando um Caso Específico (`--case=<id>`)
Filtra a execução para rodar apenas um cenário pelo ID:
```bash
php tests/evals/run-evals.php --case=autenticacao-segura
```
> ⚠️ **Nota:** O parâmetro `--case` destina-se exclusivamente ao desenvolvimento e depuração local. No pipeline de CI, todos os testes rodam obrigatoriamente sem filtros.

### 4. Modo Live com IA em Tempo Real (`--live`)
Envia o `prompt` diretamente para um agente ou LLM e valida a resposta em tempo real.

#### O Contrato da Interface `AI_EVAL_CMD` / `--agent-cmd`:
O comando deve receber o prompt como argumento final da CLI (ou via STDIN) e imprimir a resposta do modelo no `STDOUT`.

Exemplos de configuração:
```bash
# Exemplo 1: Usando o CLI do Claude Code:
php tests/evals/run-evals.php --live --agent-cmd="claude -p"

# Exemplo 2: Usando a variável de ambiente:
export AI_EVAL_CMD="claude -p"
php tests/evals/run-evals.php --live

# Exemplo 3: Usando script wrapper customizado (ex: cURL para OpenAI, Gemini ou Ollama):
php tests/evals/run-evals.php --live --agent-cmd="bash ./scripts/consultar-llm.sh"
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

### Papel dos Campos:
* **`id`** *(string, obrigatório)*: Identificador único em kebab-case.
* **`titulo`** *(string, obrigatório)*: Nome legível do cenário de teste.
* **`prompt`** *(string, obrigatório)*: Texto do prompt enviado para a IA quando em modo `--live`.
* **`fixture`** *(string, obrigatório)*: Nome do arquivo em `tests/evals/fixtures/` contendo a implementação canônica aprovada para o modo offline (CI).
* **`must_contain`** *(array de strings, obrigatório)*: Trechos que obrigatoriamente devem constar na resposta.
* **`must_not_contain`** *(array de strings, obrigatório)*: Trechos proibidos (ex: `md5`, `mysql_`, `?>`).
* **`patterns_not_match`** *(array de regex, opcional)*: Expressões regulares proibidas.

---

## 🛠️ Como Adicionar um Novo Caso de Teste

1. Crie um arquivo em `tests/evals/cases/XX-meu-cenario.json`.
2. Adicione a fixture canônica em `tests/evals/fixtures/meu_cenario.sample.php`.
3. Valide o schema: `php tests/evals/run-evals.php --validate-only`.
4. Execute o harness: `php tests/evals/run-evals.php`.
