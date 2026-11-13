# Guia de Contribuição — php-doc-pt-br

Agradecemos o seu interesse em contribuir com o **`php-doc-pt-br`**! Este projeto tem como missão elevar a qualidade, segurança e modernidade do código PHP gerado por agentes de IA e adotado por desenvolvedores da comunidade lusófona.

---

## 🎯 O Contrato de Contribuição (Leia o `AGENTS.md`)
Antes de propor alterações ou adicionar novas referências, você **deve** ler o arquivo [AGENTS.md](AGENTS.md). 

Toda contribuição deve respeitar o **Filtro dos 6 Meses**:
1. Esta afirmação continuará verdadeira daqui a 6 meses?
2. Faz sentido para quem nunca viu este repositório antes?
3. O conteúdo é estritamente atemporal (sem prazos curtos ou contextos pessoais)?
4. Há link direto para a documentação canônica em `https://www.php.net/manual/pt_BR/`?

---

## 🛠️ Como Contribuir

### 1. Reportando Problemas ou Alucinações de IA
Se você notou que a IA gerou código inseguro, com funções depreciadas ou com alucinações ao usar esta skill:
- Abra uma **Issue** no GitHub.
- Descreva o prompt exato que você usou, a resposta que a IA deu e o que a documentação oficial diz a respeito.

### 2. Propondo Novos Guias em `references/`
- Crie guias curtos, canônicos e focados em problemas reais.
- Mantenha a estrutura padrão:
  1. Explicação técnica concisa.
  2. Exemplo de código no padrão moderno (PHP 8.2+, `declare(strict_types=1);`).
  3. Seção final de **Fonte** com o link direto para `https://www.php.net/manual/pt_BR/...`.

### 3. Adicionando Novos Testes no Test Harness (`tests/evals/`)
Adoraríamos que a comunidade ampliasse a suíte de testes automáticos da skill:
1. Abra `tests/evals/run-evals.php`.
2. Adicione um novo método de teste (ex: `testeProtecaoCsrf()`).
3. Adicione o arquivo correspondente em `tests/evals/fixtures/`.
4. Execute `php tests/evals/run-evals.php` no seu terminal e certifique-se de que todos os testes passaram.

---

## 🚀 Fluxo de Pull Request (PR)

1. Faça um Fork do repositório.
2. Crie uma branch para sua modificação:
   ```bash
   git checkout -b feat/nova-referencia-seguranca
   ```
3. Execute o Test Harness para validar a integridade:
   ```bash
   php tests/evals/run-evals.php
   ```
4. Faça commit seguindo o padrão de commits semânticos:
   - `feat:` para novos guias ou testes.
   - `fix:` para correção de links quebrados ou imprecisões no texto.
   - `docs:` para melhorias nas explicações educacionais.
5. Abra o Pull Request detalhando as melhorias propostas.
