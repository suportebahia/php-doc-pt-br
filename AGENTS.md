# AGENTS.md — Instruções para o Agente Mantenedor

Este arquivo é o **contrato de comportamento** de qualquer agente de IA que trabalhe neste repositório. Leia-o por completo antes de propor qualquer alteração. O objetivo não é apenas "editar arquivos", mas **preservar a integridade e a longevidade da skill `php-doc-pt-br`**.
---

## 1. Contexto do Projeto (leia antes de agir)

Este repositório contém uma **Skill de documentação PHP em português** (`php-doc-pt-br`), projetada para ser carregada por agentes de IA (Claude Code, Antigravity, Cursor, etc.) e usada por **programadores PHP da comunidade**.

A missão da skill é:
- Responder dúvidas sobre o **core da linguagem PHP** com base no manual oficial em português (`https://www.php.net/manual/pt_BR/`).
- **Sempre citar a fonte** (link da página do manual consultada).
- Ser **genérica, atemporal e reutilizável** por qualquer agente, em qualquer sessão, para qualquer usuário.

O público-alvo são **desenvolvedores PHP**, não mantenedores deste repositório. Portanto, tudo o que for escrito aqui deve fazer sentido para **quem só vê a skill em uso**, nunca para quem acompanhou o histórico do projeto.

---

## 2. Regra de Ouro: Remover Informações com Prazo de Validade Curto

Sempre que você (agente) criar, editar ou revisar qualquer arquivo deste repositório, **remova proativamente** as seguintes categorias de conteúdo:

### 2.1. Estado atual do projeto
- ❌ "Atualmente a skill está na versão X."
- ❌ "Estamos trabalhando na melhoria da seção Y."
- ❌ "A próxima release vai incluir Z."
- ❌ "Por enquanto, ainda não suportamos W."
- ❌ Checklists de tarefas em andamento.

### 2.2. Informações dependentes de contexto de sessão
- ❌ "Como discutimos na última sessão…"
- ❌ "Conforme decidido no PR #42…"
- ❌ "Esta é a versão corrigida do problema relatado por João."
- ❌ Nomes de mantenedores específicos, datas de reuniões, links para issues internas.

### 2.3. Conteúdo não-genérico
- ❌ Exemplos que só fazem sentido no contexto do mantenedor.
- ❌ Configurações específicas de máquina, IDE ou sistema operacional do autor.
- ❌ Comentários internos sobre decisões de design.

### 2.4. Dados efêmeros
- ❌ "PHP 8.3 é a versão mais recente."
- ❌ "O manual está atualmente em revisão."
- ❌ Métricas, contadores, screenshots de dashboards.

Prefira sempre frases atemporais: "No PHP 8.3+…" em vez de "Na versão atual…".

---

## 3. Regra Complementar: O Que Deve Permanecer

- ✅ Descrição da **missão** da skill.
- ✅ **Gatilhos de ativação** (quando a skill deve ser usada).
- ✅ **Diretrizes de comportamento** (citar fonte, tratar versões, respeitar escopo).
- ✅ **Referências atemporais**: links fixos do manual, diferenças entre versões estáveis, funções depreciadas/removidas.
- ✅ **Exemplos de interação** que ilustrem o comportamento esperado.
- ✅ **Metadados do frontmatter** (`name`, `description`).

---

## 4. Fluxo de Trabalho Esperado do Agente

1. **Leia** `SKILL.md` e `references/versoes-e-depreciacoes.md` para entender a skill atual.
2. **Identifique** se o conteúdo que você vai produzir (ou que já existe) se enquadra nas categorias da Seção 2.
3. **Remova** o que for efêmero, contextual ou não-genérico — **antes** de adicionar o que é novo.
4. **Reescreva** o conteúdo restante em linguagem **atemporal e impessoal**.
5. **Valide** que a skill continua autoexplicativa.
6. **Atualize a fonte** quando a resposta vier do manual.

---

## 5. Anti-padrões (o que NÃO fazer)

- ❌ Criar seções "Changelog" ou "Roadmap" dentro de `SKILL.md`.
- ❌ Deixar comentários `<!-- TODO -->` ou `<!-- FIXME -->` em arquivos distribuídos.
- ❌ Referenciar caminhos absolutos da máquina do autor.
- ❌ Usar "este projeto" para se referir a algo que só o mantenedor vê.
- ❌ Manter exemplos com dados fictícios que aparentem ser reais.

---

## 6. Prompt Operacional (para o agente executar)

> **Ao editar qualquer arquivo deste repositório, aplique este filtro antes de salvar:**
>
> 1. Esta frase continuará verdadeira daqui a **6 meses**? Se não, **remova ou reescreva**.
> 2. Esta frase faz sentido para um **desenvolvedor PHP que nunca viu este repositório**? Se não, **remova ou reescreva**.
> 3. Esta frase depende de **quem está lendo**? Se sim, **remova ou reescreva**.
> 4. Esta afirmação técnica tem **link para a fonte** no manual? Se não, **adicione**.
>
> Se as quatro respostas forem "sim", o conteúdo está pronto para permanecer.

---

## 7. Compatibilidade Multi-Agente

Este arquivo é lido por Claude Code (via `CLAUDE.md` → `@AGENTS.md`), Antigravity IDE (nativamente) e Cursor. Portanto, **não** coloque instruções específicas de um único agente aqui.
