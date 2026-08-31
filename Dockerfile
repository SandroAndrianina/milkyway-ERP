FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Installer les dépendances système pour intl
RUN apt-get update && apt-get install -y libicu-dev

# Installer les extensions PHP
RUN docker-php-ext-install mysqli pdo_mysql intl

# Active mod_rewrite (déjà présent, une seule fois)
RUN a2enmod rewrite

# emplace la directive AllowOverride None par AllowOverride All dans la config Apache, ce qui autorise ton .htaccess à prendre le contrôle du routage
RUN sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

RUN { \
    echo 'upload_max_filesize = 20M'; \
    echo 'post_max_size = 20M'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Copie du code source DANS l'image (en prod) - MAIS comme on va coder en direct avec le bind mount, cette ligne sera commentée en développement et décommentée en prod.
# COPY . /var/www/html/

# Change le propriétaire pour qu'Apache puisse écrire dans writable/
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Définit le répertoire de travail
WORKDIR /var/www/html

# Le port exposé par Apache
EXPOSE 80