FROM php:8.2-fpm

WORKDIR /var/www/html

ARG USERNAME
ARG USER_UID
ARG USER_GID

ENV DEPLOY_USER=$USERNAME

RUN groupadd -r -g ${GID} ${USERNAME} && \
    useradd -r -m -u ${UID} -g ${GID} --no-log-init ${USERNAME}

RUN apt-get update && apt-get install -y \
    libjpeg-dev \
    libfreetype6-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql

RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./init.sh /usr/local/bin/init.sh

RUN chmod +x /usr/local/bin/init.sh
