
# 1. Apache + PHP resmi imajı kullan
FROM php:8.2-apache

# 2. PHP modülleri (gerekliyse burada genişletebilirsin)
RUN docker-php-ext-install mysqli

# 3. Apache için rewrite modunu aktif et (React Router varsa şart)
RUN a2enmod rewrite

# 4. Tüm dosyaları Apache root dizinine kopyala
COPY . /var/www/html/
COPY .htaccess /var/www/html/.htaccess

# 5. React build'den gelen index.html'e ulaşabilmek için uygun izin ver
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 6. Apache varsayılan portu aç
EXPOSE 80
