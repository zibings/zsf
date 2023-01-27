FROM php:8.1-apache
RUN apt-get update && apt-get upgrade
RUN apt-get install -y libbz2-dev sqlite3 libsqlite3-dev libssl-dev libcurl4-openssl-dev libjpeg-dev libonig-dev libreadline-dev libtidy-dev libxslt-dev libzip-dev 'libpng*'
RUN docker-php-ext-install pdo pdo_mysql mysqli bz2 mbstring gd xsl

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug @composer

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite