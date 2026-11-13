# php-doc-pt-br

> **Skill, Mentor e Test Harness de Desenvolvimento PHP Oficial em Português para Agentes de IA e Desenvolvedores.**  
> Baseado no manual oficial do PHP (`https://www.php.net/manual/pt_BR/`) e nos padrões modernos de engenharia da comunidade (PSR / Composer / PHPUnit).

---

## 🎯 Por que Este Projeto Existe?

Os modelos de IA atuais (Claude, GPT, Gemini) foram treinados com um volume gigantesco de código legado dos últimos 20 anos da internet. Sem direcionamento, **as IAs frequentemente sofrem de retrocesso estatístico**, sugerindo código arcaico (PHP 5/7), funções obsoletas e brechas graves de segurança (SQL Injection e senhas em MD5).

O **`php-doc-pt-br`** resolve esse problema através de 3 pilares interdependentes:
1. **O Cérebro:** Instruções precisas e atemporais para os agentes de IA (`SKILL.md`, `AGENTS.md`, `.cursorrules`).
2. **O Sistema Imunológico:** Um Test Harness automatizado de Evals (`tests/evals/`) que prova matematicamente que o código gerado é seguro e livre de alucinações.
3. **A Escola & Governança:** Trilhas didáticas para humanos auditarem a IA (`docs/`) e governança aberta com a comunidade PHP Brasil (`CONTRIBUTING.md`).

---

## 🛡️ O Sistema Imunológico (Test Harness da Skill)

Para garantir que a IA não invente código vulnerável, o projeto inclui uma suíte de avaliação nativa (**AI Evals**) executável localmente e no GitHub Actions:

```bash
php tests/evals/run-evals.php
```

Ela valida rigorosamente:
- ✅ **Autenticação Segura:** Bloqueia `md5`, `sha1` e concatenação de SQL; exige `password_verify` e `PDO::prepare`.
- ✅ **Upload de Arquivos:** Bloqueia validações frágeis por extensão; exige inspeção MIME real via `finfo`.
- ✅ **Proteção XSS:** Exige `htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE)`.
- ✅ **APIs REST:** Exige cabeçalhos JSON corretos e validação com `json_validate()`.
- ✅ **POO Moderna:** Exige tipagem estrita, classes `readonly` e proíbe funções depreciadas.

---

## 🎓 A Escola (Conteúdo Estruturado para Humanos)

- 🗺️ **[Trilha do Iniciante (Do Zero ao Profissional)](docs/trilha-do-iniciante.md)**: 5 módulos progressivos ensinando o que pedir para a IA e como construir sistemas passo a passo.
- 🔍 **[Guia Anti-Alucinação](docs/guia-anti-alucinacao.md)**: O checklist visual de 5 minutos para qualquer pessoa auditar se a IA entregou código seguro.
- 💡 **[Guia de Prompts Prontos](docs/guia-de-prompts-para-iniciantes.md)**: Modelos de prompts prontos para copiar, colar e obter respostas perfeitas.
- ⚖️ **[Caso Prático: Antes e Depois](examples/antes-e-depois.md)**: Comparativo lado a lado entre o código vulnerável que a internet ensina e o código profissional da skill.

---

## ⚡ Modos Operacionais da Skill

Quando esta skill está ativa, o agente responde de forma especializada aos seguintes comandos:

| Comando / Intenção | Descrição |
| :--- | :--- |
| **`/codificar`** | Escreve código moderno com `declare(strict_types=1);`, tipagem estrita, PSR-12 e PDO seguro. |
| **Mentor Didático** | Explica conceitos técnicos para leigos e iniciantes com analogias e comentários claros no código. |
| **`/composer` / `/api`** | Configura projetos com Composer PSR-4, variáveis de ambiente ou constrói endpoints RESTful nativos. |
| **`/seguranca`** | Audita códigos em busca de vulnerabilidades (SQLi, XSS, hash de senhas, sessões) e entrega a versão blindada. |
| **`/modernizar`** | Refatora código antigo (PHP 5/7) para recursos modernos do PHP 8 (match, readonly, enums, atributos). |
| **`/consultar`** | Consulta a assinatura oficial de funções nativas, tipos de parâmetros, versões e link do manual. |

---

## 📚 Base de Conhecimento e Referências

Guias canônicos na pasta `references/`:

### Core da Linguagem e Boas Práticas
- **[Padrões Oficiais de Desenvolvimento](references/padroes-de-desenvolvimento.md)**: Sintaxe, tipagem estrita, early return e PSR-12.
- **[Guia de Segurança Essencial](references/seguranca-essencial.md)**: PDO contra SQLi, `htmlspecialchars`, `password_hash`, sessões e upload com Fileinfo.
- **[POO e Recursos Modernos](references/poo-e-recursos-modernos.md)**: Constructor Promotion, classes `readonly`, Enums, `match` e Generators.
- **[Banco de Dados com PDO](references/banco-de-dados-pdo.md)**: Conexão resiliente, transações atômicas e consultas preparadas.
- **[Armadilhas e Boas Práticas](references/armadilhas-e-boas-praticas.md)**: Comparação estrita vs frouxa, pegadinhas de `empty()`, `isset()` e precisão `float`.
- **[Versões e Depreciações](references/versoes-e-depreciacoes.md)**: Histórico de recursos do PHP 8.0 ao 8.3+ e funções descontinuadas.
- **[Índice Geral do Manual Oficial](references/indice-geral-manual-ptbr.md)**: Catálogo completo de links diretos de todas as seções do manual pt-BR.

### Ecossistema Profissional e Engenharia de Software
- **[Composer e Autoloading PSR-4](references/composer-e-autoloading.md)**: Estrutura padrão de projetos, dependências e deploy otimizado.
- **[APIs RESTful e Manipulação de JSON](references/apis-rest-e-json.md)**: Endpoints REST nativos, `json_validate()`, status codes HTTP, CORS e cURL.
- **[Configuração e Ambiente](references/configuracao-e-ambiente.md)**: Separação de código e ambiente, arquivos `.env` e segurança de credenciais.
- **[Testes Automatizados e Análise Estática](references/testes-e-analise-estatica.md)**: Testes unitários com PHPUnit e análise estática de tipos com PHPStan.

---

## 🚀 Como Usar no seu Projeto

- **No Claude Code:** Abra o projeto (o `CLAUDE.md` importa `AGENTS.md` automaticamente).
- **No Antigravity IDE:** Reconhece `AGENTS.md` e a pasta de skills nativamente.
- **No Cursor / Windsurf:** Carrega as regras automaticamente via `.cursorrules`.
- **Como Submódulo Git:**
  ```bash
  git submodule add https://github.com/suportebahia/php-doc-pt-br.git .skills/php-doc-pt-br
  ```

---

## 🏛️ Governança e Contribuição

Este projeto é mantido de forma aberta e transparente com a comunidade PHP:
- 🤝 **[Guia de Contribuição](CONTRIBUTING.md)**: Como sugerir guias e adicionar testes de validação.
- 🛡️ **[Política de Segurança](SECURITY.md)**: Como reportar fragilidades de forma responsável.
- 📜 **[Código de Conduta](CODE_OF_CONDUCT.md)**: Diretrizes de respeito e acolhimento comunitário.
- ⚖️ **[Licença MIT](LICENSE)**: Código livre e reutilizável por qualquer pessoa ou empresa.
