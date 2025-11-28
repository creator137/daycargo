FROM php:8.2-fpm

ARG DEBIAN_FRONTEND=noninteractive

# PHP расширения + утилиты
RUN set -eux; \
    apt-get update; \
    apt-get install -y \
    git unzip curl gnupg ca-certificates \
    libzip-dev libicu-dev libonig-dev libpq-dev; \
    docker-php-ext-install pdo pdo_mysql intl zip bcmath pcntl; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    rm -rf /var/lib/apt/lists/*

# Node.js 20 LTS (для Vite)
RUN set -eux; \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -; \
    apt-get update; \
    apt-get install -y nodejs; \
    node -v && npm -v; \
    rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# на всякий — включим opcache если доступен
RUN docker-php-ext-enable opcache || true
