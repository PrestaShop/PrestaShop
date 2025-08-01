# PrestaShop Development Troubleshooting Guide

This guide helps you resolve common issues when working with the PrestaShop development environment.

## Table of Contents

- [Docker Issues](#docker-issues)
- [Database Issues](#database-issues)
- [Asset Build Issues](#asset-build-issues)
- [Performance Issues](#performance-issues)
- [Permission Issues](#permission-issues)
- [Network Issues](#network-issues)
- [Development Tools Issues](#development-tools-issues)

## Docker Issues

### Container Won't Start

**Symptoms:** Containers fail to start or exit immediately

**Solutions:**

1. **Check Docker is running:**
   ```bash
   docker info
   ```

2. **Check available resources:**
   ```bash
   # Ensure Docker has enough memory (at least 4GB recommended)
   docker system df
   ```

3. **Clean Docker cache:**
   ```bash
   docker system prune -f
   docker volume prune -f
   ```

4. **Rebuild from scratch:**
   ```bash
   make down
   docker system prune -af
   make build --no-cache
   make up
   ```

### Port Already in Use

**Symptoms:** Error about port 8001 (or other ports) being in use

**Solutions:**

1. **Check what's using the port:**
   ```bash
   # On macOS/Linux
   lsof -i :8001
   
   # On Windows
   netstat -ano | findstr :8001
   ```

2. **Change the port:**
   ```bash
   export HTTP_PORT=8002
   make up
   ```

3. **Stop conflicting services:**
   ```bash
   # If you have another PrestaShop instance running
   docker ps
   docker stop <container-id>
   ```

### Image Build Fails

**Symptoms:** `make build` fails with errors

**Solutions:**

1. **Check Dockerfile syntax:**
   ```bash
   docker build --no-cache .
   ```

2. **Increase Docker resources:**
   - Open Docker Desktop settings
   - Increase memory to at least 4GB
   - Increase disk space

3. **Check network connectivity:**
   ```bash
   # Test if you can reach external resources
   curl -I https://deb.nodesource.com
   ```

## Database Issues

### Database Connection Failed

**Symptoms:** PrestaShop can't connect to the database

**Solutions:**

1. **Check database container:**
   ```bash
   docker ps | grep database
   docker logs prestashop-database-1
   ```

2. **Reset database:**
   ```bash
   make down
   docker volume rm prestashop_database_data
   make up
   ```

3. **Check database credentials:**
   ```bash
   # Verify environment variables
   docker compose exec php env | grep DB_
   ```

### Database Data Lost

**Symptoms:** Database is empty or reset unexpectedly

**Solutions:**

1. **Check volume mounting:**
   ```bash
   docker volume ls | grep database
   ```

2. **Restore from backup (if available):**
   ```bash
   # If you have a backup
   docker compose exec database mysql -u prestashop -p prestashop < backup.sql
   ```

3. **Reinstall PrestaShop:**
   ```bash
   # Remove parameters.php to trigger reinstall
   rm app/config/parameters.php
   make up
   ```

## Asset Build Issues

### Node.js Dependencies Missing

**Symptoms:** Asset build fails with npm errors

**Solutions:**

1. **Install Node.js dependencies:**
   ```bash
   make sh
   cd admin-dev/themes/default && npm install
   cd admin-dev/themes/new-theme && npm install
   cd themes/classic/_dev && npm install
   cd themes/hummingbird && npm install
   ```

2. **Clear npm cache:**
   ```bash
   make sh
   npm cache clean --force
   ```

3. **Check Node.js version:**
   ```bash
   make sh
   node --version  # Should be 20.x
   ```

### Asset Build Fails

**Symptoms:** `make assets` fails with compilation errors

**Solutions:**

1. **Check for syntax errors:**
   ```bash
   make assets-dev  # This will show more detailed errors
   ```

2. **Clear build cache:**
   ```bash
   make sh
   # Clear various caches
   rm -rf admin-dev/themes/default/node_modules/.cache
   rm -rf admin-dev/themes/new-theme/node_modules/.cache
   rm -rf themes/classic/_dev/node_modules/.cache
   rm -rf themes/hummingbird/node_modules/.cache
   ```

3. **Rebuild from scratch:**
   ```bash
   make sh
   # Remove node_modules and reinstall
   find . -name "node_modules" -type d -exec rm -rf {} +
   make assets
   ```

## Performance Issues

### Slow File Operations

**Symptoms:** File operations are very slow, especially on macOS/Windows

**Solutions:**

1. **Exclude vendor directory from bind mount:**
   ```bash
   # Edit compose.override.yaml and uncomment:
   # - /app/vendor
   ```

2. **Use Docker Desktop file sharing settings:**
   - Add your project directory to Docker Desktop's file sharing
   - Enable "Use the new Virtualization framework" on macOS

3. **Use volume mounts for specific directories:**
   ```yaml
   volumes:
     - ./:/app
     - /app/vendor
     - /app/var/cache
     - /app/var/logs
   ```

### High Memory Usage

**Symptoms:** Docker containers using too much memory

**Solutions:**

1. **Limit container resources:**
   ```yaml
   # Add to compose.override.yaml
   services:
     php:
       deploy:
         resources:
           limits:
             memory: 2G
   ```

2. **Optimize PHP settings:**
   ```bash
   # Edit .docker/conf.d/10-app.ini
   memory_limit = 512M
   max_execution_time = 300
   ```

## Permission Issues

### File Permission Errors

**Symptoms:** Cannot write to files or directories

**Solutions:**

1. **Fix ownership:**
   ```bash
   sudo chown -R $USER:$USER .
   ```

2. **Fix permissions:**
   ```bash
   find . -type d -exec chmod 755 {} \;
   find . -type f -exec chmod 644 {} \;
   chmod +x .docker/docker-entrypoint.sh
   ```

3. **Use Docker user:**
   ```bash
   # Edit compose.override.yaml
   services:
     php:
       user: "${UID:-1000}:${GID:-1000}"
   ```

### Cache Directory Issues

**Symptoms:** Symfony cache errors

**Solutions:**

1. **Clear cache:**
   ```bash
   make cc
   ```

2. **Fix cache permissions:**
   ```bash
   make sh
   chmod -R 777 var/cache
   chmod -R 777 var/logs
   ```

## Network Issues

### Host Resolution Problems

**Symptoms:** Can't access localhost or host.docker.internal

**Solutions:**

1. **Check extra_hosts configuration:**
   ```yaml
   # Should be in compose.override.yaml
   extra_hosts:
     - host.docker.internal:host-gateway
   ```

2. **Use Docker network:**
   ```bash
   docker network create prestashop-network
   ```

3. **Check firewall settings:**
   ```bash
   # On macOS
   sudo pfctl -s all
   ```

### SSL/HTTPS Issues

**Symptoms:** SSL certificate errors or HTTPS not working

**Solutions:**

1. **Disable SSL for development:**
   ```bash
   export PS_ENABLE_SSL=0
   make up
   ```

2. **Generate self-signed certificate:**
   ```bash
   # Create SSL certificate for local development
   openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
     -keyout .docker/ssl/localhost.key \
     -out .docker/ssl/localhost.crt
   ```

## Development Tools Issues

### Xdebug Not Working

**Symptoms:** Can't debug PHP code

**Solutions:**

1. **Enable Xdebug:**
   ```bash
   export XDEBUG_MODE=debug
   make up
   ```

2. **Check Xdebug configuration:**
   ```bash
   make sh
   php -i | grep xdebug
   ```

3. **Configure IDE:**
   - Set breakpoints in your IDE
   - Configure PHP Debug extension
   - Set server mapping to `/app`

### Composer Issues

**Symptoms:** Composer commands fail

**Solutions:**

1. **Clear Composer cache:**
   ```bash
   make composer c='clear-cache'
   ```

2. **Update Composer:**
   ```bash
   make sh
   composer self-update
   ```

3. **Check composer.json:**
   ```bash
   make composer c='validate'
   ```

### PHPUnit Test Issues

**Symptoms:** Tests fail or can't run

**Solutions:**

1. **Check test configuration:**
   ```bash
   make test c='--configuration phpunit.xml.dist'
   ```

2. **Run specific test groups:**
   ```bash
   make test c='--group unit'
   make test c='--group integration'
   ```

3. **Check test database:**
   ```bash
   # Ensure test database exists
   make sh
   php bin/console doctrine:database:create --env=test
   ```

## Getting Help

If you're still experiencing issues:

1. **Check logs:**
   ```bash
   make logs
   docker logs prestashop-php-1
   docker logs prestashop-database-1
   ```

2. **Search existing issues:**
   - [GitHub Issues](https://github.com/PrestaShop/PrestaShop/issues)
   - [PrestaShop Forums](https://www.prestashop.com/forums/)

3. **Ask for help:**
   - [PrestaShop Slack](https://www.prestashop-project.org/slack/)
   - [GitHub Discussions](https://github.com/PrestaShop/PrestaShop/discussions)

4. **Create a detailed issue report:**
   - Include your operating system
   - Docker version
   - Complete error messages
   - Steps to reproduce 