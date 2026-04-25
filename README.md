# Rodando o projeto localmente com Docker

Este projeto é um aplicativo Laravel. A forma mais prática de executá-lo localmente via Docker é:

1. construir a imagem base usada para o Composer;
2. instalar as dependências PHP dentro de um container;
3. preparar o arquivo `.env`;
4. subir os serviços do projeto;
5. gerar a chave da aplicação, aplicar migrations e seeders;
6. validar com os testes e com o PHPStan.

## Pré-requisitos

- Docker instalado;
- Docker Compose disponível;
- acesso ao repositório clonado localmente.

## 1) Construir a imagem do Composer

Na raiz do projeto, gere a imagem inicial usada para instalar as dependências:

```bash
docker build -t composer-php83-bootstrap .
```

## 2) Instalar as dependências PHP

Use a imagem criada no passo anterior para executar o Composer no diretório do projeto:

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD":/app -w /app composer-php83-bootstrap composer install --no-scripts
```

> O `--no-scripts` evita a execução automática de hooks do Composer durante a instalação inicial.

## 3) Preparar o arquivo de ambiente

Crie o arquivo `.env` a partir do exemplo, caso ele ainda não exista:

```bash
cp .env.example .env
```

### Banco de dados com PostgreSQL via Docker Compose

use o serviço `pgsql` definido no `docker-compose.yml`, ajuste o `.env` para algo compatível com o container:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
QUEUE_CONNECTION=database
```

> Os nomes exatos de usuário/senha podem ser ajustados, desde que fiquem coerentes com o `docker-compose.yml` e com o arquivo `.env`.

## 4) Subir os serviços do projeto

Com o ambiente preparado, suba os containers do projeto:

```bash
./vendor/bin/sail up -d
```

## 5) Gerar a chave da aplicação

Assim como no CI, gere a chave do Laravel:

```bash
./vendor/bin/sail artisan key:generate
```

## 6) Recarregar a configuração

No CI é executado `php artisan config:cache`. Localmente, após alterar o `.env`, faça o mesmo dentro do container:

```bash
./vendor/bin/sail artisan config:cache
```

## 7) Executar migrations e seeders

Depois do ambiente pronto, aplique a estrutura do banco e os dados iniciais:

```bash
./vendor/bin/sail artisan migrate
```

```bash
./vendor/bin/sail artisan db:seed
```

## Para executar os testes automatizados e o PHPStan

### PHPStan

```bash
./vendor/bin/sail composer phpStan
```

### Testes automatizados

```bash
./vendor/bin/sail composer test
```

## Modelo Entidade-Relacionamento da aplicação

Diagrama do modelo entidade-relacionamento (MER):
https://dbdiagram.io/d/MER-transferencias-693325ba3c4ea889c6be1a9b

## Diagramas de classes e casos de uso
### No diretório `docs/diagrams` estão os diagramas de casos de uso e de classes, ambos em formato Mermaid e pdf, para facilitar a leitura e manutenção.


