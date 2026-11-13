# Guia Anti-Alucinação para Iniciantes

Como auditar em 5 minutos o código gerado por qualquer IA e ter certeza de que você recebeu código seguro, moderno e baseado no manual oficial do PHP.

---

## 🚩 Os 7 Sinais de Alerta (Red Flags)
Se o código gerado pela IA contiver qualquer um dos itens abaixo, **a IA alucinou ou usou dados defasados da internet**. Rejeite o código e aplique o prompt de correção:

| # | Sinal de Alerta no Código | Por que é Perigoso? | O que DEVE Estar no Lugar |
| :-: | :--- | :--- | :--- |
| **1** | Falta `declare(strict_types=1);` no topo | O PHP converterá tipos silenciosamente (ex: `"10"` virando número), causando bugs imprevisíveis. | Deve ser a primeira linha após `<?php`. |
| **2** | Concatenação em SQL: `"... WHERE id = $id"` | **SQL Injection Crítico!** Qualquer um pode invadir seu banco de dados. | Deve usar PDO com `prepare()` e `:id` parametrizado. |
| **3** | Uso de `md5()` ou `sha1()` para senhas | Senhas com MD5 são quebradas em segundos por invasores. | Deve usar `password_hash()` e `password_verify()`. |
| **4** | Funções `mysql_*` ou `mysqli` sem prepared | Extensão `mysql_*` foi **removida** do PHP moderno no PHP 7.0. | Deve usar a extensão nativa **PDO**. |
| **5** | Tag de fechamento `?>` no final do arquivo | Pode enviar espaços em branco acidentais e quebrar cabeçalhos HTTP/cookies. | Arquivos puramente PHP **nunca** fecham com `?>`. |
| **6** | Validação de upload por extensão ou `$_FILES['type']` | Invasores podem renomear um vírus para `.jpg` ou forjar o header. | Deve usar a extensão nativa `finfo` (MIME real). |
| **7** | Saída HTML com `echo $_POST['...']` direto | **XSS (Cross-Site Scripting)!** Invasores podem injetar JavaScript no seu site. | Deve usar `htmlspecialchars(..., ENT_QUOTES \| ENT_SUBSTITUTE)`. |

---

## ⏱️ O Checklist dos 5 Minutos (Auditoria Rápida)
Quando a IA terminar de gerar o seu arquivo PHP, faça esta checagem visual:

1. [ ] A primeira linha do arquivo é exatamente `declare(strict_types=1);`?
2. [ ] Todas as funções e métodos têm tipos declarados? (Ex: `function somar(int $a, int $b): int`)
3. [ ] Não há nenhuma tag `?>` na última linha do arquivo?
4. [ ] Se mexer com banco de dados, todas as consultas usam `->prepare(` e `->execute(`?
5. [ ] A resposta da IA terminou com links para o manual oficial `https://www.php.net/manual/pt_BR/...`?

Se todas as respostas forem **SIM**, o código é confiável e segue o padrão oficial!

---

## ⚡ Prompt Corretivo de 1 Linha (Para Quando a IA Errar)
Se a IA vacilar e gerar código antigo, copie e cole este comando corretivo:

> *"Você gerou código com práticas antigas ou vulneráveis. Ative a skill `php-doc-pt-br`, remova a tag de fechamento ?>, aplique `declare(strict_types=1);`, use exclusivamente PDO com Prepared Statements e fundamente com os links do manual oficial em pt-BR."*
