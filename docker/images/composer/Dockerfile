FROM php:7.2-cli

RUN apt-get update && apt-get install -y \
    curl \
    vim \
    libcurl4-gnutls-dev \
    libicu-dev \
    zlib1g-dev \
    git \
    unzip

RUN docker-php-ext-install curl zip

# required for amqp
RUN apt-get update \
    && apt-get install -y \
        librabbitmq-dev \
        libssh-dev \
    && docker-php-ext-install \
        bcmath \
        sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp

ADD app.ini /usr/local/etc/php/conf.d/

RUN curl -o /usr/local/bin/composer http://getcomposer.org/composer.phar && chmod +x /usr/local/bin/composer

# change shell for www-data user
RUN chsh -s /bin/bash www-data

RUN usermod -u 1000 www-data
