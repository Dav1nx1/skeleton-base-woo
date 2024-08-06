#!/bin/bash

# Pull the latest images
docker compose pull

# Build the Docker containers
docker compose build

# Start the containers
docker compose up -d

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until docker exec mysql mysqladmin ping --silent; do
  sleep 1
done

# Wait for WordPress to be ready
echo "Waiting for WordPress to be ready..."
sleep 30  # Give some time for WordPress to start up

# Install wp-cli in the WordPress container
echo "Installing wp-cli..."
docker exec wordpress bash -c "curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp"

# Wait for WordPress to be fully up
echo "Waiting for WordPress to be fully up..."
sleep 30  # Give some more time

# Install WordPress
echo "Installing WordPress..."
docker exec wordpress bash -c "wp core install \
  --url='http://localhost:8000' \
  --title='My WordPress Site' \
  --admin_user='admin' \
  --admin_password='admin_password' \
  --admin_email='admin@example.com' \
  --allow-root"

# Set the site to use the pretty permalinks
docker exec wordpress bash -c "wp rewrite structure '/%postname%/' --allow-root"

# Install and activate WooCommerce
echo "Installing and activating WooCommerce..."
docker exec wordpress bash -c "wp plugin install woocommerce --activate --allow-root"

echo "Activating the theme..."
docker exec wordpress bash -c "wp theme activate betheme --allow-root"

echo "WordPress setup is complete. Access it at http://localhost:8000"
