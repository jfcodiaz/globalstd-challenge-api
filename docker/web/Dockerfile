# ============================
# STAGE: BASE
# ============================
FROM ubuntu:22.04 AS base

ENV DEBIAN_FRONTEND=noninteractive

# Install base dependencies
RUN apt-get update && apt-get install -y \
  software-properties-common \
  curl \
  gnupg \
  ca-certificates \
  lsb-release \
  unzip \
  git \
  sudo \
  wget \
  vim \
  libzip-dev \
  libonig-dev \
  libxml2-dev \
  libcurl4-openssl-dev \
  nginx \
  supervisor \
  python3 \
  python3-pip \
  postgresql postgresql-contrib \
  libpq-dev

# Add PHP 8.2 repository
RUN add-apt-repository ppa:ondrej/php -y && apt-get update

# Install PHP 8.2 + extensions for Laravel
RUN apt-get install -y \
  php8.2 \
  php8.2-cli \
  php8.2-common \
  php8.2-fpm \
  php8.2-bcmath \
  php8.2-curl \
  php8.2-gd \
  php8.2-mbstring \
  php8.2-pgsql \
  php8.2-pdo \
  php8.2-xml \
  php8.2-zip \
  php8.2-tokenizer \
  php8.2-readline \
  php8.2-opcache \
  php8.2-intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js (LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - && \
    apt-get install -y nodejs

# Fix PHP-FPM to run in foreground
RUN sed -i 's/^;daemonize = yes/daemonize = no/' /etc/php/8.2/fpm/php-fpm.conf

# Ensure PHP-FPM socket folder exists
RUN mkdir -p /run/php && chown -R www-data:www-data /run/php

# Re-initialize PostgreSQL cluster using env vars
RUN rm -rf /var/lib/postgresql/14/main && \
    chown -R postgres:postgres /var/lib/postgresql && \
    su - postgres -c '/usr/lib/postgresql/14/bin/initdb -D /var/lib/postgresql/14/main' && \
    echo "listen_addresses = '*'" >> /var/lib/postgresql/14/main/postgresql.conf && \
    echo "host all all 0.0.0.0/0 md5" >> /var/lib/postgresql/14/main/pg_hba.conf
COPY ./docker/web/php-fpm/www-dev.conf /etc/php/8.2/fpm/pool.d/www.conf
COPY ./docker/web/nginx/default-prd.conf /etc/nginx/sites-available/default

# ============================
# STAGE: DEV
# ============================
FROM base AS dev

RUN apt-get update && apt-get install -y php8.2-xdebug

RUN echo "zend_extension=xdebug.so" >> /etc/php/8.2/fpm/php.ini && \
    echo "xdebug.mode=debug" >> /etc/php/8.2/fpm/php.ini && \
    echo "xdebug.start_with_request=yes" >> /etc/php/8.2/fpm/php.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /etc/php/8.2/fpm/php.ini

WORKDIR /var/www/html

EXPOSE 8080

#CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
CMD ["tail", "-f", "/dev/null"]

# ============================
# STAGE: TEST
# ============================
FROM base AS test

RUN apt-get update && apt-get install -y php8.2-xdebug

RUN echo "zend_extension=xdebug.so" >> /etc/php/8.2/fpm/php.ini && \
    echo "xdebug.mode=coverage" >> /etc/php/8.2/fpm/php.ini && \
    echo "xdebug.start_with_request=yes" >> /etc/php/8.2/fpm/php.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /etc/php/8.2/fpm/php.ini

WORKDIR /var/www/html

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

# ============================
# STAGE: PROD
# ============================
FROM base AS prod

ARG DB_DATABASE
ARG DB_USERNAME
ARG DB_PASSWORD
ARG APP_KEY
ARG APP_DEFAULT_PASSWORD

ENV DB_DATABASE=${DB_DATABASE}
ENV DB_USERNAME=${DB_USERNAME}
ENV DB_PASSWORD=${DB_PASSWORD}
ENV APP_KEY=${APP_KEY}
ENV APP_ENV=production
ENV DB_CONNECTION=pgsql
ENV APP_DEFAULT_PASSWORD=${APP_DEFAULT_PASSWORD}

COPY ./docker/web/supervisord/supervisord-prd.conf /etc/supervisor/conf.d/supervisord.conf

COPY ./code /var/www/html
WORKDIR /var/www/html

RUN composer install --working-dir=/var/www/html

# RUN composer install --no-dev --no-autoloader --working-dir=/var/www/html
RUN mkdir -p storage/framework/views storage/logs && \
  chown -R www-data:www-data storage && \
  chmod -R 775 storage && \
  php artisan config:cache && \
  php artisan route:cache && \
  php artisan view:cache && \
  php artisan storage:link && \
  php artisan optimize

# Inicializar PostgreSQL, crear usuario y base de datos, migrar y seedear
RUN service postgresql start && \
    su - postgres -c "psql -c \"CREATE USER $DB_USERNAME WITH PASSWORD '$DB_PASSWORD';\"" && \
    su - postgres -c "psql -c \"CREATE DATABASE $DB_DATABASE OWNER $DB_USERNAME;\"" && \
    php artisan migrate --force --seed && \
    service postgresql stop

RUN chmod -R 777 storage

EXPOSE 8080
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
