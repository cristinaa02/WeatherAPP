FROM php:8.2-apache

# Instalar extensiones de PHP para MySQL (PDO y mysqli)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar el módulo rewrite de Apache (por si usas .htaccess)
RUN a2enmod rewrite

# Copiamos el contenido de tu carpeta src al directorio de Apache en el contenedor
COPY ./src /var/www/html/

# Ajustar permisos para que Apache pueda leer los archivos
RUN chown -R www-data:www-data /var/www/html