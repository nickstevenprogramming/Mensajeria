# Usa una imagen base oficial de PHP con Apache (fácil y ligera)
FROM php:8.2-apache

# Copia tus archivos PHP a la carpeta del servidor web
COPY . /var/www/html/

# Habilita el módulo rewrite si usas URLs amigables (opcional, quítalo si no lo necesitas)
RUN a2enmod rewrite

# Expone el puerto 80 (estándar para web)
EXPOSE 80

# Inicia Apache (el servidor web)
CMD ["apache2-foreground"]