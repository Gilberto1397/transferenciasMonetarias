FROM php:8.3-cli

# Apenas o necessário para baixar dependências PHP
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer oficial
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
