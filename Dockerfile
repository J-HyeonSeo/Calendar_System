FROM php:8.1-apache

# Composer 설치하기
COPY --from=composer:2.8.6 /usr/bin/composer /usr/bin/composer

# 필수 패키지 설치
RUN apt-get update && apt-get install -y \
    libicu-dev \
    zip unzip git \
    && docker-php-ext-install intl mysqli \
    && docker-php-ext-enable intl mysqli

# Apache 설정하기
RUN a2enmod rewrite

# Apache Document Root를 지정하는 000-default.conf 파일 복사
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# 프로젝트 데이터를 Apache 폴더로 복사
WORKDIR /var/www/html
COPY . .

# Composer를 통한 CodeIgniter4 설치
RUN composer install --no-dev --optimize-autoloader

# 리눅스 권한 설정
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/writable

# Apache 실행하기
ENTRYPOINT ["apache2-foreground"]