# Configuração e Variáveis de Ambiente

Padrão de arquitetura e segurança para gerenciamento de credenciais, configurações sensíveis e separação entre código e ambiente (Princípios *12-Factor App*).

---

## 1. O Princípio de Separação
**Regra de Ouro:** Nunca grave dados sensíveis (senhas de banco de dados, chaves de API, segredos de criptografia) diretamente nos arquivos de código PHP.

1. **Código:** É versionado no repositório Git e idêntico em qualquer ambiente (desenvolvimento, homologação, produção).
2. **Configuração:** Varia de ambiente para ambiente e é injetada através de **Variáveis de Ambiente** (`Environment Variables`).

---

## 2. O Padrão `.env` no Desenvolvimento Local
Para desenvolvimento em máquina local ou estações de trabalho, utiliza-se comumente um arquivo `.env` na raiz do projeto:

### Arquivo `.env.example` (Versionado no Git):
Serve de modelo para outros membros da equipe saberem quais variáveis são necessárias:
```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meu_banco
DB_USERNAME=root
DB_PASSWORD=

API_GATEWAY_KEY=
```

### Arquivo `.env` (NUNCA versionado no Git):
Contém as credenciais reais da sua máquina local. **Deve estar listado no `.gitignore`**:
```gitignore
# No arquivo .gitignore:
/.env
/vendor/
```

---

## 3. Leitura e Validação Segura em PHP Puro
Em projetos sem frameworks pesados, você pode carregar o `.env` ou ler variáveis do sistema com validação rigorosa (*Fail-Fast*):

```php
<?php

declare(strict_types=1);

namespace App\Config;

final class Ambiente
{
    public static function obter(string $chave, ?string $padrao = null): string
    {
        // 1. Procura na superglobal $_ENV ou $_SERVER (injetadas pelo FPM/Docker):
        $valor = $_ENV[$chave] ?? $_SERVER[$chave] ?? getenv($chave);

        if ($valor === false || $valor === null || $valor === '') {
            if ($padrao !== null) {
                return $padrao;
            }
            // Falha imediata (fail-fast) para evitar que o sistema rode sem configuração crítica:
            throw new \RuntimeException("A variável de ambiente obrigatória '{$chave}' não foi definida.");
        }

        return (string) $valor;
    }

    public static function obterBool(string $chave, bool $padrao = false): bool
    {
        $valor = self::obter($chave, $padrao ? 'true' : 'false');
        return filter_var($valor, FILTER_VALIDATE_BOOL);
    }

    public static function obterInt(string $chave, int $padrao = 0): int
    {
        $valor = self::obter($chave, (string) $padrao);
        return (int) $valor;
    }
}
```
- Fonte: [getenv](https://www.php.net/manual/pt_BR/function.getenv.php) e [filter_var](https://www.php.net/manual/pt_BR/function.filter-var.php)

---

## 4. Variáveis de Ambiente em Produção (Docker / Servidor Web)
Em ambientes de produção com alta carga:
- **Evite ler arquivos `.env` do disco a cada requisição HTTP.** O I/O de disco para leitura de texto diminui a performance.
- **Injete as variáveis diretamente no servidor web:**
  - No **PHP-FPM** (`www.conf`):
    ```ini
    env[DB_HOST] = "db.producao.interno"
    env[DB_DATABASE] = "app_prod"
    env[DB_PASSWORD] = "segredo_forte"
    ```
  - No **Docker** (`docker-compose.yml` ou `environment` de orquestradores):
    ```yaml
    environment:
      - DB_HOST=db
      - DB_DATABASE=app_prod
      - APP_ENV=production
    ```
  - No **Nginx** (via `fastcgi_param`):
    ```nginx
    fastcgi_param DB_HOST "10.0.0.5";
    ```
