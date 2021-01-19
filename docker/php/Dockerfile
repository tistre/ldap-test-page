FROM php:7-fpm

# Install "ldap" extension
RUN apt-get update && \
    apt-get install -y ldap-utils libldap2-dev \
    && docker-php-ext-install ldap
