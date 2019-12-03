# A minimal Docker image with Node and Puppeteer
#
# Based upon:
# https://github.com/GoogleChrome/puppeteer/blob/master/docs/troubleshooting.md#running-puppeteer-in-docker

FROM node:10-slim

# Install latest chrome dev package and fonts
RUN  wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add - \
     && sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list' \
     && apt-get update \
     && apt-get install -y google-chrome-stable fonts-ipafont-gothic fonts-wqy-zenhei fonts-thai-tlwg fonts-kacst \
     ttf-freefont --no-install-recommends \
     && rm -rf /var/lib/apt/lists/*

# Copy scripts wait-for-it and run-tests
COPY ["./.docker/prestashop/wait-for-it.sh", "./.docker/run-tests.sh", "/tmp/"]

# Create folder for tests and another for downloaded files
RUN mkdir -p tests/reports \
    && mkdir -p /downloads

WORKDIR /tests/
COPY ["./", "./"]

# Install dependencies
RUN npm install
