# Trilha introdutória de PHP com apoio de IA

Uma sequência sugerida para estudar fundamentos, banco de dados, aplicações web, APIs e qualidade. A ordem pode ser adaptada ao projeto e ao conhecimento prévio; os exemplos deste repositório usam PHP 8.2 como base, salvo indicação diferente.

## Módulo 1: fundamentos e tipos

Estude variáveis, tipos, operadores, decisões e funções. Tipos explícitos e `declare(strict_types=1);` podem ajudar em código novo, mas considere o estilo e a compatibilidade do projeto.

Prompt sugerido: “Uso PHP [versão]. Ensine-me funções e tipos com uma calculadora simples. Explique as suposições e mostre testes pequenos.”

## Módulo 2: acesso a banco de dados

Aprenda conexão, consultas parametrizadas e transações usando o driver já adotado no seu projeto. Prepared statements ajudam a separar dados da instrução SQL, mas não substituem autorização, validação de domínio nem uma lista permitida para identificadores dinâmicos.

Prompt sugerido: “No projeto [framework/driver], mostre como consultar e gravar tarefas seguindo a arquitetura existente. Inclua um teste de sucesso e um de falha.”

## Módulo 3: formulários e sessões

Estude validação de entradas, codificação de saída conforme o contexto, hashing de senhas e o ciclo de sessões. Cookies com flags adequadas são controles úteis, não substitutos de autorização nem proteção CSRF.

Prompt sugerido: “Explique o fluxo de cadastro e login adequado a [versão/framework]. Separe os controles de senha, sessão e autorização e aponte o que precisa de configuração no servidor.”

## Módulo 4: APIs e JSON

Aprenda cabeçalhos, métodos HTTP, códigos de estado, limites de requisição e validação do contrato. Para PHP 8.2, `json_decode()` com `JSON_THROW_ON_ERROR` permite tratar erro sintático ao converter. `json_validate()` requer PHP 8.3+ e serve quando basta verificar a sintaxe.

Prompt sugerido: “Crie ou reveja um endpoint segundo este contrato: [contrato]. Informe a versão mínima de PHP, valide o JSON e as regras de negócio, e proponha testes para entradas inválidas.”

## Módulo 5: ferramentas e qualidade

Explore Composer, autoloading, testes, análise estática e processo de deploy conforme o ecossistema do projeto. Confirme a versão atual e os requisitos nas documentações oficiais dessas ferramentas.

Prompt sugerido: “Analise o `composer.json` e os testes do meu projeto. Sugira melhorias compatíveis com as versões usadas; não mude dependências nem arquitetura sem explicar os impactos.”
