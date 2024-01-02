# To run files with the same group as your primary user
ARG VERSION

FROM prestashop/prestashop-git:$VERSION

ARG GROUP_ID
ARG USER_ID
ARG NODE_VERSION

# To run files with the same group as your primary user
RUN groupmod -g $GROUP_ID www-data \
  && usermod -u $USER_ID -g $GROUP_ID www-data

COPY .docker/wait-for-it.sh /tmp/
COPY .docker/docker_run_git.sh /tmp/

RUN mkdir -p /var/www/.npm
RUN chown -R www-data:www-data /var/www/.npm

# These two directories are docker mounted volumes
RUN chown -R www-data:www-data /var/www/html/vendor
RUN chown -R www-data:www-data /var/www/html/var

# Install NVM And Node
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

CMD ["/tmp/docker_run_git.sh"]
