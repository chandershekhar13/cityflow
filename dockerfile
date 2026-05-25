FROM php:8.4-cli

# Install system dependencies

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    libzip-dev \
    libsqlite3-dev \
    sqlite3 \
    nodejs \
    npm

# Install PHP extensions

RUN docker-php-ext-install \
    pdo \
    pdo_sqlite \
    zip

# Install Composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory

WORKDIR /app

# Copy application files

COPY . .

# Install Composer dependencies

RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies

RUN npm install

# Laravel permissions

RUN chmod -R 777 storage

RUN chmod -R 777 bootstrap/cache

# Create SQLite database

RUN mkdir -p database

RUN touch database/database.sqlite

RUN chmod -R 777 database

# Run migrations


RUN php artisan migrate:fresh --seed --force


# Expose Render port

EXPOSE 10000

# Start Laravel server

CMD php -S 0.0.0.0:10000 -t public
