FROM php:8.2-apache

# Copia tu código a la raíz del servidor web
COPY ./code /var/www/html

# Puerto usado por Cloud Run
EXPOSE 8080

# Cambia el puerto en Apache para que escuche en 8080
RUN sed -i 's/80/8080/' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
