# Use the official WordPress image from the Docker Hub
FROM wordpress:latest

# Add any custom configuration or plugins here
# COPY my-custom-plugin /var/www/html/wp-content/plugins/

# Set the appropriate permissions for WordPress
RUN mkdir -p /var/www/html/wp-content/uploads && \
    chown -R www-data:www-data /var/www/html/wp-content/uploads
