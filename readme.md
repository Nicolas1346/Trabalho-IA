# Rezze Imóveis: busca inteligente com IA

Trabalho prático da disciplina **Sistemas Inteligentes** (PHP + Hugging Face).

## Integrantes

- (coloque aqui seu nome e o da dupla, se houver)

## Objetivo

Site de imóveis em PHP em que o usuário descreve, em linguagem natural, o que está procurando (por exemplo: *"algo barato para estudante perto da faculdade"*). Um modelo de IA lê o catálogo de imóveis, cruza com o pedido e recomenda os imóveis mais adequados, explicando o motivo.

## API / modelo utilizado

| Item | Informação |
|---|---|
| **Modelo** | Qwen3-32B (equipe Qwen, da Alibaba) |
| **Página no Hugging Face** | https://huggingface.co/Qwen/Qwen3-32B |
| **Tipo** | Modelo de linguagem (LLM) de propósito geral, licença Apache 2.0 |
| **Acesso** | Hugging Face Inference Providers, com token gratuito |
| **Endpoint** | `https://router.huggingface.co/v1/chat/completions` |
| **Formato** | "Chat completions" (mensagens com papéis `system` e `user`) |

**Entrada:** texto. Duas mensagens: uma de sistema (instruções + catálogo de imóveis em JSON) e uma do usuário (o pedido digitado).

**Saída:** texto em linguagem natural com a recomendação e a justificativa.

### Por que este modelo

A tarefa não é uma classificação simples: é interpretar texto livre e cruzá-lo com dados estruturados. Isso exige um modelo de linguagem completo, com boa compreensão de português e capacidade de seguir instruções. O Qwen3-32B atende a isso, é gratuito e usa uma API em formato padrão de mercado, fácil de integrar em PHP com cURL.

## Como funciona

1. Em `imoveis.php`, o usuário digita o pedido no campo de busca e envia o formulário (POST).
2. O `buscar.php` recebe o texto, remove espaços (`trim`) e valida se não está vazio.
3. O catálogo (`data/imoveis.json`) é carregado e convertido em texto JSON.
4. O PHP monta o corpo da requisição: instrução + catálogo (mensagem `system`) e o pedido (mensagem `user`).
5. Com **cURL**, o PHP envia a requisição à API do Hugging Face, com o token no cabeçalho `Authorization`.
6. A resposta chega em JSON. O PHP confere erros (conexão, código HTTP diferente de 200, formato inesperado) e extrai o texto de `choices[0].message.content`.
7. O bloco de raciocínio interno do modelo (`<think>...</think>`) é removido e a resposta é exibida na tela.

A chamada à API acontece no `buscar.php`, na função `curl_exec`. O navegador nunca acessa a API diretamente, então o token fica protegido no servidor.

## Estrutura do projeto

```
├── index.php          # Página inicial
├── imoveis.php        # Lista de imóveis + formulário de busca
├── buscar.php         # Integração com a API (cURL) e exibição da recomendação
├── detalhe.php        # Detalhes do imóvel + botão de contato (WhatsApp)
├── sobre.php          # Página institucional
├── config.php         # Lê o .env e define token, modelo e endpoint
├── data/imoveis.json  # Catálogo de imóveis
├── style.css / imoveis.css / sobre.css
├── .env.example       # Modelo do arquivo de configuração
└── .gitignore         # Impede o envio do .env ao GitHub
```

## Como executar

**Requisitos:** PHP 7.4 ou superior, com as extensões `curl` e `openssl` habilitadas, e acesso à internet.

1. Clone o repositório e entre na pasta do projeto.
2. Crie uma conta em https://huggingface.co e gere um token em **Settings → Access Tokens**. Escolha o tipo *Fine-grained* e marque apenas a permissão **"Make calls to Inference Providers"**.
3. Copie o arquivo `.env.example` para `.env` e coloque o seu token:
   ```
   HF_TOKEN=seu_token_aqui
   ```
4. Inicie o servidor embutido do PHP:
   ```
   php -S localhost:8000
   ```
5. Abra `http://localhost:8000/index.php` no navegador e vá em **Imóveis** para usar a busca.

## Segurança do token

O token da API **não está no código nem no repositório**. Ele fica no arquivo `.env`, que está listado no `.gitignore` e por isso não é enviado ao GitHub. O `config.php` lê esse arquivo em tempo de execução. Para configurar, siga o passo 3 acima, sem publicar o seu token em lugar nenhum.

## Exemplo de uso

**Entrada (pedido do usuário):**

```
Quero uma casa grande perto da praia
```

**Resultado (exemplo de resposta da IA):**

```
Recomendação: Casa Espaçosa Próxima à Praia (Barra da Tijuca, Rio de Janeiro - RJ)
Valor: R$ 2.800.000

Motivo: o imóvel fica na Barra da Tijuca, com vista para o mar, tem 390 m²,
4 quartos e ampla área externa, o que atende ao pedido de uma casa grande
próxima ao litoral. Nenhum outro imóvel do catálogo fica em região de praia.
```

## Tratamento de erros

O `buscar.php` trata três situações: falha de conexão (`curl_error`), resposta da API com erro (código HTTP diferente de 200, como 401 para token inválido) e resposta em formato inesperado. Em cada caso, uma mensagem clara é exibida em vez de uma página quebrada.

## Limitações

- A IA pode errar ou omitir detalhes, por ser um modelo de linguagem e não um banco de dados.
- O catálogo inteiro é enviado a cada busca. Funciona bem com poucos imóveis, mas não escalaria para milhares.
- A resposta é texto livre: o site não destaca automaticamente o card recomendado.
