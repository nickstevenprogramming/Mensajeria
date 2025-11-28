# Usa la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instala la extensión mysqli y pdo_mysql (para que funcione new mysqli())
RUN docker-php-ext-install mysqli pdo_mysql

# Copia todos tus archivos al servidor web
COPY . /var/www/html/

# Habilita mod_rewrite (por si usas URLs amigables)
RUN a2enmod rewrite

# Corrige permisos (importante)
RUN chown -R www-data:www-data /var/www/html/ && chmod -R 755 /var/www/html/

# Expone el puerto 80
EXPOSE 80

# Inicia Apache
CMD ["apache2-foreground"]
