# Testes automatizados e análise estática em PHP

Testes e análise estática ajudam a encontrar defeitos, mas cobrem apenas os cenários, regras e configurações incluídos. Não provam que uma aplicação inteira está correta ou segura. Escolha ferramentas e versões compatíveis com o PHP e o projeto.

## Análise estática

Ferramentas como PHPStan analisam o código sem executá-lo e podem apontar problemas de tipos, chamadas e fluxo de dados conforme a configuração e os stubs disponíveis. O resultado depende do nível, das extensões e das informações fornecidas. Uma análise sem erros não demonstra ausência de bugs.

Instale uma versão compatível com o PHP do projeto e siga a documentação correspondente:

```bash
composer require --dev phpstan/phpstan
vendor/bin/phpstan analyse
```

Configure os caminhos e o nível de análise de forma incremental. Um nível mais alto pode revelar mais inconsistências, mas não é automaticamente o melhor ponto de partida para todos os projetos.

## Testes com PHPUnit

PHPUnit executa os testes escritos pelo projeto. Selecione uma versão compatível com a versão mínima de PHP e com as dependências; consulte a documentação da mesma versão instalada.

```bash
composer require --dev phpunit/phpunit
vendor/bin/phpunit
```

Um arquivo `phpunit.xml` básico pode definir o autoloader e a pasta de testes. As opções e o esquema XML variam entre versões do PHPUnit; use o exemplo oficial compatível com a versão selecionada.

Um teste unitário pequeno:

```php
<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CalculadoraDescontoTest extends TestCase
{
    public function testCalculaDesconto(): void
    {
        $resultado = 100.0 - (100.0 * 15.0 / 100.0);
        self::assertSame(85.0, $resultado);
    }
}
```

Este caso verifica apenas essa operação e entrada. Para requisitos financeiros, inclua escala decimal, arredondamento e moeda conforme o domínio, em vez de presumir que `float` é adequado.

## Usar os resultados

Ligue os comandos à integração contínua se a equipe quiser que falhas bloqueiem o fluxo de revisão ou publicação. Um teste que não é executado pelo CI não bloqueia deploy por si só. Revise também se a suíte cobre cenários negativos, permissões e limites relevantes; os próprios testes podem estar incompletos.

- Documentação: [PHPStan](https://phpstan.org/user-guide/getting-started), [PHPUnit](https://docs.phpunit.de/)
