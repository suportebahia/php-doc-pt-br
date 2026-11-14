# php-doc-pt-br

Orientações em português brasileiro para programadores PHP e agentes de IA. O repositório reúne instruções de contexto, guias temáticos, exemplos e um pequeno verificador de padrões. É um projeto comunitário independente: não é o manual oficial do PHP nem uma distribuição completa da documentação.

Use-o como apoio, confirmando detalhes no [manual oficial do PHP](https://www.php.net/manual/pt_BR/) e na documentação do framework, das bibliotecas e da versão de PHP usadas no projeto. A tradução do manual pode ter partes incompletas ou desatualizadas; consulte a documentação oficial em inglês quando necessário.

## O que contém

- `SKILL.md`, `AGENTS.md` e `.cursorrules`: instruções que algumas ferramentas de programação com IA podem carregar automaticamente. Leia-as antes de as ativar num projeto.
- `references/`: guias práticos sobre linguagem/POO, web/HTTP, segurança, bancos, Composer, frameworks, runtime, arquivos/dados, operação, testes e versões.
- `docs/` e `examples/`: materiais didáticos e exemplos para revisão.
- `docs/MATRIZ-COBERTURA.md`: triagem datada da relação entre as referências selecionadas e as seções imediatas do índice oficial; não representa paridade nem completude.
- `GOVERNANCE.md`: papéis de revisão, política de versionamento de conteúdo e processo para acompanhar fontes.
- `tests/evals/`: casos e fixtures usados por verificações estáticas simples.

O conteúdo procura cobrir decisões frequentes de desenvolvimento PHP, mas não contém toda a linguagem, todos os frameworks, configurações de produção ou vetores de ataque. Não é uma auditoria de segurança nem substitui testes adequados ao sistema. Nenhum guia é literalmente completo para todo projeto.

A automação atual cobre verificações textuais, sintaxe de snippets, regressões do runner e três testes funcionais allowlistados para helpers puros selecionados. A matriz de cobertura associa os 22 arquivos atuais de `references/` a 124 seções do índice consultado; a maioria é parcial ou ausente. Ainda faltam testes comportamentais representativos para muitos guias e aprofundamento em integrações versionadas de Laravel/Symfony, implantação PHP-FPM/reverse proxy, bancos específicos e workers de longa duração. Essas são lacunas de cobertura, não promessas de trabalho concluído.

## Testes locais

O verificador usa funcionalidades disponíveis a partir do PHP 8.2 e não instala dependências. Execute:

```bash
php tests/evals/run-evals.php --validate-only
php tests/evals/run-evals.php
php tests/evals/test-runner.php
php tests/evals/validate-links.php
php tests/evals/validate-coverage-matrix.php
php tests/evals/test-coverage-matrix.php
```

O modo padrão compara fixtures de texto com requisitos e padrões definidos em JSON e analisa sintaticamente os blocos PHP dos documentos usando o parser da versão corrente. A análise não executa os blocos Markdown. Os três testes funcionais carregam helpers PHP puros explicitamente allowlistados e executam cada caso num processo separado. O validador da matriz confere associações e hashes, sem fazer pedidos HTTP nem avaliar semanticamente as justificações. Nenhum destes mecanismos verifica o comportamento em produção ou prova que um exemplo é seguro.

Existe um modo opcional para consultar um agente de IA em tempo real. Esse modo inicia um programa local escolhido pelo usuário; use apenas executáveis e argumentos que conhece e confia. Consulte [`tests/evals/README.md`](tests/evals/README.md) antes de o ativar. Não é usado no CI.

## Compatibilidade

Os exemplos ainda usam PHP 8.2 como base, salvo indicação explícita em contrário. O plano proposto é elevar o piso de compatibilidade para 8.4 em 01/01/2027, mantendo PHP 8.5 como recomendação para novos ambientes enquanto estiver em suporte ativo; esse plano ainda não muda o baseline atual. Blocos que exigem versão superior devem trazer `<!-- php-min: X.Y -->`. Consulte [recursos, suporte e plano de migração](references/versoes-e-depreciacoes.md); confirme a tabela oficial antes de decidir versões de produção.

## Utilização em ferramentas de IA

A forma de carregar instruções varia por ferramenta e versão. Consulte a documentação da ferramenta. Não presuma que qualquer IDE reconhece os mesmos nomes de arquivo, comandos ou formato. Para integrar o conteúdo num projeto, prefira copiar apenas as instruções relevantes ou fixar uma revisão específica como submódulo, em vez de acompanhar automaticamente alterações não revistas.

## Contribuições

Consulte [`CONTRIBUTING.md`](CONTRIBUTING.md), a [política de segurança](SECURITY.md), o [código de conduta](CODE_OF_CONDUCT.md) e a [licença MIT](LICENSE). Sugestões de correção são bem-vindas; a presença de um link ou de uma verificação no CI não significa que a informação tenha sido validada semanticamente.
