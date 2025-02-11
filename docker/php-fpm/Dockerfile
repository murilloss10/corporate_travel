# Usar a imagem oficial do PHP-FPM como base
FROM php:8.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libmongoc-dev \
    libbson-dev \
    git \
    unzip \
    supervisor

RUN docker-php-ext-install sockets pcntl && \
    docker-php-ext-install pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /var/log/supervisor
COPY docker/supervisor/worker.conf /etc/supervisor/conf.d/worker.conf

WORKDIR /var/www/html

CMD ["supervisord", "-n", "-c", "/etc/supervisor/conf.d/worker.conf"]