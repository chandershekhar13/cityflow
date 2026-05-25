

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

# Build frontend assets

RUN npm run build

# Laravel setup

RUN chmod -R 777 storage bootstrap/cache

# Create SQLite database if missing

RUN mkdir -p database

RUN touch database/database.sqlite

# Generate app key safely

RUN php artisan key:generate || true

# Expose Render port

EXPOSE 10000

# Start Laravel server

CMD php artisan serve --host=0.0.0.0 --port=10000