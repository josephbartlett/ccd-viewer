# Dockerfile for CCD Viewer application
# Uses the official PHP Apache base image and configures the document root
# to the public directory where the entry point resides.

FROM php:8.2-apache

LABEL maintainer="ccd-viewer"

## Install DOM extension dependencies and compile extension.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libxml2-dev \
    && docker-php-ext-install -j$(nproc) dom \
    && rm -rf /var/lib/apt/lists/*

## Configure Apache to serve from the /var/www/html/public directory.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

## Copy application code into the image
COPY . /var/www/html

## Set permissions (optional; ensures www-data can read/write uploads)
RUN chown -R www-data:www-data /var/www/html

## Expose port 80
EXPOSE 80

## Start Apache in the foreground
CMD ["apache2-foreground"]
