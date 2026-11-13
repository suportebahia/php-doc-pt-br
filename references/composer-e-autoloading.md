# Composer, dependências e autoloading

Composer é o gerenciador de dependências mais usado no ecossistema PHP. Use a documentação da versão instalada: recursos de CLI, auditoria e políticas de segurança evoluem. O conteúdo abaixo orienta projetos de aplicação e bibliotecas, que têm políticas de lockfile diferentes.

## `composer.json`, constraints e plataforma

O `composer.json` declara metadados, dependências, requisitos de PHP/extensões, scripts e regras de autoload. Escolha constraints conforme a política de compatibilidade; uma faixa aberta como `>=8.2` pode aceitar versões futuras que não foram testadas.

```json
{
  "name": "empresa/meu-projeto",
  "type": "project",
  "require": {
    "php": ">=8.2"
  },
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  }
}
```

Declare extensões necessárias usando os requisitos Composer apropriados. `config.platform` pode simular versões de PHP/extensões durante resolução, mas não muda o runtime. Use `composer check-platform-reqs` no ambiente real para verificar requisitos da plataforma.

- Fontes: [schema do Composer](https://getcomposer.org/doc/04-schema.md), [configuração Composer](https://getcomposer.org/doc/06-config.md), [check-platform-reqs](https://getcomposer.org/doc/03-cli.md)

## `install`, `update` e `composer.lock`

`composer install` instala as versões registradas em `composer.lock` quando o arquivo existe e está consistente. Sem lock, Composer precisa resolver versões segundo as constraints. `composer update` resolve versões permitidas e altera o lock; revise o diff e rode testes antes de aceitar a mudança.

Aplicações normalmente versionam `composer.lock` para reproduzir deploys. Bibliotecas reutilizáveis frequentemente não versionam lock como contrato para consumidores: o lock da biblioteca não fixa as dependências de quem a instala. Escolha a política que corresponde ao tipo de pacote.

Um fluxo de atualização cuidadoso inclui validar o manifesto, atualizar de forma intencional, revisar mudanças diretas e transitivas, executar testes e análise estática, verificar compatibilidade PHP/extensões e revisar alertas de segurança.

- Fontes: [uso básico do Composer](https://getcomposer.org/doc/01-basic-usage.md), [bibliotecas](https://getcomposer.org/doc/02-libraries.md)

## PSR-4 e autoload

PSR-4 define como prefixos de namespace correspondem a diretórios-base. O case dos nomes e dos caminhos importa para portabilidade entre sistemas de arquivos. O autoloader é normalmente gerado pelo Composer e incluído uma vez no ponto de entrada.

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  }
}
```

Para uma classe `App\Service\Notificador` em `src/Service/Notificador.php`, inclua o autoloader gerado:

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
```

Com PSR-4, adicionar um novo arquivo que segue um mapeamento já configurado normalmente não exige `composer dump-autoload`. Regenerar o autoloader pode ser necessário quando a configuração muda, ou para atualizar mapas otimizados/classmap conforme o modo usado. Não use esse comando como correção automática para todo erro de namespace.

- Fontes: [PSR-4](https://www.php-fig.org/psr/psr-4/), [autoloading Composer](https://getcomposer.org/doc/01-basic-usage.md#autoloading), [schema de autoload](https://getcomposer.org/doc/04-schema.md)

## Auditoria e execução de código

`composer audit` consulta avisos e políticas disponíveis na versão/configuração do Composer usada. Ele pode localizar advisories conhecidos; não comprova ausência de vulnerabilidades nem substitui atualização e revisão.

Composer pode executar scripts do pacote raiz; plugins também executam código. Revise `scripts` e permissões de plugins, mantenha `allow-plugins` restrito aos plugins necessários e use opções como `--no-scripts`/`--no-plugins` apenas quando apropriado ao fluxo. Essas opções podem impedir etapas necessárias e não tornam segura uma dependência desconhecida.

Credenciais para repositórios privados não devem ser gravadas em `composer.json`, no histórico Git ou em logs. Use `auth.json` local/global ou mecanismos de segredos do CI conforme a documentação, ignore arquivos locais sensíveis e revogue/rote credenciais expostas.

- Fontes: [Composer audit e comandos](https://getcomposer.org/doc/03-cli.md), [allow-plugins](https://getcomposer.org/doc/06-config.md), [scripts](https://getcomposer.org/doc/articles/scripts.md), [autenticação para pacotes privados](https://getcomposer.org/doc/articles/authentication-for-private-packages.md)

## Comandos úteis

```bash
composer validate --strict
composer install --no-interaction
composer check-platform-reqs
composer audit
```

Em produção, um projeto de aplicação pode usar `composer install --no-dev --optimize-autoloader --no-interaction` quando o lockfile, a plataforma e a estratégia de deploy estiverem corretos. Confirme as opções e requisitos da versão de Composer adotada; otimização pode ajudar o autoloader, mas o ganho depende da aplicação e do ambiente.
