FROM php:7.1-cli

MAINTAINER Daniel Macedo <admacedo@gmail.com>

RUN apt-get update \
    && deps="libicu-dev zlib1g-dev" \
    && apt-get install -y --no-install-recommends curl git make $deps libicu52 zlib1g \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install intl mbstring zip \
    && apt-get purge -y --auto-remove $deps

COPY php/php.ini /usr/local/etc/php/

WORKDIR /usr/src/sitemapper

CMD [ "echo", "Success!" ]
