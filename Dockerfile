FROM php:8.2-fpm-alpine

# Instalar dependências do sistema
RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    icu-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    postgresql-dev \
    g++ \
    oniguruma-dev \
    && docker-php-ext-configure gd \
        --with-jpeg \
        --with-freetype \
        --with-webp \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        intl \
        xml \
        gd \
        zip \
        opcache


# Instalar extensões PHP


# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto
COPY . /var/www

# Instalar dependências do Laravel (em ambiente de desenvolvimento)
# Isso será feito no docker-compose exec para garantir que o volume esteja montado
# RUN composer install --no-dev --optimize-autoloader

# Configurar permissões (para o usuário www-data)
RUN chown -R www-data:www-data /var/www

# Ajustar permissões do Laravel
RUN addgroup -g 1000 www && adduser -G www -g www -s /bin/sh -D www \
    && chown -R www:www /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache


# Expor porta 9000 (porta padrão do PHP-FPM)
EXPOSE 9000

# Comando de inicialização (PHP-FPM)
CMD ["php-fpm"]

USER www

