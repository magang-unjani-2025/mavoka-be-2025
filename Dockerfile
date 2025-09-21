FROM php:8.3-fpm

WORKDIR /var/www/html

ARG USERNAME
ARG USER_UID
ARG USER_GID

ENV DEPLOY_USER=$USERNAME

RUN groupadd -r -g ${USER_GID} ${USERNAME} && \
    useradd -r -m -u ${USER_UID} -g ${USER_GID} --no-log-init ${USERNAME}

RUN apt-get update && apt-get install -y \
    libjpeg-dev \
    libfreetype6-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    fd-find \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd pdo_pgsql pgsql mbstring exif pcntl bcmath zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./init.sh /usr/local/bin/init.sh

RUN chmod +x /usr/local/bin/init.sh
