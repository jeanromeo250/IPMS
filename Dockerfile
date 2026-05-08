# ============================================================
# Dockerfile — IPMS PHP Application
# Base image: PHP 8.2 with Apache web server
# ============================================================

FROM php:8.2-apache

# ── Install PHP extensions needed for MySQL ─────────────────
RUN docker-php-ext-install pdo pdo_mysql mysqli

# ── Enable Apache mod_rewrite (needed for .htaccess) ────────
RUN a2enmod rewrite

# ── Set Apache to allow .htaccess overrides ─────────────────
RUN sed -i 's/AllowOverride None/AllowOverride All/g' \
    /etc/apache2/apache2.conf

# ── Copy all project files into the Apache web root ─────────
COPY . /var/www/html/

# ── Set correct file permissions ────────────────────────────
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# ── Expose port 80 (Apache default) ─────────────────────────
EXPOSE 80
