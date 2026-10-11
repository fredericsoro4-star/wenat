FROM php:8.2-apache

# Extensions PHP pour MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Activer les URL propres (.htaccess)
RUN a2enmod rewrite

# Autoriser .htaccess dans /var/www/html
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copier le projet
COPY . /var/www/html/

# Droits corrects
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
