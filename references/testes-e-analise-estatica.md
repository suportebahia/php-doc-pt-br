# Testes automatizados e análise estática em PHP

Testes e análise estática ajudam a encontrar defeitos nos cenários e configurações cobertos. Não provam que uma aplicação inteira está correta ou segura. Escolha ferramentas e versões compatíveis com o PHP mínimo e consulte a documentação da mesma versão instalada.

## Testes que exercitam o código

Um teste comportamental deve chamar uma função ou objeto sob teste (SUT), não repetir dentro do teste a mesma expressão que depois verifica. Inclua cenários válidos, inválidos e limites relevantes ao domínio.

```php
<?php

declare(strict_types=1);

function calcularPrecoComDesconto(int $precoCentavos, int $percentual): int
{
    if ($precoCentavos < 0 || $percentual < 0 || $percentual > 100) {
        throw new InvalidArgumentException('Valores fora do intervalo permitido.');
    }

    return intdiv($precoCentavos * (100 - $percentual) + 50, 100);
}
```

Em um arquivo de teste PHPUnit separado:

```php
<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PrecoTest extends TestCase
{
    public function testAplicaDesconto(): void
    {
        self::assertSame(8500, calcularPrecoComDesconto(10000, 15));
    }

    public function testRejeitaPercentualInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        calcularPrecoComDesconto(10000, 101);
    }
}
```

Este exemplo ilustra a estrutura de um teste, não uma regra financeira universal. A multiplicação inteira pode exceder limites em domínios de valores muito grandes; escala, arredondamento, moeda e limites devem ser definidos e testados pelo produto.

## Unidade, integração e comportamento

Testes unitários exercitam uma unidade isolada; testes de integração verificam colaboração com banco, filesystem, filas ou serviços; testes funcionais verificam fluxos de aplicação observáveis. Test doubles ajudam a isolar uma dependência, mas não substituem testes com o componente real quando a integração é relevante.

Mantenha casos independentes, dados de teste reproduzíveis e asserts que expressem o requisito. Para erros, verifique não apenas que ocorreu uma exceção, mas também que o estado ou efeito colateral ficou correto.

## Análise estática e estilo

PHPStan analisa código sem executá-lo. Configure caminhos, stubs, extensões e nível no projeto; elevar o nível pode exigir adoção incremental. O resultado depende da configuração e não demonstra ausência de bugs.

```bash
composer require --dev phpstan/phpstan
vendor/bin/phpstan analyse
```

Um formatter verifica convenções de estilo, não semântica. PSR-12 é uma especificação de estilo que pode ser adotada quando adequada à equipe; execute o modo de verificação sem alterações automáticas no CI.

## Cobertura e técnicas adicionais

Cobertura mede código executado pela suíte, conforme o driver e a configuração. Cobertura de linha não é cobertura de branch/path, e um percentual alto não demonstra que os asserts são bons. Inclua apenas arquivos de produção no relatório e interprete lacunas em conjunto com o risco do módulo.

Fuzzing, testes baseados em propriedades e mutation testing podem ser úteis seletivamente para parsers, validação e regras críticas. Ferramentas de segurança automatizadas complementam testes dirigidos e revisão; não substituem avaliação do modelo de ameaças.

## CI e o que este repositório verifica

A integração contínua deste repositório verifica sintaxe de arquivos PHP selecionados, casos textuais, segurança do runner e links. O runner também pode validar a sintaxe dos blocos de código PHP nos documentos; ele não executa esses exemplos. Essa aprovação não é uma suíte de testes de aplicação, nem prova segurança, comportamento em produção, cobertura ou compatibilidade de dependências.

- Fontes: [PHPUnit: escrever testes](https://docs.phpunit.de/en/13.4/writing-tests-for-phpunit.html), [PHPUnit: cobertura](https://docs.phpunit.de/en/13.4/code-coverage.html), [versões PHPUnit suportadas](https://phpunit.de/supported-versions.html), [PHPStan](https://phpstan.org/user-guide/getting-started), [níveis PHPStan](https://phpstan.org/user-guide/rule-levels), [PHP-FIG PSR-12](https://www.php-fig.org/psr/psr-12/)
