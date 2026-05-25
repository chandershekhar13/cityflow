
FROM php:8.3-cli

# Install system dependencies

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libsqlite3-dev \
    sqlite3 \
    nodejs \
    npm

# Install Composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory

WORKDIR /app

# Copy files

COPY . .

# Install PHP dependencies

RUN composer install --no-dev --optimize-autoloader

# Install frontend dependencies

RUN npm install

RUN npm run build

# Laravel permissions

RUN chmod -R 777 storage bootstrap/cache

# Generate app key if missing

RUN php artisan key:generate || true

# Expose port

EXPOSE 10000

# Start server

CMD php artisan serve --host=0.0.0.0 --port=10000
