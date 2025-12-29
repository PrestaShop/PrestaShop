# PrestaShop Development Guide

Complete guide for setting up and working with PrestaShop development environment.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Architecture](#architecture)
- [Makefile Commands](#makefile-commands)
- [Environment Configuration](#environment-configuration)
- [Development Workflow](#development-workflow)
- [Troubleshooting](#troubleshooting)

## Prerequisites

- **Docker Desktop** (with Docker Compose)
- **Git**
- **Make** (usually pre-installed on macOS/Linux)

## Quick Start

### 1. Clone and Start

```bash
git clone https://github.com/PrestaShop/PrestaShop.git
cd PrestaShop
make docker-start
```

### 2. Access Your Installation

- **Frontend**: http://localhost:8001
- **Backend**: http://localhost:8001/admin-dev
- **MailDev**: http://localhost:1080 (email testing)
- **Database**: localhost:3306

**Default Admin Credentials:**
- Email: `demo@prestashop.com`
- Password: `Correct Horse Battery Staple`

## Architecture

The setup includes three services:

- **prestashop-git**: PHP 8.1 + Apache + Node.js 20
- **mysql**: MySQL 8 database
- **maildev**: Email testing service

### File Structure

```
.docker/                         # Docker configuration
├── Dockerfile                   # PHP container definition
└── install/                     # Installation scripts

docker-compose.yml               # Base configuration
docker-compose.override.yml.dist # Development overrides
Makefile                         # Development commands
```

## Makefile Commands

### Quick Reference

```bash
make help # Show all available commands

```

### Docker Commands

| Command | Description |
|---------|-------------|
| `make docker-build` | Build Docker images from scratch |
| `make docker-up` | Start containers in detached mode |
| `make docker-start` | Build and start containers |
| `make docker-restart` | Restart the docker hub |
| `make docker-down` | Stop and remove containers |
| `make docker-logs` | Show live container logs |
| `make docker-bash` | Connect to PHP container via bash |

### Asset Management

| Command | Description |
|---------|-------------|
| `make assets` | Build all assets (admin + front) |
| `make assets-dev` | Start development servers for all themes |
| `make wait-assets` | Wait for assets to finish building |
| `make admin` | Build admin assets (default + new theme) |
| `make front` | Build front assets (core + classic) |
| `make admin-default` | Build default admin theme assets |
| `make admin-new-theme` | Build new admin theme assets |
| `make front-core` | Build core front theme assets |
| `make front-classic` | Build classic front theme assets |
| `make front-hummingbird` | Build hummingbird theme assets |

### PrestaShop Installation

| Command | Description |
|---------|-------------|
| `make install` | Install PHP dependencies and build assets |
| `make install-prestashop` | Install fresh PrestaShop database |

### Composer & Symfony

| Command | Description |
|---------|-------------|
| `make composer` | Install PHP dependencies |
| `make cc` | Clear Symfony cache |

### Testing

| Command | Description |
|---------|-------------|
| `make test` | Run all tests (unit + integration) |
| `make test-unit` | Run unit tests |
| `make test-integration` | Run integration tests |
| `make test-integration-behaviour` | Run integration behaviour tests |
| `make test-api-module` | Run API module tests |

### Code Quality

| Command | Description |
|---------|-------------|
| `make cs-fixer` | Run PHP CS Fixer |
| `make cs-fixer-dry` | Run PHP CS Fixer (dry run) |
| `make phpstan` | Run PHPStan analysis |
| `make scss-fixer` | Fix SCSS files |
| `make es-linter` | Fix ESLint issues |

## Environment Configuration

| Category | Variable | Default | Description |
|----------|----------|---------|-------------|
| **Database** | `DB_PASSWD` | `prestashop` | Database password |
| | `DB_NAME` | `prestashop` | Database name |
| | `DB_SERVER` | `mysql` | Database host |
| | `DB_PREFIX` | `ps_` | Database table prefix |
| **PrestaShop** | `PS_INSTALL_AUTO` | `1` | Auto-install PrestaShop |
| | `PS_DOMAIN` | `localhost:8001` | Shop domain |
| | `PS_FOLDER_INSTALL` | `install-dev` | Install folder |
| | `PS_FOLDER_ADMIN` | `admin-dev` | Admin folder |
| | `PS_COUNTRY` | `fr` | Shop country |
| | `PS_LANGUAGE` | `en` | Installation language |
| | `PS_DEV_MODE` | `1` | Development mode |
| | `PS_ENABLE_SSL` | `0` | Enable SSL |
| | `PS_ERASE_DB` | `0` | Erase database on install |
| | `PS_USE_DOCKER_MAILDEV` | `1` | Use MailDev for emails |
| **Admin** | `ADMIN_MAIL` | `demo@prestashop.com` | Admin email |
| | `ADMIN_PASSWD` | `Correct Horse Battery Staple` | Admin password |
| **Development** | `VERSION` | `8.1-apache` | PHP version |
| | `NODE_VERSION` | `20.19.5` | Node.js version |
| | `INSTALL_XDEBUG` | `false` | Install Xdebug |
| | `BLACKFIRE_ENABLE` | `0` | Enable Blackfire profiling |
| | `BLACKFIRE_SERVER_ID` | `0` | Blackfire server ID |
| | `BLACKFIRE_SERVER_TOKEN` | `0` | Blackfire server token |
| | `USER_ID` | `1000` | User ID for container |
| | `GROUP_ID` | `1000` | Group ID for container |
| | `DISABLE_MAKE` | `0` | Disable make commands |
| | `PS_HOSTNAME` | `localhost` | Container hostname |

## Development Workflow

### Initial Setup

```bash
make docker-start       # Build, start containers and install assets and database fixtures
```

### Daily Development

```bash
make docker-up          # Start containers
make cc                 # Clear Symfony's cache
make test               # Run tests
make cs-fixer           # Fix code style
```

### Asset Building

The asset build system provides a simple and reliable way to build all assets:

- **Always Fresh**: Assets are always rebuilt to ensure consistency
- **Parallel Execution**: Multiple assets build in parallel for faster builds
- **Clean Builds**: Node modules are cleaned and reinstalled for each build

**Examples:**
```bash
# Build all assets
make assets
# Build Admin default theme
make admin-default

# Direct script usage (dry-run by default. Use --force to force rebuild)

# All assets
./tools/assets/build.sh # dry-run
./tools/assets/build/sh --force

# Specific asset
./tools/assets/build.sh admin-default # dry-run
./tools/assets/build.sh admin-default --force
```

### Performance Tips

- Use `make test-unit` for faster test runs during development
- Use `make cs-fixer-dry` to check code style without modifying files

## Troubleshooting

### Docker Issues

#### Container Won't Start

**Solutions:**

1. **Clean Docker cache:**
   ```bash
   docker system prune -f
   docker volume prune -f
   ```

2. **Rebuild from scratch:**
   ```bash
   make docker-down
   docker system prune -af
   make docker-start
   ```

#### Port Already in Use

**Solutions:**

1. **Check what's using the port:**
   ```bash
   lsof -i :8001
   ```

2. **Stop conflicting services:**
   ```bash
   docker ps
   docker stop <container-id>
   ```

#### Image Build Fails

**Solutions:**

1. **Increase Docker resources:**
   - Open Docker Desktop settings
   - Increase memory to at least 4GB

2. **Check network connectivity:**
   ```bash
   curl -I https://deb.nodesource.com
   ```

### Database Issues

#### Database Connection Failed

**Solutions:**

1. **Check database container:**
   ```bash
   docker ps | grep mysql
   docker logs prestashop-mysql-1
   ```

2. **Reset database:**
   ```bash
   make docker-down
   docker volume rm prestashop_db-data
   make docker-up
   ```

3. **Check database credentials:**
   ```bash
   docker compose exec prestashop-git env | grep DB_
   ```

#### Database Data Lost

If your database is corrupted / data is lost, you can reset your data by running the following command:
   ```bash
   make install-prestashop
   ```

### Asset Build Issues

#### Node.js Dependencies Missing

**Solutions:**

1. **Install Node.js dependencies:**
   ```bash
   make assets
   ```

2. **Restart Docker containers:**
   ```bash
   make docker-restart
   ```

#### Asset Build Fails

**Solutions:**

1. **Check for syntax errors:**
   ```bash
   make assets
   ```

2. **Rebuild specific assets:**
   ```bash
   make admin-default
   make front-classic
   ```

3. **Restart Docker containers:**
   ```bash
   make docker-restart
   ```

### Permission Issues

#### File Permission Errors

**Solutions:**

1. **Fix ownership:**
   ```bash
   sudo chown -R $USER:$USER .
   ```

2. **Fix permissions:**
   ```bash
   find . -type d -exec chmod 755 {} \;
   find . -type f -exec chmod 644 {} \;
   chmod +x .docker/docker_run_git.sh
   ```

#### Cache Directory Issues

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

### Development Tools Issues

#### Xdebug Not Working

**Solutions:**

1. **Enable Xdebug:**
   ```bash
   export INSTALL_XDEBUG=true
   make docker-start
   ```

2. **Check Xdebug configuration:**
   ```bash
   make docker-sh
   php -i | grep xdebug
   ```

#### PHPUnit Test Issues

**Solutions:**

1. **Run specific test groups:**
   ```bash
   make test-unit
   make test-integration
   ```

2. **Check test database:**
   ```bash
   make docker-sh
   php bin/console doctrine:database:create --env=test
   ```

3. **Run tests with custom options:**
   ```bash
   # Run specific test files or classes
   composer run unit-tests -- --filter=ClassName
   composer run integration-tests -- --filter=ClassName
   
   # Run with specific options
   composer run unit-tests -- --stop-on-failure
   composer run integration-tests -- --stop-on-failure
   ```

### Getting Help

If you're still experiencing issues:

1. **Check logs:**
   ```bash
   make docker-logs
   docker logs prestashop-prestashop-git-1
   docker logs prestashop-mysql-1
   ```

2. **Search existing issues:**
   - [GitHub Issues](https://github.com/PrestaShop/PrestaShop/issues)
   - [PrestaShop Forums](https://www.prestashop.com/forums/)

3. **Ask for help:**
   - [PrestaShop Slack](https://www.prestashop-project.org/slack/)
   - [GitHub Discussions](https://github.com/PrestaShop/PrestaShop/discussions)
