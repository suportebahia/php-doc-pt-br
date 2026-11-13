# Política de Segurança

A segurança é o pilar mais crítico do projeto **`php-doc-pt-br`**. Como este repositório instrui agentes de inteligência artificial a gerarem código que será utilizado em ambientes de produção, levamos a precisão das diretrizes de segurança com o mais alto rigor técnico.

---

## 🛡️ Versões Suportadas
Nos alinhamos diretamente ao calendário oficial de suporte e segurança mantido pela equipe do PHP:

| Ramo do PHP | Status Oficial | Suporte nesta Skill |
| :---: | :---: | :---: |
| **PHP 8.4** | Suporte Ativo | Suportado |
| **PHP 8.3** | Suporte Ativo | Suportado (Recomendado) |
| **PHP 8.2** | Suporte de Segurança | Suportado |
| **PHP 8.1 ou inferior** | Fim de Vida (EOL) | Desencorajado / Modo de Migração |

---

## 🚨 Reportando uma Fragilidade ou Instrução Vulnerável
Se você identificar:
- Uma instrução ou exemplo que induza a IA a sugerir código vulnerável (ex: brechas de SQL Injection, XSS, CSRF, fixação de sessão ou vazamento de segredos);
- Um link oficial desatualizado que aponte para práticas depreciadas;

Por favor, **não** abra uma Issue pública imediatamente se a fragilidade puder comprometer sistemas em produção de forma crítica. 

Envie um e-mail com detalhes técnicos e sugestão de correção para:
📧 **fernando.guppies@gmail.com** (com o assunto: `[SEGURANÇA] php-doc-pt-br`).

---

## ⏱️ Compromisso de Resposta
1. Confirmaremos o recebimento do reporte em até **48 horas**.
2. Analisaremos e desenvolveremos uma correção em alinhamento com a documentação do `php.net`.
3. Atualizaremos o repositório e o test harness (`tests/evals/`) para garantir que o vetor de vulnerabilidade seja bloqueado de forma contínua.
