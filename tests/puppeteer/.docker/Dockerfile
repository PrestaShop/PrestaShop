# A minimal Docker image with Node and Puppeteer
#
# Based upon:
# https://github.com/GoogleChrome/puppeteer/blob/master/docs/troubleshooting.md#running-puppeteer-in-docker

FROM node:8-slim

RUN  apt-get update \
     # See https://crbug.com/795759
     && apt-get install -yq libgconf-2-4 \
     # Install latest chrome dev package, which installs the necessary libs to
     # make the bundled version of Chromium that Puppeteer installs work.
     && apt-get install -y wget --no-install-recommends \
     && wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add - \
     && sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list' \
     && apt-get update \
     && apt-get install -y google-chrome-unstable --no-install-recommends \
     && rm -rf /var/lib/apt/lists/*

# instantiate args
#ENV URL_BO='http://localhost:8080/admin-dev/' \
#    URL_FO='http://localhost:8080/' \
#    EMAIL='demo@prestashop.com' \
#    PASSWD='prestashop_demo' \
#    HEADLESS=true;
# Install Puppeteer under /node_modules so it's available system-wide
COPY ["./package.json", "./test/linkchecker.js","./"]
COPY ["./.docker/prestashop/wait-for-it.sh", "/tmp/"]
RUN npm install
RUN mkdir reports
CMD ["node", "linkchecker.js"]
