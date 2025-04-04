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

COPY ./docker/web/supervisord/supervisord-prd.conf /etc/supervisor/conf.d/supervisord.conf

COPY ./code /var/www/html

COPY ./docker/web/init.sh /init.sh
RUN composer install --working-dir=/var/www/html

WORKDIR /var/www/html

EXPOSE 8080
CMD [ "bash", "/init.sh" ]
# CMD ["tail", "-f", "/var/log/supervisor/supervisord.log"]
#CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
