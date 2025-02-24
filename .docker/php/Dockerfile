FROM php:8.2-fpm-bullseye

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions \
    pdo_mysql \
    bcmath \
    zip \
    mbstring \
    opcache \
    gd \
    decimal

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt install -y nodejs
RUN npm install --global yarn

COPY .docker/php/php.ini /usr/local/etc/php/php.ini
COPY . /app

WORKDIR /app

RUN chmod -R a+w /app/storage /app/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]