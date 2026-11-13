# Política de Segurança

A segurança é o pilar mais crítico do projeto **`php-doc-pt-br`**. Como este repositório instrui agentes de inteligência artificial a gerarem código que será utilizado em ambientes de produção, levamos a precisão das diretrizes de segurança com o mais alto rigor técnico.

---

## 🛡️ Versões Suportadas
Esta skill segue rigorosamente a política oficial de ciclo de vida da linguagem mantida pela equipe do PHP.

- **Política de Versões:** As diretrizes desta skill aplicam-se a todas as versões que constam com status de **Suporte Ativo** (*Active Support*) ou **Apenas Correções de Segurança** (*Security Fixes Only*) na página oficial de versões suportadas:  
  👉 **[https://www.php.net/supported-versions.php](https://www.php.net/supported-versions.php)**
- **Versões com Fim de Vida (EOL):** Versões marcadas como *End of Life* pelo projeto PHP oficial não são recomendadas para novas implementações. Quando o usuário apresentar código em versões legadas, a skill instrui o agente a propor refatoração preventiva para uma versão suportada.

---

## 🚨 Reportando uma Fragilidade ou Instrução Vulnerável
Se você identificar:
- Uma instrução ou exemplo que induza a IA a sugerir código vulnerável (ex: brechas de SQL Injection, XSS, CSRF, fixação de sessão ou vazamento de segredos);
- Um link oficial desatualizado que aponte para práticas depreciadas;

Por favor, utilize o canal oficial de **Reporte Privado de Vulnerabilidades do GitHub** (*Private Vulnerability Reporting*):
👉 **[Reportar Vulnerabilidade de Segurança](https://github.com/suportebahia/php-doc-pt-br/security/advisories/new)**

Este canal garante sigilo, previne spam, mantém o histórico de reporte auditável e protege a comunidade até que a correção esteja pronta.

---

## ⏱️ Compromisso de Resposta
1. Confirmaremos o recebimento do reporte em até **48 horas** através do advisory do GitHub.
2. Analisaremos e desenvolveremos uma correção em alinhamento com a documentação do `php.net`.
3. Atualizaremos o repositório e os casos de teste dinâmicos em `tests/evals/cases/` para garantir que o vetor de vulnerabilidade seja bloqueado de forma contínua.
