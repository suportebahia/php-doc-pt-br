# Composer e Autoloading (PSR-4)

Padrão da indústria para gerenciamento de dependências, autoloading de classes e estruturação de projetos modernos em PHP.

---

## 1. O que é o Composer?
O **Composer** é o gerenciador de pacotes padrão do ecossistema PHP. Ele resolve dependências, baixa bibliotecas da comunidade (via Packagist) e gera automaticamente o mecanismo de carregamento de classes (**Autoloader**), eliminando a necessidade de múltiplos `require` ou `include` manuais.

- Site oficial: [getcomposer.org](https://getcomposer.org/)
- Repositório central de pacotes: [packagist.org](https://packagist.org/)

---

## 2. Estrutura Padrão de Diretórios
Um projeto PHP profissional estruturado com Composer segue a convenção:

```text
meu-projeto/
├── src/                  # Código-fonte da aplicação (classes)
│   ├── Controller/
│   ├── Model/
│   └── Service/
├── tests/                # Testes automatizados (PHPUnit)
├── vendor/               # Dependências baixadas pelo Composer (IGNORADO no .gitignore)
├── composer.json         # Manifesto do projeto e dependências
├── composer.lock         # Versões exatas instaladas (VERSIONADO no Git)
└── public/               # Ponto de entrada web (DocumentRoot do servidor)
    └── index.php
```

---

## 3. Autoloading com o Padrão PSR-4
A norma **PSR-4 (PHP Standards Recommendation 4)** define como namespaces de classes correspondem diretamente à estrutura de pastas no disco.

### Configuração no `composer.json`:
```json
{
    "name": "empresa/meu-projeto",
    "description": "Aplicação PHP moderna desenvolvida nos padrões oficiais",
    "type": "project",
    "license": "MIT",
    "require": {
        "php": ">=8.2"
    },
    "require-dev": {
        "phpunit/phpunit": "^11.0",
        "phpstan/phpstan": "^1.11"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "App\\Tests\\": "tests/"
        }
    }
}
```

### Como funciona na prática:
Se você criar uma classe em `src/Service/NotificadorEmail.php`:
```php
<?php

declare(strict_types=1);

namespace App\Service;

class NotificadorEmail
{
    public function enviar(string $destinatario, string $mensagem): bool
    {
        // Lógica de envio
        return true;
    }
}
```

No ponto de entrada (`public/index.php`), basta incluir o autoloader uma única vez:
```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Service\NotificadorEmail;

$notificador = new NotificadorEmail();
$notificador->enviar('dev@empresa.com', 'Bem-vindo ao PHP moderno!');
```
- Fonte oficial: [PHP-FIG PSR-4](https://www.php-fig.org/psr/psr-4/)

---

## 4. Comandos Essenciais do Composer

| Comando | Finalidade |
| :--- | :--- |
| `composer init` | Inicia um novo `composer.json` interativamente. |
| `composer install` | Instala as dependências exatas travadas no `composer.lock`. |
| `composer update` | Atualiza as dependências para as versões mais recentes permitidas e recria o `lock`. |
| `composer require <pacote>` | Adiciona uma dependência de produção em `require`. |
| `composer require --dev <pacote>` | Adiciona uma dependência de desenvolvimento (testes, linters) em `require-dev`. |
| `composer dump-autoload` | Regenera o mapa de classes do autoloader após criar novos arquivos. |
| `composer audit` | Verifica vulnerabilidades de segurança conhecidas nas bibliotecas instaladas. |

---

## 5. Boas Práticas em Produção
No ambiente de produção (deploy), nunca instale dependências de teste e otimize o mapa de carregamento para máximo desempenho:
```bash
composer install --no-dev --optimize-autoloader --no-interaction
```
- `--no-dev`: Não baixa bibliotecas de teste (PHPUnit, PHPStan), reduzindo o tamanho do deploy.
- `--optimize-autoloader`: Converte a resolução PSR-4 em um mapa estático de arquivos (`classmap`), acelerando drasticamente o tempo de resposta do PHP.
