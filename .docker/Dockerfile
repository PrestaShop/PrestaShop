# To run files with the same group as your primary user
ARG VERSION

FROM prestashop/base:$VERSION

EXPOSE 443

ARG GROUP_ID
ARG USER_ID
ARG NODE_VERSION

RUN groupmod -g $GROUP_ID www-data \
  && usermod -u $USER_ID -g $GROUP_ID www-data

COPY .docker/wait-for-it.sh /tmp/
COPY .docker/docker_run_git.sh /tmp/

RUN mkdir -p /var/www/.npm
RUN chown -R www-data:www-data /var/www/.npm
RUN mkdir -p /var/www/.composer
RUN chown -R www-data:www-data /var/www/.composer

ENV NVM_DIR       /usr/local/nvm
RUN mkdir -p $NVM_DIR \
    && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash \
    && . $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

ENV NODE_PATH     $NVM_DIR/versions/node/v$NODE_VERSION/bin
ENV PATH          $PATH:$NODE_PATH

# Install mailutils to make sendmail work
RUN apt update && apt install -y mailutils

# Configure OpenSSL
## Enable legacy providers in OpenSSL
RUN sed -i "s/\# \[provider_sect\]/\[provider_sect\]/g" /etc/ssl/openssl.cnf \
    && sed -i "s/\# default = default_sect/default = default_sect\nlegacy = legacy_sect/g" /etc/ssl/openssl.cnf \
    && sed -i "s/\# \[default_sect\]/\[legacy_sect\]\nactivate = 1\n\[default_sect\]\nactivate = 1/g" /etc/ssl/openssl.cnf \
	  && cat /etc/ssl/openssl.cnf

RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer && rm -rf /tmp/composer-setup.php
CMD ["/tmp/docker_run_git.sh"]
