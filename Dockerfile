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
    g++

# Instalar extensões PHP
RUN docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

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

# Expor porta 9000 (porta padrão do PHP-FPM)
EXPOSE 9000

# Comando de inicialização (PHP-FPM)
CMD ["php-fpm"]
