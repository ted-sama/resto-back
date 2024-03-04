# Dockerfile
FROM php:8.2-fpm

RUN apt-get update -y && apt-get install -y libmcrypt-dev && apt-get install -y wget

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer
RUN docker-php-ext-install pdo pdo_mysql
COPY composer.json composer.json

RUN wget https://get.symfony.com/cli/installer -O - | bash && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

WORKDIR /app
COPY . /app

EXPOSE 8000
CMD composer install && symfony server:start