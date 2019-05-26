FROM php:7.2-cli

# graphviz is for dumping the workflows
RUN apt-get update && apt-get install -y \
    curl \
    vim \
    libcurl4-gnutls-dev \
    libicu-dev

RUN docker-php-ext-install curl

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

# change shell for www-data user
RUN chsh -s /bin/bash www-data

RUN usermod -u 1000 www-data
