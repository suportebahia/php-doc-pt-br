# Recursos, suporte e migração entre versões do PHP

Esta referência é seletiva. Não substitui os guias oficiais de migração nem representa uma lista exaustiva. Os exemplos do repositório usam PHP 8.2 como base, salvo indicação explícita; isso descreve compatibilidade pretendida, não recomenda manter em produção uma versão fora de suporte.

## Marcos selecionados

- **PHP 8.0:** argumentos nomeados, expressão `match` e `str_contains()`.
- **PHP 8.1:** enums, propriedades `readonly`, `never` e Fibers.
- **PHP 8.2:** classes `readonly` e depreciação de propriedades dinâmicas em classes que não as permitem.
- **PHP 8.3:** `json_validate()`, constantes de classe tipadas e atributo `#[Override]`.
- **PHP 8.4:** property hooks e visibilidade assimétrica de propriedades, além de outras mudanças.
- **PHP 8.5:** pipe operator e recursos adicionais; consulte a página de lançamento e o guia de migração antes de usar cada recurso.

`json_validate()` está disponível nativamente a partir do PHP 8.3. Em PHP 8.2, use `json_decode()` com `JSON_THROW_ON_ERROR` quando precisar validar e obter o valor.

A sintaxe `private(set)` é do PHP 8.4 e não deve aparecer sem rótulo em exemplo apresentado como PHP 8.2. O validador de snippets do projeto verifica os blocos PHP de acordo com o runtime e um marcador `<!-- php-min: X.Y -->`, quando presente.

## Ciclo de suporte verificado

Estado consultado em **13/11/2026** na página oficial de versões suportadas. As datas podem mudar para novas branches e devem ser reconsultadas antes de uma decisão:

- PHP 8.2: suporte ativo encerrado; atualizações de segurança até 31/12/2026.
- PHP 8.3: suporte ativo encerrado; atualizações de segurança até 31/12/2027.
- PHP 8.4: suporte ativo até 31/12/2026; segurança até 31/12/2028.
- PHP 8.5: suporte ativo até 31/12/2027; segurança até 31/12/2029.

O baseline PHP 8.2 do material se aproxima do fim do suporte de segurança na data acima. Para produção, escolha uma branch que ainda receba atualizações e confirme também as versões do framework, das extensões e das dependências.

## Plano proposto de atualização do baseline

Este plano foi elaborado em **13/11/2026** e ainda não altera o baseline atual. A proposta é usar PHP 8.4 como piso de compatibilidade a partir de **01/01/2027**, pois essa branch continua recebendo correções críticas de segurança até 31/12/2028. Para novos ambientes, prefira PHP 8.5 enquanto a compatibilidade da aplicação e das dependências permitir, porque o suporte ativo dessa branch está previsto até 31/12/2027. O piso 8.4, após 2026, é apenas uma meta de compatibilidade sob suporte de segurança; não equivale a suporte ativo.

Até **15/12/2026**, revise exemplos, ferramentas e dependências para identificar trechos que exigem PHP 8.4 ou superior. Prepare uma mudança separada para janeiro que atualize as menções ao baseline, verifique os snippets em PHP 8.4 e 8.5 e rotule recursos exclusivos de PHP 8.5 com `<!-- php-min: 8.5 -->`. Não remova jobs antigos antes dessa revisão.

Na mesma mudança que atualizar a matriz do CI, atualize os checks obrigatórios da proteção de `main` para os nomes de job efetivamente publicados. A branch principal atualmente exige checks de PHP 8.2, 8.3, 8.4 e 8.5; remover jobs sem ajustar esses contextos pode bloquear futuras mesclagens. Reavalie o piso até **30/06/2027** e antes do fim do suporte de segurança do PHP 8.4, em 31/12/2028. Consulte novamente a fonte oficial, pois o calendário pode mudar.

- Fonte dinâmica: [versões suportadas pelo PHP](https://www.php.net/supported-versions.php)

## Roteiro de migração

Ao atualizar uma aplicação, faça a mudança em etapas: leia o guia oficial da versão de destino; identifique incompatibilidades, depreciações e extensões removidas; habilite logs de depreciação em ambiente controlado; atualize código e dependências; teste na versão-alvo e na SAPI real; valide requisitos de plataforma; ensaie o deploy e mantenha plano de reversão.

Uma depreciação não significa necessariamente que o recurso já foi removido. Registre a versão em que começou e consulte o guia correspondente; não substitua APIs mecanicamente sem conhecer codificações, contratos e dependências.

Se o projeto usa Composer, `config.platform` simula a plataforma na resolução; ele não altera o runtime. Valide também o ambiente real com `composer check-platform-reqs`.

## Recursos removidos/depreciados: exemplos históricos

- `each()` e `create_function()` foram removidas no PHP 8.0.
- `money_format()` foi removida no PHP 8.0.
- `utf8_encode()` e `utf8_decode()` foram depreciadas no PHP 8.2; a alternativa depende da codificação de origem e destino.

A lista acima é ilustrativa, não exaustiva. Consulte as notas de migração específicas para PHP 8.2, 8.3, 8.4 e 8.5 antes de atualizar.

- Fontes: [novidades PHP 8.3](https://www.php.net/releases/8.3/en.php), [migração 8.4](https://www.php.net/manual/en/migration84.php), [novidades PHP 8.4](https://www.php.net/manual/en/migration84.new-features.php), [migração 8.5](https://www.php.net/manual/en/migration85.php), [lançamento PHP 8.5](https://www.php.net/releases/8.5/en.php), [suporte PHP](https://www.php.net/supported-versions.php)
