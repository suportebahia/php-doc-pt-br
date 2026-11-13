---
name: php-doc-pt-br
description: >
  Apoio em português brasileiro para desenvolvimento, explicação e revisão de PHP,
  com referências ao manual oficial e atenção à versão, às dependências e à arquitetura existente.
  Use como complemento à documentação do projeto, do framework e das bibliotecas adotadas.
---

# Apoio de desenvolvimento PHP em português

## Escopo e limites

Esta skill é um conjunto de orientações para agentes de IA. Ela não substitui o manual completo do PHP, a documentação do framework usado, uma revisão humana nem testes de segurança especializados. Não prometa que código está “blindado”, livre de vulnerabilidades ou pronto para produção apenas por seguir estas instruções.

Antes de recomendar ou escrever código, identifique, quando relevante, a versão mínima do PHP, extensões, dependências, framework, banco de dados e convenções já presentes no projeto. Se algum desses dados mudar a solução e não estiver disponível, pergunte ou declare a suposição. Não introduza recursos que exijam uma versão superior à declarada pelo usuário.

## Tipos de pedido

Adapte a resposta à intenção do usuário:

- **Explicar ou consultar:** descreva o comportamento, os parâmetros, os retornos, as exceções e a versão mínima quando forem relevantes. Distingua comportamento documentado de convenções ou recomendações.
- **Criar ou alterar código:** siga a arquitetura e o estilo existentes. Explique decisões e apresente o código necessário, sem impor uma reescrita ou uma explicação passo a passo se não forem pedidas.
- **Rever código:** separe defeitos confirmados, riscos dependentes de contexto e sugestões de estilo. Aponte localização e impacto; não diga que uma revisão estática prova a ausência de vulnerabilidades.
- **Avaliar segurança:** indique escopo, pressupostos, evidência e limitações. Diferencie vulnerabilidades demonstradas de riscos a investigar. Não substitua um teste de segurança completo nem uma análise do sistema em execução.

Comandos como `/codificar`, `/api` e `/seguranca` são rótulos de intenção sugeridos pelo projeto, não comandos garantidos em todas as IDEs.

## Padrões e decisões técnicas

Use as referências desta pasta como orientação, não como regras universais. Prefira as convenções já adotadas pelo projeto. Quando não houver convenção, explique escolhas relevantes e use padrões comuns do ecossistema PHP:

- `declare(strict_types=1);` e tipos explícitos são bons padrões para código novo quando compatíveis com o projeto, mas não devem ser inseridos mecanicamente em toda edição ou em todo arquivo misto.
- Evite capturar exceções sem necessidade. Capture exceções quando for possível tratá-las, traduzi-las ou acrescentar contexto; não esconda erros.
- Para SQL, use parâmetros vinculados em PDO, MySQLi ou na abstração já usada pelo projeto. Placeholders representam valores, não identificadores SQL; nomes dinâmicos de colunas e ordenação precisam de lista de permissões.
- Para senhas novas, prefira `password_hash()`, `password_verify()` e, quando aplicável, `password_needs_rehash()`. Não recomende MD5 ou SHA-1 para armazenamento de senhas.
- Escape a saída conforme o contexto. `htmlspecialchars()` é apropriada para texto e atributos HTML devidamente citados, mas não é solução universal para JavaScript, CSS, URLs ou outros contextos.
- A configuração de sessão deve considerar HTTPS, o fluxo do produto, `SameSite`, proteção CSRF e o momento de regenerar o identificador. Cookies seguros não substituem autorização nem proteção CSRF.
- Em uploads, combine validação de erro e tamanho, inspeção de conteúdo, política de tipos, nome seguro e armazenamento apropriado. A extensão ou o MIME informado pelo cliente não bastam; `finfo` também não prova que um arquivo é inofensivo.
- CORS só deve ser configurado quando necessário. Use origens explícitas; não combine credenciais com origem curinga.
- Valide dados conforme regras do domínio. Escapar saída e validar entrada são tarefas diferentes.

## Compatibilidade com PHP

Os exemplos deste repositório têm como base PHP 8.2, salvo indicação explícita em contrário. Confirme sempre a versão real do projeto antes de reutilizar um exemplo.

`json_validate()` existe nativamente a partir do PHP 8.3. Em PHP 8.2, para validar e já converter JSON, use `json_decode()` com `JSON_THROW_ON_ERROR` e trate `JsonException`. `json_validate()` pode ser útil em PHP 8.3+ quando se pretende apenas verificar a sintaxe sem converter o conteúdo.

Ao falar de funcionalidades, depreciações ou suporte de versões, consulte a documentação oficial atual e indique a versão aplicável. Não apresente uma lista estática como garantia de que uma versão ainda recebe atualizações.

## Fontes

Use o manual do PHP, a especificação PHP-FIG e a documentação oficial das dependências conforme o assunto. Cite links pertinentes e verificados perto da afirmação. Não invente uma consulta ou uma citação. A tradução em português pode estar incompleta ou atrasada; quando isso afetar a resposta, consulte também a fonte oficial em inglês ou o histórico de versões e assinale a diferença.

## Referências desta skill

- `references/padroes-de-desenvolvimento.md`
- `references/seguranca-essencial.md`
- `references/poo-e-recursos-modernos.md`
- `references/banco-de-dados-pdo.md`
- `references/composer-e-autoloading.md`
- `references/apis-rest-e-json.md`
- `references/configuracao-e-ambiente.md`
- `references/testes-e-analise-estatica.md`
- `references/armadilhas-e-boas-praticas.md`
- `references/versoes-e-depreciacoes.md`
- `references/indice-geral-manual-ptbr.md`
