# Prompts para aprender e trabalhar com PHP

Prompts claros costumam incluir o objetivo, o contexto do projeto, o resultado esperado e o nível de explicação desejado. Antes de pedir uma alteração concreta, informe a versão mínima do PHP, o framework, as dependências relevantes e o código relacionado.

Estes exemplos são prompts para orientar uma conversa, não implementações nem auditorias completas; as respostas não garantem segurança ou correção. Revise as sugestões e valide-as no contexto do projeto.

Substitua os trechos entre colchetes pelos dados do seu projeto. Se não souber uma informação, peça à IA que declare a suposição em vez de inventá-la.

## Exemplo: rever um fluxo de autenticação

> “Analise este fluxo de login em [versão do PHP/framework]. Antes de propor código, indique o que precisa confirmar. Verifique armazenamento e verificação de senhas, consultas ao banco, sessões, autorização e tratamento de erros. Separe problemas confirmados de riscos que dependem da configuração. Não afirme que o resultado fica totalmente seguro; indique testes e fontes oficiais relevantes. Explique para uma pessoa iniciante.”

## Exemplo: gravar dados de um formulário

> “No projeto [framework/versão], preciso validar e gravar nome, e-mail e mensagem na tabela [schema]. Siga as convenções e a camada de acesso a dados já existentes. Mostre quais validações são de formato e quais são regras de negócio. Explique o tratamento de falhas sem expor detalhes internos ao usuário.”

## Exemplo: criar ou rever uma API JSON

> “Para [versão do PHP/framework], proponha uma solução para o endpoint [contrato]. Use os códigos HTTP definidos pelo projeto e valide o formato do JSON e as regras de domínio. Se clientes em navegadores precisarem acessar a API a partir de outra origem, explique que CORS controla o compartilhamento de respostas no navegador e não substitui autenticação nem autorização; nesse caso, pergunte quais origens são permitidas e quais métodos/cabeçalhos precisam ser considerados no preflight. Não presuma que CORS seja necessário em outros contextos. Indique os limites da proposta e sugira testes para entradas válidas e inválidas.”

## Exemplo: análise de segurança

> “Revise o código abaixo para [versão do PHP] e [framework]. Aponte cada problema com localização, evidência e impacto. Diferencie uma vulnerabilidade demonstrada de um risco que depende do ambiente. Não reescreva a arquitetura sem justificar. Depois sugira correções e testes; não declare o código livre de vulnerabilidades.”

## Exemplo: entender uma função

> “Explique a diferença entre `isset()`, `empty()` e `array_key_exists()` na versão de PHP que uso. Use exemplos contrastivos para uma chave ausente e para uma chave presente com `null`, `0`, `false`, string vazia (`''`) e string `'0'`; mostre o resultado de cada função em cada caso. Diga quando cada função é apropriada e inclua links para o manual oficial. Se houver alguma diferença entre a tradução pt-BR e a documentação em inglês, indique-a.”

## Dicas

Informe seu nível de experiência e peça exemplos completos apenas quando isso for útil. Ao aprender, peça explicações dos conceitos, não apenas um bloco de código para copiar. Antes de executar código gerado, leia-o, confirme dependências e use um ambiente de desenvolvimento isolado.
