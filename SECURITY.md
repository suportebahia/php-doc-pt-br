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

Por favor, **não** abra uma Issue pública imediatamente se a fragilidade puder comprometer sistemas em produção de forma crítica. 

Envie um e-mail com detalhes técnicos e sugestão de correção para:
📧 **fernando.guppies@gmail.com** (com o assunto: `[SEGURANÇA] php-doc-pt-br`).

---

## ⏱️ Compromisso de Resposta
1. Confirmaremos o recebimento do reporte em até **48 horas**.
2. Analisaremos e desenvolveremos uma correção em alinhamento com a documentação do `php.net`.
3. Atualizaremos o repositório e o test harness (`tests/evals/cases/`) para garantir que o vetor de vulnerabilidade seja bloqueado de forma contínua.
