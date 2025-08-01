# PrestaShop Docker Documentation

This document provides detailed information about the Docker setup for PrestaShop development.

## Architecture Overview

The PrestaShop Docker setup uses a multi-service architecture with the following components:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   PHP Container │    │ Database        │    │   MailDev       │
│   (Apache +     │◄──►│   Container     │    │   Container     │
│    PHP 8.1+)    │    │   (MySQL 8)     │    │   (Email        │
│                 │    │                 │    │    Testing)     │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Container Details

### PHP Container (`php`)

**Base Image:** `php:8.1-apache`

**Features:**
- PHP 8.1+ with Apache web server
- Node.js 20 for asset compilation
- Composer for PHP dependency management
- Xdebug for debugging (disabled by default)
- Common PHP extensions (GD, Intl, PDO MySQL, etc.)

**Configuration Files:**
- `.docker/conf.d/10-app.ini` - Base PHP configuration
- `.docker/conf.d/20-app.dev.ini` - Development-specific settings
- `.docker/conf.d/vhost.conf` - Apache virtual host configuration

**Ports:**
- `80` (internal) → `8001` (host) - HTTP

### Database Container (`database`)

**Base Image:** `mysql:8`

**Features:**
- MySQL 8 with persistent storage
- Health checks for reliable startup
- Configurable credentials via environment variables

**Configuration:**
- Database: `prestashop` (default)
- User: `prestashop` (default)
- Password: `prestashop` (default)
- Port: `3306` (exposed to host)

**Volumes:**
- `database_data` - Persistent MySQL data storage

### MailDev Container (`maildev`)

**Base Image:** `maildev/maildev`

**Features:**
- Email testing interface
- SMTP server for development
- Web interface for viewing emails

**Ports:**
- `1080` - Web interface
- `1025` - SMTP server

## Configuration Files

### compose.yaml

Base Docker Compose configuration with minimal service definitions:

```yaml
services:
  php:
    image: ${IMAGES_PREFIX:-}app-php
    depends_on:
      - database
    restart: unless-stopped

  database:
    image: mysql:${MYSQL_VERSION:-8}
    environment:
      MYSQL_RANDOM_ROOT_PASSWORD: "true"
      MYSQL_USER: ${DB_USER:-prestashop}
      MYSQL_PASSWORD: ${DB_PASSWORD:-prestashop}
      MYSQL_DATABASE: ${DB_DATABASE:-prestashop}
    healthcheck:
      test: ["CMD", "mysqladmin" ,"ping", "-h", "localhost"]
      timeout: 5s
      retries: 5
      start_period: 60s
    volumes:
      - database_data:/var/lib/mysql:rw

  maildev:
    image: 'maildev/maildev'

volumes:
  database_data:
```

### compose.override.yaml

Development-specific overrides that extend the base configuration:

```yaml
services:
  php:
    build:
      context: .
      target: php_dev
      args:
        - PHP_VERSION=${PHP_VERSION:-8.1}
        - NODE_VERSION=${NODE_VERSION:-20}
    volumes:
      - ./:/app
      - /app/var
    ports:
      - target: 80
        published: ${HTTP_PORT:-8001}
        protocol: tcp
    environment:
      DATABASE_URL: mysql://${DB_USER:-prestashop}:${DB_PASSWORD:-prestashop}@${DB_SERVER:-database}:${DB_PORT:-3306}/${DB_DATABASE:-prestashop}?serverVersion=${DB_VERSION:-8}&charset=${DB_CHARSET:-utf8mb4}
      # ... additional environment variables
    extra_hosts:
      - host.docker.internal:host-gateway
    tty: true
```

### Dockerfile

Multi-stage Dockerfile for building the PHP container:

```dockerfile
#syntax=docker/dockerfile:1

ARG PHP_VERSION=8.4
ARG NODE_VERSION=20

FROM php:${PHP_VERSION}-apache AS php_dev

WORKDIR /app

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -

# Install system dependencies
RUN apt-get update && apt-get install --no-install-recommends -y \
    acl \
    file \
    gettext \
    git \
    nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

RUN install-php-extensions \
    @composer \
    apcu \
    gd \
    intl \
    opcache \
    pdo_mysql \
    xdebug \
    zip

# Copy configuration files
COPY --link .docker/conf.d/10-app.ini $PHP_INI_DIR/app.conf.d/
COPY --link .docker/conf.d/20-app.dev.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 .docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link .docker/conf.d/vhost.conf /etc/apache2/sites-available/000-default.conf

ENV APP_ENV=dev
ENV XDEBUG_MODE=off

ENTRYPOINT ["docker-entrypoint"]
HEALTHCHECK --start-period=60s CMD curl -f http://localhost || exit 1
CMD ["apache2-foreground"]
```

## Environment Variables

### Database Configuration

| Variable | Default | Description |
|----------|---------|-------------|
| `DB_USER` | `prestashop` | Database username |
| `DB_PASSWORD` | `prestashop` | Database password |
| `DB_DATABASE` | `prestashop` | Database name |
| `DB_SERVER` | `database` | Database host |
| `DB_PORT` | `3306` | Database port |
| `DB_VERSION` | `8` | MySQL version |
| `DB_CHARSET` | `utf8mb4` | Database charset |

### PrestaShop Configuration

