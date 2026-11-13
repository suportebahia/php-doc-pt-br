# php-doc-pt-br

> **Skill e Mentor de Desenvolvimento PHP Oficial em Português para Agentes de IA e Desenvolvedores.**  
> Baseado no manual oficial do PHP (`https://www.php.net/manual/pt_BR/`) e nos padrões modernos de engenharia da comunidade (PSR / Composer / PHPUnit).

---

## 🎯 Objetivo

Ensinar agentes de IA (Claude Code, Antigravity IDE, Cursor, etc.) a atuar como **desenvolvedores e mentores sênior em PHP**. 

A skill garante que qualquer pessoa — desde um **iniciante/leigo** até um **arquiteto de software** — receba:
- Código PHP moderno (PHP 8.2+), tipado, limpo e seguro do zero.
- Explicações didáticas e descomplicadas, fundamentadas nas boas práticas do manual.
- Estruturação de projetos reais com Composer, Autoloading PSR-4 e separação de ambientes (`.env`).
- Criação e consumo de APIs RESTful com validação nativa de JSON (`json_validate`) e códigos HTTP corretos.
- Garantia de qualidade com testes automatizados (PHPUnit) e análise estática (PHPStan).
- Auditoria preventiva de segurança (mitigação de SQL Injection, XSS, CSRF, sessões e uploads).
- Links canônicos para cada conceito consultado no manual oficial em português.

---

## ⚡ Modos Operacionais e Comandos da Skill

Quando esta skill está ativa, o agente responde de forma especializada aos seguintes comandos e intenções:

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

O repositório inclui guias estruturados e atemporais na pasta `references/`:

### Core da Linguagem e Boas Práticas
- **[Padrões Oficiais de Desenvolvimento](references/padroes-de-desenvolvimento.md)**: Regras de sintaxe, tipagem estrita, retorno antecipado e estilo de código.
- **[Guia de Segurança Essencial](references/seguranca-essencial.md)**: PDO contra SQLi, `htmlspecialchars`, `password_hash`, sessões e upload com Fileinfo.
- **[POO e Recursos Modernos](references/poo-e-recursos-modernos.md)**: Constructor Promotion, classes `readonly`, Enums, `match` e Generators.
- **[Banco de Dados com PDO](references/banco-de-dados-pdo.md)**: Conexão resiliente, transações atômicas e consultas preparadas.
- **[Armadilhas e Boas Práticas](references/armadilhas-e-boas-praticas.md)**: Comparação estrita vs frouxa, pegadinhas de `empty()`, `isset()` e precisão `float`.
- **[Versões e Depreciações](references/versoes-e-depreciacoes.md)**: Histórico de recursos do PHP 8.0 ao 8.3+ e funções descontinuadas.
- **[Índice Geral do Manual Oficial](references/indice-geral-manual-ptbr.md)**: Catálogo completo de links diretos de todas as seções do manual pt-BR.

### Ecossistema Profissional e Engenharia de Software
- **[Composer e Autoloading PSR-4](references/composer-e-autoloading.md)**: Estrutura padrão de projetos, gerenciamento de dependências e otimização em produção.
- **[APIs RESTful e Manipulação de JSON](references/apis-rest-e-json.md)**: Endpoints REST nativos, `json_validate()`, status codes HTTP, CORS e cURL.
- **[Configuração e Ambiente](references/configuracao-e-ambiente.md)**: Separação de código e ambiente, arquivos `.env`, segurança de credenciais e fail-fast.
- **[Testes Automatizados e Análise Estática](references/testes-e-analise-estatica.md)**: Testes unitários com PHPUnit e análise estática rigorosa de tipos com PHPStan.

---

## 🚀 Como Usar no seu Projeto

### No Claude Code
Abra o repositório diretamente no Claude Code. O arquivo `CLAUDE.md` importa automaticamente as instruções do `AGENTS.md`.

### No Antigravity IDE
O Antigravity reconhece o arquivo `AGENTS.md` e a pasta de skills nativamente ao abrir o workspace.

### Como Submódulo Git em Qualquer Projeto
Para que sua IA consulte esta skill dentro de outro projeto, adicione este repositório como submódulo:
```bash
git submodule add https://github.com/suportebahia/php-doc-pt-br.git .skills/php-doc-pt-br
```

---

## 📜 Licença

Distribuído sob a licença **MIT**. Consulte o arquivo [LICENSE](LICENSE) para mais detalhes.
