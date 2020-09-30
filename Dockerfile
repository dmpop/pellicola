FROM php:5.6-apache
RUN apt-get update && apt-get install -y libpng-dev libjpeg62-turbo-dev libfreetype6-dev && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && docker-php-ext-install gd exif
VOLUME ["/var/www/html/config"]
VOLUME ["/var/www/html/photos"]
COPY favicon.png /var/www/html
COPY favicon.svg /var/www/html
COPY index.php /var/www/html
