FROM php:8.1-apache

# Composer 설치하기
COPY --from=composer:2.8.6 /usr/bin/composer /usr/bin/composer

# 필요 패키지 설치 (MySQL 드라이버 & intl 확장)
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libzip-dev unzip curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring zip pdo pdo_mysql intl

# Apache 설정하기
RUN a2enmod rewrite

# 프로젝트 데이터를 Apache 폴더로 복사
WORKDIR /var/www/html
COPY . .

# Composer를 통한 CodeIgniter4 설치
RUN composer install --no-dev --optimize-autoloader

# 리눅스 권한 설정
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# Apache 실행하기
CMD ["apache2-foreground"]