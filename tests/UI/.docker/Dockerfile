# A minimal Docker image with Node and playwright
#
# Based upon:
# https://github.com/microsoft/playwright/blob/master/docs/docker/Dockerfile.bionic

FROM ubuntu:bionic

# 1. Install node12
RUN apt-get update && apt-get install -y curl && \
    curl -sL https://deb.nodesource.com/setup_12.x | bash - && \
    apt-get install -y nodejs

# 2. Install WebKit dependencies
RUN apt-get install -y libwoff1 \
                       libopus0 \
                       libwebp6 \
                       libwebpdemux2 \
                       libenchant1c2a \
                       libgudev-1.0-0 \
                       libsecret-1-0 \
                       libhyphen0 \
                       libgdk-pixbuf2.0-0 \
                       libegl1 \
                       libnotify4 \
                       libxslt1.1 \
                       libevent-2.1-6 \
                       libgles2 \
                       libvpx5 \
                       libglu1

# 3. Install Chromium dependencies
RUN apt-get install -y libnss3 \
                       libxss1 \
                       libasound2

# 4. Install Firefox dependencies
RUN apt-get install -y libdbus-glib-1-2 \
                       libxt6


# 5. Copy scripts wait-for-it and run-tests
COPY ["./.docker/prestashop/wait-for-it.sh", "./.docker/run-tests.sh", "/tmp/"]

# 6. Create folder for tests and copy component
RUN mkdir -p /tests/reports
WORKDIR /tests/
COPY ["./", "./"]

# 7. Install dependencies
RUN npm install
