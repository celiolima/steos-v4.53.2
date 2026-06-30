# Dockerfile
FROM php:8.4-apache

# Copiar seu php.ini
COPY ./dockerUtils/php.ini /usr/local/etc/php/php.ini
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# PHP extensions installer
#COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/


# 2. Instalar dependências obrigatórias e recomendadas para o CI4
# O CI4 exige obrigatoriamente as extensões: intl e mbstring (mbstring já vem por padrão)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_mysql mysqli gd zip \
    && rm -rf /var/lib/apt/lists/*

# 3. Habilitar o mod_rewrite do Apache (essencial para as rotas do CI4)
RUN a2enmod rewrite

# 4. Alterar o DocumentRoot do Apache para a pasta /public do CodeIgniter
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Definir o diretório de trabalho interno
WORKDIR /var/www/html

# 6. Copiar os arquivos do projeto
COPY . /var/www/html/

# 6.5 Instalar o Composer e as dependências do projeto (gera application/vendor)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 7. Dar permissão para o Apache na pasta do projeto e na pasta 'writable' (essencial para logs e cache do CI4)
RUN chown -R www-data:www-data /var/www/html 

# 8. Expor a porta padrão
EXPOSE 80

CMD ["apache2-foreground"]

