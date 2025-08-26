FROM php:8.2-fpm-alpine3.22

WORKDIR /var/www/html

ARG USERNAME
ARG USER_UID
ARG USER_GID

ENV DEPLOY_USER=$USERNAME

RUN addgroup -g ${USER_GID} ${USERNAME} && \
    adduser --disabled-password --uid ${USER_UID} --ingroup ${USERNAME} ${USERNAME}

RUN apk update && apk add --no-cache \
    libjpeg-turbo-dev \
    freetype-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    zip \
    unzip \
    curl \
    git

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql

RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./init.sh /usr/local/bin/init.sh

RUN chmod +x /usr/local/bin/init.sh
