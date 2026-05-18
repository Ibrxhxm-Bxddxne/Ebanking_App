FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql


RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf


COPY src/ /var/www/html/

# Donner les droits au dossier (OpenShift utilise un utilisateur aléatoire)
RUN chmod -R 777 /var/www/html/

EXPOSE 8080
