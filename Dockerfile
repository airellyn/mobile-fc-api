FROM php:7.4-fpm

RUN apt update && apt install -y libzip-dev
RUN docker-php-ext-install pdo pdo_mysql sockets zip
RUN apt install jq -y

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
RUN composer update

COPY .env.local .env

# for development only
# RUN touch database/database.sqlite

RUN php artisan key:generate

# Change ownership of our applications
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R ug+rwx /var/www/html/storage/logs
RUN chmod -R ug+rwx /var/www/html/storage/app/public
RUN chmod -R ug+rwx /var/www/html/storage/framework/views
RUN chmod -R ug+rwx /var/www/html/storage/framework/cache
RUN chmod -R ug+rwx /var/www/html/bootstrap/cache

# for development only
# RUN php artisan migrate
RUN php artisan --version

RUN ls -l
CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]