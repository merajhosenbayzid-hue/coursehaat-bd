FROM php:8.2-apache
COPY . /var/www/html/
RUN mkdir -p /var/www/orders /var/www/html/data && chown -R www-data:www-data /var/www/orders /var/www/html/data \
 && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && chmod +x /var/www/html/start.sh
ENV ORDERS_DIR=/var/www/orders
EXPOSE 10000
CMD ["/var/www/html/start.sh"]
