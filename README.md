# Desafio Back-end

[![Build Status](https://github.com/fnsc/transactions-api/workflows/Tests/badge.svg)](https://github.com/fnsc/cpf-cnpj-validation/actions?query=workflow%3ATests)

Primeiramente, obrigado pelo seu interesse em trabalhar na melhor plataforma de pagamentos do mundo!
Abaixo você encontrará todos as informações necessárias para iniciar o seu teste.

### Sobre o ambiente da aplicação:

- Escolha qualquer framework que se sinta **confortável** em trabalhar. Esse teste **não faz** nenhuma preferência, portanto decida por aquele com o qual estará mais seguro em apresentar e conversar com a gente na entrevista ;)

- Você pode, inclusive, não optar por framework nenhum. Neste caso, recomendamos a implementação do serviço via script para diminuir a sobrecarga de criar um servidor web.

- Ainda assim, se optar por um framework tente evitar usar muito métodos mágicos ou atalhos já prontos. Sabemos que essas facilidades aumentam a produtividade no dia-a-dia mas aqui queremos ver o **seu** código e a sua forma de resolver problemas.

- Valorizamos uma boa estrutura de containeres criada por você.

## Para o dia da entrevista técnica
Na data marcada pelo recrutador tenha sua aplicação rodando na sua máquina local para execução dos testes e para nos mostrar os pontos desenvolvidos e possíveis questionamentos.
Faremos um code review junto contigo como se você já fosse do nosso time :heart:, você poderá explicar o que você pensou, como arquitetou e como pode evoluir o projeto.

## Objetivo:

Temos 2 tipos de usuários, os comuns e lojistas, ambos têm carteira com dinheiro e realizam transferências entre eles. Vamos nos atentar **somente** ao fluxo de transferência entre dois usuários.

Requisitos:

- [x] Para ambos tipos de usuário, precisamos do Nome Completo, CPF, e-mail e Senha. CPF/CNPJ e e-mails devem ser únicos no sistema. Sendo assim, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou endereço de e-mail.

- [x] Usuários podem enviar dinheiro (efetuar transferência) para lojistas e entre usuários. 

- [x] Lojistas **só recebem** transferências, não enviam dinheiro para ninguém.

- [x] Validar se o usuário tem saldo antes da transferência.

- [x] Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo, use este mock para simular (https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6).

- [x] A operação de transferência deve ser uma transação (ou seja, revertida em qualquer caso de inconsistência) e o dinheiro deve voltar para a carteira do usuário que envia. 

- [x] No recebimento de pagamento, o usuário ou lojista precisa receber notificação (envio de email, sms) enviada por um serviço de terceiro e eventualmente este serviço pode estar indisponível/instável. Use este mock para simular o envio (http://o4d9z.mocklab.io/notify). 

- [x] Este serviço deve ser RESTFul.

### Payload

Faça uma **proposta** :heart: de payload, se preferir, temos uma exemplo aqui:

POST /transaction

```json
{
    "value" : 100.00,
    "payer" : 4,
    "payee" : 15
}
```


# Avaliação

Apresente sua solução utilizando o framework que você desejar, justificando a escolha.
Atente-se a cumprir a maioria dos requisitos, pois você pode cumprir-los parcialmente e durante a avaliação vamos bater um papo a respeito do que faltou.

Teremos 2 partes da avaliação:

A correção objetiva será realizada através da utilização de um script de correção automatizada. Você **pode** rodar na sua máquina local ou usar outra ferramenta:
```bash
docker run -it --rm -v $(pwd):/project -w /project jakzal/phpqa phpmd app text cleancode,codesize,controversial,design,naming,unusedcode
```    

A correção qualitativa será durante a entrevista e levará em conta os seguintes critérios:

## O que será avaliado e valorizamos :heart:
- Documentação
- Se for para vaga sênior, foque bastante no **desenho de arquitetura**
- Código limpo e organizado (nomenclatura, etc)
- Conhecimento de padrões (PSRs, design patterns, SOLID)
- Ser consistente e saber argumentar suas escolhas
- Apresentar soluções que domina
- Modelagem de Dados
- Manutenibilidade do Código
- Tratamento de erros
- Cuidado com itens de segurança
- Arquitetura (estruturar o pensamento antes de escrever)
- Carinho em desacoplar componentes (outras camadas, service, repository)

De acordo com os critérios acima, iremos avaliar seu teste para avançarmos para a entrevista técnica.
Caso não tenha atingido aceitavelmente o que estamos propondo acima, não iremos prosseguir com o processo.

## O que NÃO será avaliado :warning:
- Fluxo de cadastro de usuários e lojistas
- Frontend (só avaliaremos a [API Restful](https://www.devmedia.com.br/rest-tutorial/28912))
- Autenticação

## O que será um Diferencial
- Uso de Docker
- Testes de [integração](https://www.atlassian.com/continuous-delivery/software-testing/types-of-software-testing)
- Testes [unitários](https://www.atlassian.com/continuous-delivery/software-testing/types-of-software-testing)
- Uso de Design Patterns
- Documentação
- Proposta de melhoria na arquitetura

## Swagger
O arquivo na raiz do projeto `api_swagger.yaml` contem as instruções necessárias para rodar os principais endpoints.

## Setup
Após clonar o repositório na sua máquina, rode os seguintes comandos:
1. Copie o arquivo .env.example:
    ```shell
    cp .env.example .env
    ```
2. Build das imagens docker:
    ```shell
    docker-compose build --no-cache web
    ```
3. Build das imagens docker:
    ```shell
    docker-compose up -d web
    ```
4. Gere a chave para a aplicação:
    ```shell
    docker-compose exec web php artisan key:generate
    ```
5. Rode as migrations:
    ```shell
    docker-compose exec web php artisan migrate
    ```
6. Acesse [http://localhost:8100](http://localhost:8100)
