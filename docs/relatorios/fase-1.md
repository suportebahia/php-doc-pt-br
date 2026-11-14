# Relatório da Fase 1 — governança e cobertura

## Resultado

Na branch `feat/fase-1-governance-coverage`, foi criada uma política documental de revisão e acompanhamento de fontes em `GOVERNANCE.md` e uma matriz em `docs/MATRIZ-COBERTURA.md`. A matriz compara as referências selecionadas com o índice oficial do Manual PHP pt_BR e com as páginas das seções listadas. A própria página do índice indicava atualização em 2026-10-15; a consulta e a classificação foram registradas em 2026-11-13. O repositório oficial da tradução pt_BR é `php/doc-pt_br`; este projeto continua independente e complementar, sem reivindicar autoridade nem paridade integral.

A matriz tem 124 linhas: 13 cobertas, 67 parciais, 44 ausentes e nenhuma classificada como desatualizada. Os 22 arquivos Markdown atuais aparecem associados a pelo menos uma seção. “Coberto” não significa reprodução completa; “ausente” não avalia a qualidade do manual oficial; e a ausência de evidência de desatualização não prova que uma orientação local continue atual.

Foi acrescentado `tests/evals/validate-coverage-matrix.php` para detectar referências sem mapeamento, caminhos inexistentes, estados ou URLs inválidos e divergências nos hashes SHA-256. `tests/evals/test-coverage-matrix.php` verifica seis casos sintéticos: matriz válida, data impossível, hash visível divergente do marcador, arquivo Markdown oculto sem mapeamento, referência nova não mapeada e conteúdo alterado com hash antigo. O README e as instruções de contribuição agora distinguem verificações textuais, análise sintática, testes funcionais e validação editorial da matriz.

## Fontes e ressalvas

A estrutura foi obtida no [índice oficial do Manual PHP pt_BR][1], usando as categorias de nível superior e as subseções imediatas que o índice apresenta. O projeto oficial de tradução é [`php/doc-pt_br`][2]. Cada linha da matriz aponta diretamente para a página do manual usada na comparação.

A extração da página oficial `features.cookies.php` não apresentou o corpo da seção. A extração de `features.dtrace.php` apresentou conteúdo não relacionado. Essas limitações estão registradas na matriz; as classificações foram conservadoras e devem ser revistas quando for possível consultar integralmente as páginas. A divergência no URL de tratamento de erros foi resolvida: o índice e a página válida usam `language.error-handling.php`; `language.errors.php` não apresentou conteúdo.

A existência de uma linha ou de um hash não valida semanticamente o conteúdo. A revisão não é auditoria de segurança, prova de completude, verificação de disponibilidade HTTP nem garantia de comportamento em produção.

## Compatibilidade e segurança

O baseline não foi alterado: permanece PHP 8.2, com a matriz CI atual em PHP 8.2–8.5. O CI foi apenas ampliado para executar o novo verificador e seus testes sintéticos. Não foram alterados `config/`, o arquivo de índice do projeto nem as regras externas de proteção da branch. A política de revisão documentada não afirma que o GitHub imponha aprovações.

As verificações funcionais foram executadas sob `CI=true` em processos separados, usando somente os testes allowlistados e dados sintéticos. Não foram executados snippets Markdown, fixtures como código funcional, rede nos testes, banco de produção, credenciais, dados reais nem o modo `--live`. Separar processos reduz interferências, mas não constitui uma sandbox.

## Verificações locais executadas

Foi instalado PHP CLI 8.3.6 no sandbox de trabalho. Os seguintes comandos terminaram com sucesso:

- `php -l` em todos os PHP de `tests/evals/`, nas fixtures PHP e nos três helpers puros allowlistados;
- `CI=true php tests/evals/run-evals.php --validate-only` — 5 casos estruturalmente válidos;
- `CI=true php tests/evals/run-evals.php` — 60 verificações textuais e sintáticas aprovadas; os blocos Markdown foram analisados, não executados;
- `CI=true php tests/evals/test-runner.php` — os três testes funcionais passaram em processos separados; também passaram as regressões de contenção de caminho e análise de snippet sem execução. Os testes que exercitam `--live` foram omitidos por `CI=true`;
- `CI=true php tests/evals/validate-links.php` — formato e domínio dos links válidos; a ferramenta não consulta a disponibilidade HTTP;
- `CI=true php tests/evals/validate-coverage-matrix.php` — todas as referências mapeadas e hashes coincidentes;
- `CI=true php tests/evals/test-coverage-matrix.php` — seis casos sintéticos aprovados: válido, data inválida, hash visível divergente, arquivo oculto sem mapeamento, referência não mapeada e hash obsoleto;
- `git diff --check` — sem erros de whitespace;
- verificação estática confirmando que `config/` e `index` não foram alterados e que o CI continua em PHP 8.2–8.5.

As verificações remotas da matriz PHP 8.2–8.5 ainda dependem do CI do pull request. Esta Fase 1 não modifica o baseline nem faz qualquer alteração em produção.

## Próximo passo

Abrir um pull request para revisão, sem o mesclar. A proteção da branch e a matriz de versões devem permanecer inalteradas; qualquer mudança de baseline ou de enforcement de revisões deve ser proposta separadamente.

## Referências

[1]: https://www.php.net/manual/pt_BR/index.php "Índice oficial do Manual PHP pt_BR"
[2]: https://github.com/php/doc-pt_br "Repositório oficial da tradução php/doc-pt_br"
