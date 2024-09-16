ARG PHP_VERSION=7.4.33
FROM chinayin/php:${PHP_VERSION}-fpm-slim

ARG CI_BUILD_REVISION
ENV CI_BUILD_REVISION=${CI_BUILD_REVISION}

WORKDIR /var/www

COPY . .

RUN set -eu \
  && install_packages libldap2-dev \
  && docker-php-ext-install ldap \
  && php --ri ldap \
  && rm -rf html \
  && ln -s /var/www/public /var/www/html \
  && composer install --no-dev --no-interaction --no-progress --optimize-autoloader \
  && ls -al .