| Variable | Default | Description |
|----------|---------|-------------|
| `PS_INSTALL_AUTO` | `true` | Auto-install PrestaShop |
| `PS_DOMAIN` | `localhost:8001` | Shop domain |
| `PS_ENABLE_SSL` | `0` | Enable SSL |
| `PS_FOLDER_INSTALL` | `install-dev` | Install folder |
| `PS_LANGUAGE` | `fr` | Installation language |
| `PS_COUNTRY` | `FR` | Shop country |
| `PS_ALL_LANGUAGES` | `0` | Install all languages |
| `PS_INSTALL_DEMO_PRODUCTS` | `0` | Install demo products |
| `PS_USE_DOCKER_MAILDEV` | `1` | Use MailDev for emails |

### Admin Configuration

| Variable | Default | Description |
|----------|---------|-------------|
| `ADMIN_FIRSTNAME` | `John` | Admin first name |
| `ADMIN_LASTNAME` | `Doe` | Admin last name |
| `ADMIN_MAIL` | `demo@prestashop.com` | Admin email |
| `ADMIN_PASSWORD` | `Correct Horse Battery Staple` | Admin password |

### Development Configuration

| Variable | Default | Description |
|----------|---------|-------------|
| `HTTP_PORT` | `8001` | HTTP port |
| `PHP_VERSION` | `8.1` | PHP version |
| `NODE_VERSION` | `20` | Node.js version |
| `XDEBUG_MODE` | `off` | Xdebug mode |

## Volume Management

### Persistent Volumes

```yaml
volumes:
  database_data:
    driver: local
```

### Bind Mounts

```yaml
volumes:
  - ./:/app                    # Application code
  - /app/var                   # Exclude var directory for performance
  # - /app/vendor              # Uncomment to exclude vendor (performance)
```

## Network Configuration

### Default Network

Docker Compose creates a default network for service communication:

```bash
# List networks
docker network ls

# Inspect network
docker network inspect prestashop_default
```

### Host Resolution

For development tools that need to access host services:

```yaml
extra_hosts:
  - host.docker.internal:host-gateway
```

## Health Checks

### Database Health Check

```yaml
healthcheck:
  test: ["CMD", "mysqladmin" ,"ping", "-h", "localhost"]
  timeout: 5s
  retries: 5
  start_period: 60s
```

### PHP Health Check

```dockerfile
HEALTHCHECK --start-period=60s CMD curl -f http://localhost || exit 1
```

## Advanced Configuration

### Custom PHP Extensions

To add custom PHP extensions, modify the Dockerfile:

```dockerfile
RUN install-php-extensions \
    @composer \
    apcu \
    gd \
    intl \
    opcache \
    pdo_mysql \
    xdebug \
    zip \
    your-custom-extension
```

### Custom Apache Configuration

Edit `.docker/conf.d/vhost.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot /app
    ServerName localhost
    
    <Directory /app>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Custom configuration here
</VirtualHost>
```

### SSL Configuration

For HTTPS development:

1. Generate SSL certificate:
```bash
mkdir -p .docker/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout .docker/ssl/localhost.key \
  -out .docker/ssl/localhost.crt
```

2. Update Apache configuration:
```apache
<VirtualHost *:443>
    SSLEngine on
    SSLCertificateFile /app/.docker/ssl/localhost.crt
    SSLCertificateKeyFile /app/.docker/ssl/localhost.key
    
    DocumentRoot /app
    ServerName localhost
</VirtualHost>
```

### Performance Optimization

#### Exclude Directories from Bind Mount

For better performance on macOS/Windows:

```yaml
volumes:
  - ./:/app
  - /app/vendor
  - /app/var/cache
  - /app/var/logs
  - /app/node_modules
```

#### Resource Limits

```yaml
services:
  php:
    deploy:
      resources:
        limits:
          memory: 2G
          cpus: '1.0'
        reservations:
          memory: 1G
          cpus: '0.5'
```

## Monitoring and Logging

### Container Logs

```bash
# View all logs
docker compose logs

# Follow logs
docker compose logs -f

# View specific service logs
docker compose logs php
docker compose logs database
```

### Resource Usage

```bash
# Container stats
docker stats

# Disk usage
docker system df

# Volume usage
docker volume ls
```

## Backup and Restore

### Database Backup

```bash
# Create backup
docker compose exec database mysqldump -u prestashop -p prestashop > backup.sql

# Restore backup
docker compose exec -T database mysql -u prestashop -p prestashop < backup.sql
```

### Volume Backup

```bash
# Backup volume
docker run --rm -v prestashop_database_data:/data -v $(pwd):/backup alpine tar czf /backup/database_backup.tar.gz -C /data .

# Restore volume
docker run --rm -v prestashop_database_data:/data -v $(pwd):/backup alpine tar xzf /backup/database_backup.tar.gz -C /data
```

## Troubleshooting

### Common Issues

1. **Container won't start**: Check logs with `docker compose logs`
2. **Permission issues**: Fix with `sudo chown -R $USER:$USER .`
3. **Port conflicts**: Change port with `export HTTP_PORT=8002`
4. **Memory issues**: Increase Docker Desktop memory allocation

### Debug Mode

Enable debug mode for more verbose output:

```bash
export COMPOSE_BAKE=true
docker compose build --no-cache
docker compose up --verbose
```

## Best Practices

1. **Use environment variables** for configuration
2. **Exclude vendor directory** from bind mounts for better performance
3. **Regular backups** of database and volumes
4. **Monitor resource usage** to prevent issues
5. **Keep images updated** with security patches
6. **Use health checks** for reliable service startup
7. **Document custom configurations** for team consistency 