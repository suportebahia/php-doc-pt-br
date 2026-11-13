# Datas e fusos horários

Decida se o dado representa um instante, uma data civil ou um horário local. O formato e o fuso devem vir do contrato, não do timezone padrão acidental do servidor. Para instantes, UTC pode ser adequado ao armazenamento; horários civis precisam preservar o fuso/regra de negócio aplicável.

## Parsing com validação

`DateTimeImmutable::createFromFormat()` pode normalizar alguns valores inválidos e emitir avisos. Verifique tanto o resultado quanto os erros/avisos; compare a representação quando o formato de entrada tiver de ser exato.

```php
<?php

declare(strict_types=1);

$texto = '2026-02-28';
$data = DateTimeImmutable::createFromFormat('!Y-m-d', $texto, new DateTimeZone('UTC'));
$erros = DateTimeImmutable::getLastErrors();

if ($data === false
    || ($erros !== false && ($erros['warning_count'] > 0 || $erros['error_count'] > 0))
    || $data->format('Y-m-d') !== $texto) {
    throw new InvalidArgumentException('Data inválida ou fora do formato esperado.');
}
```

Use objetos imutáveis quando a semântica do código se beneficiar de não alterar a instância existente. A aritmética de calendário não é sempre igual a adicionar uma duração fixa de segundos.

## Horário civil e DST

Fusos IANA, como `America/Sao_Paulo`, representam regras locais que podem mudar. Transições de horário podem criar horários inexistentes ou ambíguos. Para agendamentos, defina como o produto trata esses casos e, se necessário, peça offset/fuso explícito. Teste as transições pertinentes ao local e mantenha a base de fusos do runtime atualizada.

A timezone padrão do processo pode diferir entre CLI, FPM e workers. Defina timezone de forma explícita onde o contrato exigir e não dependa do fuso da máquina em testes.

- Fontes: [`DateTimeImmutable::createFromFormat`](https://www.php.net/manual/pt_BR/datetimeimmutable.createfromformat.php), [`getLastErrors`](https://www.php.net/manual/pt_BR/datetimeimmutable.getlasterrors.php), [fusos suportados](https://www.php.net/manual/pt_BR/timezones.php)
