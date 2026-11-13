# Guia de Prompts para Iniciantes e Leigos

Como extrair o máximo do agente de IA utilizando a skill `php-doc-pt-br`, mesmo que você nunca tenha programado em PHP antes.

---

## 💡 O Segredo: Como Pedir para a IA
Agentes de IA respondem muito melhor quando recebem uma **intenção clara**, o **resultado esperado** e pedem uma **explicação didática**.

Copie e cole os modelos abaixo, adaptando as palavras entre colchetes `[assim]` para o seu caso real:

---

## Modelo 1: Criando um Sistema de Login Seguro
> **Prompt:**
> "Atue com a skill `php-doc-pt-br`. Eu preciso criar um fluxo de login seguro em PHP para os usuários do meu site. 
> 1. Escreva o código moderno utilizando `password_verify` e PDO com Prepared Statements.
> 2. Explique de forma simples para um iniciante por que não devemos usar md5 e como o hash protege as senhas.
> 3. Inclua comentários nas linhas importantes e cite as fontes do manual do PHP."

---

## Modelo 2: Salvando Dados de um Formulário de Contato no Banco
> **Prompt:**
> "Atue com a skill `php-doc-pt-br`. Recebi dados de um formulário com os campos: nome, e-mail e mensagem.
> 1. Crie uma classe de serviço em PHP moderno para validar esses campos (e-mail válido, campos obrigatórios preenchidos).
> 2. Salve os dados em uma tabela `mensagens_contato` usando PDO sem risco de SQL Injection.
> 3. Mostre como tratar se o banco cair com `try/catch` de forma amigável para quem está preenchendo o formulário."

---

## Modelo 3: Criando uma API que Retorna JSON
> **Prompt:**
> "Atue com a skill `php-doc-pt-br`. Preciso criar um endpoint de API em PHP puro chamado `listar-produtos.php` que retorna uma lista de produtos do banco de dados em formato JSON.
> 1. Configure os cabeçalhos HTTP corretos (`Content-Type: application/json` e CORS).
> 2. Consulte o banco via PDO e formate a resposta com `json_encode` mantendo acentuações e caracteres em português.
> 3. Se não houver produtos ou der erro, retorne o código HTTP adequado (ex: 404 ou 500) com uma mensagem de erro em JSON."

---

## Modelo 4: Auditando e Blindando um Código Existente
> **Prompt:**
> "Atue no modo `/seguranca` da skill `php-doc-pt-br`. Analise o código PHP que vou colar abaixo:
> [COLE O SEU CÓDIGO AQUI]
> 
> 1. Aponte se existem brechas de segurança (SQL Injection, XSS, vazamento de erros).
> 2. Mostre a versão refatorada no padrão moderno PHP 8.2+.
> 3. Explique didaticamente o que estava vulnerável e como o manual recomenda corrigir."

---

## Modelo 5: Entendendo uma Função do PHP (Modo Professor)
> **Prompt:**
> "Atue no modo didático da skill `php-doc-pt-br`. Não entendi a diferença entre `isset()`, `empty()` e `array_key_exists()`. 
> Explique com uma analogia do dia a dia, mostre um exemplo de código com um caso real onde cada um deve ser usado e coloque o link do manual oficial de cada um."

---

## 🏆 3 Dicas de Ouro para Conversar com o Agente:
1. **Diga seu nível de experiência:** Iniciar com *"Sou iniciante, explique passo a passo..."* faz a IA trocar jargões difíceis por explicações claras.
2. **Peça comentários no código:** *"Comente no código o que cada bloco faz"* ajuda você a aprender enquanto roda o sistema.
3. **Sempre peça o código completo:** Peça *"Forneça o arquivo completo pronto para salvar e rodar"*, evitando códigos cortados com reticências `...`.
