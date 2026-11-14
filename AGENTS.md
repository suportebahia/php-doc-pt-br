# AGENTS.md — Orientações para agentes

Este arquivo fornece contexto aos agentes de IA que trabalham com este repositório. As orientações são padrões de apoio, não garantias de qualidade ou segurança.

## Objetivo e âmbito

O repositório oferece guias selecionados em português brasileiro para desenvolvimento PHP. Não é o manual completo do PHP. Ao responder sobre um projeto, respeite a sua versão, arquitetura, dependências, convenções e requisitos; não assuma que os padrões deste repositório se aplicam automaticamente.

## Regras de trabalho

1. Leia os documentos relevantes para a tarefa, incluindo `SKILL.md` e as referências necessárias.
2. Confirme afirmações que dependem de versão ou de uma biblioteca em documentação primária atual. Indique a versão mínima e as limitações quando forem relevantes.
3. Não transforme uma preferência em regra universal. Por exemplo, tipagem estrita, escolha entre PDO e MySQLi, uso de exceções, CORS e configuração de sessão dependem do contexto.
4. Preserve a arquitetura existente, salvo pedido explícito para a alterar. Se faltar um dado que mude substancialmente a solução, pergunte ou declare uma suposição.
5. Em revisões, separe problemas confirmados, riscos dependentes de contexto e sugestões. Não afirme que uma análise estática prova ausência de vulnerabilidades.
6. Use fontes pertinentes e verificadas. Não invente citações nem diga que consultou uma página que não abriu.
7. Antes de executar comandos, scripts ou ferramentas externas, verifique o que fazem. Não execute código dos exemplos como se fosse confiável por estar neste repositório.

## Alterações neste repositório

Mantenha os textos genéricos e claros. Remova afirmações dependentes de sessão e métricas que envelheçam. Ao alterar exemplos ou referências:

- verifique a compatibilidade com a versão indicada;
- diferencie verificação textual, teste funcional e auditoria de segurança;
- evite promessas como “100% seguro”, “blindado” ou “livre de alucinações”;
- atualize os casos de teste relacionados e execute as verificações disponíveis;
- marque a versão mínima de blocos PHP posteriores ao baseline com `<!-- php-min: X.Y -->`; o runner analisa sintaxe e não executa esses blocos;
- documente limitações materiais sem criar garantias que os testes não sustentam.

## Ferramentas

Instruções de contexto (como `AGENTS.md`, `CLAUDE.md` e regras de IDE) não são a mesma coisa que skills reutilizáveis. O suporte, a descoberta automática, a precedência e a sintaxe dependem do produto, da versão e da configuração; não prometa carregamento universal.

- Claude Code usa `CLAUDE.md` como instrução de projeto; `@AGENTS.md` pode importar o arquivo compartilhado quando a funcionalidade estiver disponível. Skills precisam estar em diretório de descoberta, como `.claude/skills/<nome>/SKILL.md` no projeto ou `~/.claude/skills/<nome>/SKILL.md` no usuário. Um `SKILL.md` avulso na raiz não equivale a instalação.
- Cursor documenta regras em `.cursor/rules/*.mdc`; use `AGENTS.md` somente se a versão/configuração adotada o suportar. `.cursorrules` é legado, com migração recomendada para regras `.mdc` ou `AGENTS.md`, conforme o caso.
- Antigravity documenta regras e skills separadamente. Skills podem ser instaladas em `.agents/skills/<nome>/SKILL.md`; confirme os caminhos e a disponibilidade na documentação da versão em uso.

Consulte as páginas oficiais indicadas no README antes de integrar estes arquivos. O conteúdo deste repositório não comprova por si só que uma ferramenta o detectou ou aplicou.
