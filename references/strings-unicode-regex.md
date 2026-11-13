# Strings, Unicode e expressões regulares

Strings PHP são sequências de bytes; operações comuns de string não necessariamente contam caracteres visíveis. Defina a codificação de entrada e saída pelo contrato e verifique extensões opcionais como `mbstring` e `intl` no runtime usado.

## Unicode e codificação

Não tente adivinhar a codificação de uma entrada sem um contrato. Valide bytes para a codificação esperada e converta apenas quando conhecer a origem. `mb_strlen()` mede unidades segundo a codificação, que não equivalem necessariamente a grafemas exibidos na tela.

Normalização Unicode pode ser necessária para comparação, busca ou identificadores, mas a forma escolhida depende do domínio. Não normalize senhas, assinaturas ou dados cujo formato original precise ser preservado sem uma decisão explícita.

- Fontes: [`mb_check_encoding`](https://www.php.net/manual/pt_BR/function.mb-check-encoding.php), [mbstring](https://www.php.net/manual/pt_BR/book.mbstring.php), [`Normalizer::normalize`](https://www.php.net/manual/pt_BR/normalizer.normalize.php)

## PCRE

`preg_match()` retorna `1` para correspondência, `0` para ausência e `false` para erro. Não trate `false` como “não corresponde”. Se usar o modificador Unicode, valide ou normalize encoding conforme o contrato e trate erros PCRE.

```php
<?php

declare(strict_types=1);

$resultado = preg_match('/\A[A-Z]{2}\d{6}\z/', $codigo);
if ($resultado === false) {
    throw new RuntimeException('Falha ao executar a expressão regular.');
}
if ($resultado === 0) {
    throw new InvalidArgumentException('Código fora do formato esperado.');
}
```

Padrões e entradas hostis podem consumir tempo/recursos. Prefira padrões delimitados e simples, limites de tamanho e testes com entradas longas; verifique a configuração PCRE e o tipo de dados real. Regex não substitui parser de protocolo nem validação completa de Unicode.

- Fontes: [`preg_match`](https://www.php.net/manual/pt_BR/function.preg-match.php), [`preg_last_error`](https://www.php.net/manual/pt_BR/function.preg-last-error.php), [PCRE](https://www.php.net/manual/pt_BR/book.pcre.php)
