FROM php:8.3-fpm

WORKDIR /var/www/html

# Installation des dépendances
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    && docker-php-ext-install zip pdo_mysql

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copie des fichiers de l'application
COPY src/ .
COPY public/ .
COPY vendor/ .

# TODO : Ajouter composer à l'image et télécharger les dépendances au build


# Installation des dépendances de l'application
# RUN composer install --no-dev --optimize-autoloader
