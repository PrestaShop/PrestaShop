FROM prestashop/prestashop-git:7.1

ENV PS_HANDLE_DYNAMIC_DOMAIN 0
ENV PS_INSTALL_AUTO 1
ENV PS_DEV_MODE 1
ENV PS_COUNTRY fr
ENV PS_FOLDER_ADMIN admin-dev
ENV PS_FOLDER_INSTALL install-dev
ENV DB_SERVER localhost
ENV TRAVIS_BUILD_DIR /var/www/html

# MySQL installation
ENV DEBIAN_FRONTEND noninteractive
RUN echo mysql-server-5.6 mysql-server/root_password password $DB_PASSWD | debconf-set-selections
RUN echo mysql-server-5.6 mysql-server/root_password_again password $DB_PASSWD | debconf-set-selections
RUN curl -sS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | bash -s -- --mariadb-server-version=mariadb-10.1 \
	&& apt install -y mariadb-server
# MySQL configuration
RUN sed -i -e"s/^bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/" /etc/mysql/my.cnf

RUN service mysql start \
	&& mysqladmin -h $DB_SERVER -P $DB_PORT -u $DB_USER -p$DB_PASSWD create $DB_NAME --force

RUN sed -i -e"s/^exec\s*apache2-foreground/#exec apache2-foreground/" /tmp/docker_run.sh
RUN service mysql start && /tmp/docker_run_git.sh
RUN sed -i -e"s/^#exec\s*apache2-foreground/exec apache2-foreground/" /tmp/docker_run.sh

# .. PrestaShop Installed

# Install npm & node js & Java JRE
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash - \
	&& apt install -y software-properties-common python-software-properties \
	# nodejs
 	&& apt install -y nodejs \
         && npm i npm@latest -g \
	# java
	&& add-apt-repository "deb http://ppa.launchpad.net/webupd8team/java/ubuntu xenial main" \
	&& apt update \
	&& echo "oracle-java7-installer  shared/accepted-oracle-license-v1-1 boolean true" > /tmp/oracle-license-debconf \
  && /usr/bin/debconf-set-selections /tmp/oracle-license-debconf \
	&& rm /tmp/oracle-license-debconf \
	&& apt install -y oracle-java8-installer \
		oracle-java8-set-default \
	# chrome dependancies
	&& apt install -y postfix \
		libappindicator3-1 \
		libatk-bridge2.0-0 \
		libgtk-3-0 \
		libxss1 \
		fonts-liberation \
		xdg-utils \
		libnss3 \
		libasound2 \
		libnspr4 \
		poppler-utils \
	&& rm -rf /var/lib/apt/lists/*

# Repository update
RUN git pull && composer install

# Install selenium & other npm packages
COPY docker-files/npm-warmup.sh /tmp/npm-warmup.sh
RUN /tmp/npm-warmup.sh

# Install chrome
RUN wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
RUN dpkg -i google-chrome-stable_current_amd64.deb; apt-get -fy install

RUN adduser myuser --disabled-password --gecos ""

COPY docker-files/run-tests.sh /tmp/run-tests.sh
RUN chmod a+rx /tmp/run-tests.sh
CMD ["/tmp/run-tests.sh"]
