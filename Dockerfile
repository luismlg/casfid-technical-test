# Dockerfile para CASFID - API REST de Libros
# PHP 8.2 con Apache, PDO MySQL, PDO SQLite y Composer

FROM php:8.2-apache

# Metadata
LABEL maintainer="CASFID Team"
LABEL description="CASFID Books REST API"
LABEL version="1.0"

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    zip

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite headers

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar DocumentRoot a /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configurar directiva AllowOverride para .htaccess
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/casfid.conf && \
    a2enconf casfid

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de composer primero (para aprovechar cache de Docker)
COPY composer.json composer.lock ./

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copiar el resto de la aplicación
COPY . .

# Ejecutar scripts post-install de composer
RUN composer dump-autoload --optimize

# Crear directorios necesarios con permisos
RUN mkdir -p cache logs database \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 cache logs database

# Copiar archivo .env si existe (o usar .env.example como base)
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Exponer puerto 80
EXPOSE 80

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Comando por defecto
CMD ["apache2-foreground"]
