FROM php:8.2-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev \
    libssl-dev libcurl4-openssl-dev \
    pkg-config libpq-dev \
    npm \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Установка расширения Redis
RUN pecl install redis && docker-php-ext-enable redis

# Установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Установка Node.js (для Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Рабочая директория
WORKDIR /var/www

# Копирование entrypoint
COPY docker/php/docker-entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Открываем порт php-fpm
EXPOSE 9000

CMD ["php-fpm"]
