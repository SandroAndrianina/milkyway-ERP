FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

RUN apt-get update && apt-get install -y libicu-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo_mysql intl

COPY apache-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl

RUN a2enmod rewrite ssl

RUN sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

RUN { \
    echo 'upload_max_filesize = 20M'; \
    echo 'post_max_size = 20M'; \
} > /usr/local/etc/php/conf.d/uploads.ini

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

WORKDIR /var/www/html

EXPOSE 80