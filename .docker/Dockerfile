FROM prestashop/prestashop-git:latest

# To run files with the same group as your primary user
RUN groupmod -g 1000 www-data \
  && usermod -u 1000 -g 1000 www-data

COPY /wait-for-it.sh /tmp/
COPY /docker_run_git.sh /tmp/

RUN mkdir -p /var/www/.npm
RUN chown -R www-data:www-data /var/www/.npm

RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -
RUN apt install -y nodejs

RUN pecl channel-update pecl.php.net \
  && yes | pecl install xdebug \
  && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.default_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_host=172.18.0.1" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.client_host=172.18.0.1" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_log=/var/www/html/xdebug_remote.log" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.log=/var/www/html/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini

CMD ["/tmp/docker_run_git.sh"]
