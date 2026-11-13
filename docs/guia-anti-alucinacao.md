# Checklist para rever código PHP gerado por IA

Este checklist ajuda a encontrar problemas comuns; não confirma que o código é seguro, correto ou pronto para produção. A revisão depende do contexto, da versão de PHP, das dependências e do sistema completo.

## Perguntas úteis para a revisão

1. **Compatibilidade:** o exemplo usa apenas sintaxe, funções e extensões disponíveis na versão mínima do projeto?
2. **Entradas e banco de dados:** os valores externos são validados conforme o domínio e separados da consulta SQL por parâmetros vinculados? Identificadores dinâmicos são limitados por uma lista permitida?
3. **Senhas:** o fluxo usa `password_hash()` e `password_verify()` em vez de hashes rápidos ou texto simples?
4. **Saída:** os dados são codificados para o contexto específico de HTML, URL, JavaScript ou template? `htmlspecialchars()` não serve para todos os contextos.
5. **Arquivos enviados:** são verificados erros, limites, tipo de conteúdo e destino? A extensão e o MIME declarado pelo cliente não são suficientes.
6. **Sessões e autorização:** o fluxo regenera o identificador quando apropriado e verifica autorização em cada operação? Cookies não substituem proteção CSRF.
7. **Tratamento de erros:** erros internos e segredos são mantidos fora das respostas e dos logs acessíveis publicamente?
8. **Integração:** o código segue a arquitetura, as dependências, as transações e as convenções do projeto, em vez de criar uma segunda abordagem paralela?
9. **Verificação:** existem testes relevantes? Leia os testes e confirme o que cobrem; não deduza segurança pela presença de palavras como `prepare`, `finfo` ou `htmlspecialchars`.
10. **Fontes:** versões e comportamentos de funções foram conferidos na documentação oficial apropriada?

## Como pedir uma revisão melhor

Peça ao agente que declare a versão do PHP assumida, o framework e as dependências necessárias; explique as suposições; aponte riscos e limitações; e proponha testes. Se o código for para produção ou tratar dados sensíveis, faça uma revisão humana e use um processo de segurança proporcional ao risco.

A skill e este checklist são auxiliares. Nenhum checklist breve consegue garantir ausência de vulnerabilidades.
