# Guia essencial de segurança em PHP

Este guia é uma lista introdutória de controles frequentes, não uma auditoria completa. A solução depende do framework, da configuração do servidor, do modelo de ameaças e do fluxo da aplicação. Consulte a documentação primária e teste a implementação no seu contexto.

## SQL e banco de dados

Vincule valores recebidos em consultas preparadas de PDO, MySQLi ou da abstração usada pelo projeto. Não concatene diretamente valores não confiáveis em SQL. Placeholders servem para valores, não para nomes de tabelas, colunas ou palavras-chave; para partes dinâmicas da consulta, use uma lista de opções permitidas.

```php
$stmt = $pdo->prepare(
    'SELECT id, nome FROM usuarios WHERE email = :email'
);
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch();
```

A emulação de prepared statements é uma opção de comportamento do PDO que pode variar por driver. Desativá-la não é, isoladamente, uma garantia de segurança; use a configuração adequada ao driver e teste consultas e tipos. A proteção principal é separar a estrutura SQL dos valores com vinculação correta.

- Fontes: [PDO::prepare](https://www.php.net/manual/pt_BR/pdo.prepare.php), [Prepared Statements do PDO](https://www.php.net/manual/pt_BR/pdo.prepared-statements.php), [Prepared Statements do MySQLi](https://www.php.net/manual/pt_BR/mysqli.quickstart.prepared-statements.php)

## Saída HTML e XSS

Codifique a saída para o contexto em que será inserida. Para texto ou atributos HTML citados, `htmlspecialchars()` com codificação explícita costuma ser apropriada:

```php
echo htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
```

Isso não é uma solução universal para JavaScript, CSS, URLs, HTML não confiável ou contextos de template específicos. Use mecanismos de escape próprios do contexto e do template adotado; evite montar JavaScript ou HTML concatenando dados externos.

- Fonte: [htmlspecialchars](https://www.php.net/manual/pt_BR/function.htmlspecialchars.php)

## Senhas

Não armazene senhas em texto simples nem com hashes rápidos como MD5 ou SHA-1. Para novas implementações, use `password_hash()` e `password_verify()`. Considere `password_needs_rehash()` após autenticação para migrar parâmetros de hash. Planeje o tamanho do campo conforme o algoritmo e a política da aplicação.

```php
$hash = password_hash($senha, PASSWORD_DEFAULT);

if (password_verify($senhaDigitada, $hashArmazenado)) {
    if (password_needs_rehash($hashArmazenado, PASSWORD_DEFAULT)) {
        $hashAtualizado = password_hash($senhaDigitada, PASSWORD_DEFAULT);
        // Persistir $hashAtualizado conforme a transação da aplicação.
    }
}
```

- Fonte: [Funções de senha](https://www.php.net/manual/pt_BR/book.password.php)

## Sessões e cookies

Defina os parâmetros do cookie antes de iniciar a sessão, de acordo com o ambiente e o fluxo do produto. Em produção com HTTPS, `secure` deve ser habilitado. `httponly` impede acesso ao cookie por JavaScript, mas não elimina XSS. `SameSite=Lax` ou `Strict` pode reduzir alguns envios entre sites; escolha considerando login federado, links externos e outros fluxos. `SameSite` não substitui tokens CSRF quando estes forem necessários.

Após autenticação ou elevação de privilégio, regenere o identificador da sessão no ponto correto do fluxo. Combine isso com autorização, política de expiração, proteção CSRF e configuração do servidor.

```php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true, // Em ambiente HTTPS.
    'httponly' => true,
    'samesite' => 'Lax', // Ajustar ao fluxo da aplicação.
]);
session_start();
```

- Fontes: [Segurança de sessões](https://www.php.net/manual/pt_BR/security.sessions.php), [session_set_cookie_params](https://www.php.net/manual/pt_BR/function.session-set-cookie-params.php)

## Uploads

Não confie no nome, na extensão ou no MIME informado pelo navegador. Verifique o código de erro e limites de tamanho; inspecione o conteúdo, por exemplo com Fileinfo; aplique uma lista restrita de tipos que a aplicação realmente aceita; gere um nome interno; e armazene fora da área executável ou pública quando possível. Use a movimentação segura de uploads do PHP e controle o acesso aos arquivos.

A inspeção MIME não prova que o arquivo é benigno nem que o conteúdo está correto. Considere validação específica de formato, reprocessamento, antivírus ou isolamento conforme o risco.

- Fontes: [Tratamento de uploads](https://www.php.net/manual/pt_BR/features.file-upload.php), [Fileinfo](https://www.php.net/manual/pt_BR/book.fileinfo.php), [move_uploaded_file](https://www.php.net/manual/pt_BR/function.move-uploaded-file.php)

## Validação, configuração e revisão

Valide entrada segundo o tipo, o formato e as regras do domínio. Validação não substitui escape de saída. Mantenha segredos fora do código-fonte e do repositório; limite privilégios de contas de banco de dados e de processos; e não exponha erros internos em respostas públicas.

Use este guia como ponto de partida. Uma revisão estática ou uma lista de controles não demonstra que uma aplicação inteira está protegida; complemente com testes, análise das configurações e revisão adequada ao risco.
