# syntax=docker/dockerfile:1.6
#
# Imagen Docker para SISCON 2 (Gerenciadora Ortopédica - Global Médica / APOS La Rioja)
# Stack:  Laravel 8 + PHP 7.4 (Apache) + Node 10.19.0 (assets) + Composer 2
#
# Build:
#   docker build -t siscon2:latest .
#
# Run (ejemplo, con .env montado):
#   docker run --rm -p 8080:80 --env-file .env siscon2:latest
#

# =============================================================================
# Stage 1 - Compilación de assets con Node (laravel-mix 6 requiere Node >= 12.14)
#
# NOTA: aunque en producción corre Node 10.19.0, esa versión NO sirve para
# compilar laravel-mix 6 (necesita Node >= 12.14). Como además la imagen
# `node:10.19.0-buster` quedó rota (Debian Buster está EOL desde 2024-06),
# usamos node:18-alpine para el build. El output son JS/CSS estáticos, así
# que la versión de Node usada para compilar no afecta el runtime.
# =============================================================================
FROM node:18-alpine AS node-builder

WORKDIR /app

# Manifiestos primero para aprovechar el cache
COPY package.json package-lock.json ./

# npm ci es más estricto pero requiere lockfile sano; si falla, fallback a install
RUN npm ci --no-audit --no-fund \
 || npm install --no-audit --no-fund

# Solo lo necesario para que mix compile (no copiamos toda la app aquí)
COPY webpack.mix.js ./
COPY resources    ./resources
COPY public       ./public

# Compilar assets para producción (laravel-mix)
RUN npm run production


# =============================================================================
# Stage 2 - Dependencias de Composer (sin dev, optimizadas)
# =============================================================================
FROM composer:2 AS composer-builder

WORKDIR /app

# Solo el mínimo para resolver dependencias (mejor cache)
COPY composer.json composer.lock ./
COPY database ./database

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --ignore-platform-reqs


# =============================================================================
# Stage 3 - Imagen final: PHP 7.4 + Apache + todas las extensiones pedidas
# =============================================================================
FROM php:7.4-apache AS app

LABEL maintainer="Heap LR" \
      project="SISCON 2 - Sistema de Gestión y Control" \
      org.opencontainers.image.source="https://github.com/heaplr/siscon2_prod"

# -----------------------------------------------------------------------------
# Variables de entorno
# -----------------------------------------------------------------------------
ENV TZ=America/Argentina/Buenos_Aires \
    APP_ENV=production \
    APP_DEBUG=false \
    APACHE_DOCUMENT_ROOT=/var/www/html/public \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer

# -----------------------------------------------------------------------------
# Paquetes del sistema + librerías de desarrollo para compilar extensiones PHP
# -----------------------------------------------------------------------------
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        # Utilidades runtime
        ca-certificates \
        curl \
        git \
        unzip \
        zip \
        cron \
        supervisor \
        tzdata \
        gettext \
        default-mysql-client \
        # Librerías para extensiones PHP
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libxpm-dev \
        libzip-dev \
        libxml2-dev \
        libxslt1-dev \
        libonig-dev \
        libicu-dev \
        libsodium-dev \
        libffi-dev \
        libcurl4-openssl-dev \
        libreadline-dev \
        libssl-dev \
        libbz2-dev; \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone; \
    rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------------------
# Configurar y compilar extensiones PHP
#
# Las siguientes ya vienen habilitadas en la imagen oficial php:7.4 y NO se
# instalan acá (chequeado con `php -m`):
#   Core, ctype, curl, date, dom, fileinfo, filter, ftp, hash, iconv, json,
#   libxml, mbstring, mysqlnd, openssl, pcre, PDO, Phar, posix, readline,
#   Reflection, session, SimpleXML, SPL, standard, tokenizer, xml,
#   xmlreader, xmlwriter, zlib
# -----------------------------------------------------------------------------
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm

RUN docker-php-ext-install -j"$(nproc)" \
        bcmath \
        calendar \
        exif \
        ffi \
        gd \
        gettext \
        mysqli \
        opcache \
        pcntl \
        pdo_mysql \
        shmop \
        soap \
        sockets \
        sodium \
        sysvmsg \
        sysvsem \
        sysvshm \
        xsl \
        zip

# -----------------------------------------------------------------------------
# php.ini de producción + ajustes propios + OPcache
# -----------------------------------------------------------------------------
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN { \
    echo 'memory_limit=512M'; \
    echo 'upload_max_filesize=64M'; \
    echo 'post_max_size=64M'; \
    echo 'max_execution_time=300'; \
    echo 'max_input_time=300'; \
    echo 'date.timezone=America/Argentina/Buenos_Aires'; \
} > /usr/local/etc/php/conf.d/zz-app.ini

RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/zz-opcache.ini

# -----------------------------------------------------------------------------
# Composer (binario tomado de la imagen oficial composer:2)
# -----------------------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# -----------------------------------------------------------------------------
# Apache: mod_rewrite + DocumentRoot apuntando a /public (Laravel)
# -----------------------------------------------------------------------------
RUN a2enmod rewrite headers expires \
 && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g'    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# -----------------------------------------------------------------------------
# Copiar la aplicación
# -----------------------------------------------------------------------------
WORKDIR /var/www/html

# 1) Código de la app (respetando .dockerignore)
COPY . .

# 2) Vendor desde el stage de composer
COPY --from=composer-builder /app/vendor ./vendor

# 3) Assets compilados desde el stage de node (sobreescriben los del repo)
COPY --from=node-builder /app/public ./public

# Regenerar autoloader optimizado con todo el código presente.
# --no-scripts: evita correr `artisan package:discover` durante el build
# (no hay .env disponible aún; Laravel lo regenera en el primer request).
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative --no-scripts

# -----------------------------------------------------------------------------
# Permisos para Laravel (storage y bootstrap/cache deben ser escribibles)
# -----------------------------------------------------------------------------
RUN chown -R www-data:www-data /var/www/html \
 && find /var/www/html -type d -exec chmod 755 {} \; \
 && find /var/www/html -type f -exec chmod 644 {} \; \
 && chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

# -----------------------------------------------------------------------------
# Puerto, healthcheck y arranque
# -----------------------------------------------------------------------------
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -fsS http://localhost/ > /dev/null || exit 1

# Apache en foreground (lo provee la imagen base php:7.4-apache)
CMD ["apache2-foreground"]
