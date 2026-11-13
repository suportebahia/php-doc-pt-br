# Configuração e variáveis de ambiente

Mantenha configurações separadas do código e evite incluir credenciais no repositório. O mecanismo de configuração deve seguir o framework, o servidor e a plataforma usados. Um arquivo `.env` não é carregado automaticamente pelo PHP: normalmente requer um componente ou framework que faça a leitura.

## Arquivo de exemplo e segredos

Um `.env.example` pode documentar nomes e valores fictícios, sem conter credenciais reais. Ignore o `.env` local no Git e verifique o estado do repositório antes de publicar alterações. Se um segredo for exposto, removê-lo do último commit não invalida cópias ou histórico; rode-o e substitua-o.

```dotenv
APP_ENV=local
APP_DEBUG=true
DB_HOST=127.0.0.1
DB_DATABASE=app_local
DB_USERNAME=usuário_local
DB_PASSWORD=
```

Em produção, use o mecanismo de segredos da plataforma ou uma configuração protegida. Evite guardar segredos em imagens de container, arquivos versionados, logs ou argumentos de processo. Restrinja as permissões de quem pode ler os segredos.

## Ler e validar uma variável

`getenv()` pode retornar `false` quando a variável não está definida. Valide o formato conforme o tipo; não converta silenciosamente qualquer texto para inteiro ou booleano.

```php
function obterVariavelObrigatoria(string $nome): string
{
    $valor = getenv($nome);
    if ($valor === false || $valor === '') {
        throw new RuntimeException("A configuração {$nome} é obrigatória.");
    }
    return $valor;
}

function obterPorta(string $nome, int $padrao): int
{
    $valor = getenv($nome);
    if ($valor === false || $valor === '') {
        return $padrao;
    }
    $porta = filter_var($valor, FILTER_VALIDATE_INT);
    if ($porta === false || $porta < 1 || $porta > 65535) {
        throw new RuntimeException("A configuração {$nome} deve ser uma porta válida.");
    }
    return $porta;
}
```

- Fontes: [getenv](https://www.php.net/manual/pt_BR/function.getenv.php), [filter_var](https://www.php.net/manual/pt_BR/function.filter-var.php)

## PHP-FPM e containers

A forma de disponibilizar variáveis ao PHP-FPM, containers ou serviços geridos depende da configuração da plataforma. Consulte a documentação do ambiente e confirme que os segredos não são exibidos por páginas de diagnóstico, logs ou comandos de inspeção. Não copie valores secretos reais para exemplos públicos.
