# PrestaShop Makefile Guide

This document provides a comprehensive guide to the PrestaShop Makefile, which simplifies common development tasks.

## Overview

The Makefile provides a unified interface for common development tasks, abstracting away the complexity of Docker commands and providing a consistent development experience.

## Quick Reference

```bash
# Show all available commands
make help

# Start development environment
make start

# Stop development environment
make down

# Access container shell
make sh

# Build assets
make assets

# Run tests
make test
```

## Command Categories

### 🐳 Docker Management

#### `make build`
Builds the Docker images with the latest changes.

```bash
make build
```

**What it does:**
- Builds the PHP container with all dependencies
- Uses `--pull --no-cache` for clean builds
- Sets `COMPOSE_BAKE=true` for optimized builds

#### `make up`
Starts the Docker containers in detached mode.

```bash
make up
```

**What it does:**
- Starts all services (php, database, maildev)
- Uses `--detach --force-recreate` for clean startup
- Services run in background

#### `make start`
Combines build and up commands for a complete startup.

```bash
make start
```

**Equivalent to:**
```bash
make build && make up
```

#### `make down`
Stops and removes all containers and networks.

```bash
make down
```

**What it does:**
- Stops all running containers
- Removes containers and networks
- Uses `--remove-orphans` to clean up

#### `make logs`
Shows live logs from all containers.

```bash
make logs
```

**Options:**
- `--tail=0` - Shows all logs from the beginning
- `--follow` - Follows logs in real-time

#### `make sh`
Accesses the PHP container with a shell.

```bash
make sh
```

**What it does:**
- Opens an interactive shell in the PHP container
- Useful for running commands directly in the container

#### `make bash`
Accesses the PHP container with bash (better for command history).

```bash
make bash
```

**Difference from `make sh`:**
- Uses bash instead of sh
- Provides better command history support
- Up/down arrows work for previous commands

### 🎨 Asset Management

#### `make assets`
Builds all assets (admin and frontend themes).

```bash
make assets
```

**What it builds:**
- Admin default theme
- Admin new theme
- Frontend classic theme
- Frontend hummingbird theme

#### `make assets-dev`
Starts development servers for all themes.

```bash
make assets-dev
```

**What it does:**
- Starts concurrent watch processes for all themes
- Uses `npx concurrently` for parallel processing
- Color-coded output for each theme
- Auto-restart on file changes

#### `make admin`
Builds admin assets only.

```bash
make admin
```

**Builds:**
- Admin default theme
- Admin new theme

#### `make front`
Builds frontend assets only.

```bash
make front
```

**Builds:**
- Frontend classic theme
- Frontend hummingbird theme

#### Individual Theme Commands

```bash
make admin-default      # Build admin default theme
make admin-new-theme    # Build admin new theme
make front-classic      # Build frontend classic theme
make front-hummingbird  # Build frontend hummingbird theme
```

### 🛒 PrestaShop Management

#### `make install-prestashop`
Installs PrestaShop in the container.

```bash
make install-prestashop
```

**What it does:**
- Runs the installation script
- Sets up database and initial configuration
- Creates admin user

### 🧙 Composer Management

#### `make composer`
Runs Composer commands in the PHP container.

```bash
# Install dependencies
make composer c='install'

# Add a package
make composer c='require symfony/orm-pack'

# Update dependencies
make composer c='update'

# Show installed packages
make composer c='show'
```

#### `make vendor`
Installs vendor dependencies.

```bash
make vendor
```

**What it does:**
- Installs dependencies from `composer.lock`
- Uses `--prefer-dist --no-dev --no-progress --no-scripts --no-interaction`
- Optimized for production-like installation

### 🎵 Symfony Console

#### `make sf`
Runs Symfony console commands.

```bash
# Show available commands
make sf c=about

# Clear cache
make sf c='cache:clear'

# Show routes
make sf c='debug:router'

# Create database
make sf c='doctrine:database:create'
```

#### `make cc`
Clears the Symfony cache.

```bash
make cc
```

**Equivalent to:**
```bash
make sf c='cache:clear'
```

### 🧹 Code Quality

#### `make test`
Runs PHPUnit tests.

```bash
# Run all tests
make test

# Run specific test group
make test c='--group unit'

# Run with coverage
make test c='--coverage-html coverage'

# Stop on first failure
make test c='--stop-on-failure'
```

#### `make php-cs-fixer`
Fixes PHP code style using PHP-CS-Fixer.

```bash
make php-cs-fixer
```

**What it does:**
- Runs PHP-CS-Fixer in fix mode
- Uses project configuration
- Automatically fixes code style issues

#### `make php-cs-fixer-dry`
Runs PHP-CS-Fixer in dry-run mode.

```bash
make php-cs-fixer-dry
```

**What it does:**
- Shows what would be fixed without making changes
- Useful for CI/CD pipelines
- Shows diff of proposed changes

#### `make phpstan`
Runs PHPStan static analysis.

```bash
make phpstan
```

**What it does:**
- Analyzes code for potential errors
- Uses `phpstan.neon.dist` configuration
- Reports type errors and other issues

#### `make scss-fixer`
Fixes SCSS code style in all themes.

```bash
make scss-fixer
```

**What it fixes:**
- Admin default theme SCSS
- Admin new theme SCSS
- Frontend classic theme SCSS
- Frontend hummingbird theme SCSS

#### `make es-linter`
Fixes JavaScript/ES6 code style.

```bash
make es-linter
```

**What it fixes:**
- Admin default theme JavaScript
- Admin new theme JavaScript
- Frontend classic theme JavaScript
- Frontend themes JavaScript

## Development Workflows

### Initial Setup

```bash
# 1. Clone and start
git clone https://github.com/PrestaShop/PrestaShop.git
cd PrestaShop
make start

# 2. Install dependencies
make vendor

# 3. Build assets
make assets

# 4. Install PrestaShop
make install-prestashop
```

### Daily Development

```bash
# 1. Start environment
make up

# 2. Start asset development (in separate terminal)
make assets-dev

# 3. Make changes to code...

# 4. Run tests
make test

# 5. Check code quality
make php-cs-fixer
make phpstan
```

### Code Review Preparation

```bash
# 1. Fix code style
make php-cs-fixer
make scss-fixer
make es-linter

# 2. Run static analysis
make phpstan

# 3. Run tests
make test

# 4. Build assets
make assets
```

### Debugging

```bash
# 1. Access container
make sh

# 2. Check logs
make logs

# 3. Clear cache
make cc

# 4. Restart services
make down && make up
```

## Environment Variables

The Makefile respects several environment variables:

```bash
# Docker Compose
export DOCKER_COMP=docker compose

# PHP container
export PHP_CONT="docker compose exec php"

# PHP executable
export PHP="docker compose exec php php"

# Composer
export COMPOSER="docker compose exec php composer"

# Symfony console
export SYMFONY="docker compose exec php php bin/console"
```

## Customization

### Adding New Commands

To add a new command to the Makefile:

```makefile
## —— Your Category 🎯 ——————————————————————————————————————————————————————————
your-command: ## Description of what your command does
	@$(DOCKER_COMP) exec php your-command-here
```

### Modifying Existing Commands

You can override commands by creating a local `Makefile.local`:

```makefile
# Makefile.local
test: ## Run tests with custom options
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit --verbose $(c)
```

### Command Parameters

Many commands accept parameters using the `c=` syntax:

```bash
# Composer with parameters
make composer c='require --dev phpunit/phpunit'

# Symfony with parameters
make sf c='doctrine:migrations:migrate --no-interaction'

# PHPUnit with parameters
make test c='--filter=testSpecificFunction'
```

## Best Practices

### 1. Use the Help Command

Always start with `make help` to see available commands:

```bash
make help
```

### 2. Chain Commands

Use command chaining for common workflows:

```bash
# Clean restart
make down && make start

# Full rebuild
make down && make build && make up
```

### 3. Use Parameters

Leverage the parameter system for flexibility:

```bash
# Run specific tests
make test c='--group=integration --stop-on-failure'

# Install specific package
make composer c='require symfony/mailer'
```

### 4. Monitor Resources

Keep an eye on resource usage:

```bash
# Check container status
docker ps

# Monitor logs
make logs

# Check disk usage
docker system df
```

## Troubleshooting

### Common Issues

1. **Command not found**: Ensure you're in the project root directory
2. **Permission denied**: Check file permissions with `ls -la`
3. **Container not running**: Use `make up` to start containers
4. **Port conflicts**: Change port with `export HTTP_PORT=8002`

### Debug Commands

```bash
# Check container status
docker ps

# View container logs
make logs

# Access container for debugging
make sh

# Check environment variables
make sh -c 'env | grep DOCKER'
```

## Integration with IDEs

### VS Code

Add to `.vscode/settings.json`:

```json
{
    "makefile.makeDirectory": ".",
    "makefile.makefilePath": "./Makefile"
}
```

### PhpStorm

Configure external tools:

1. Go to Settings → Tools → External Tools
2. Add new tool with:
   - Program: `make`
   - Arguments: `test`
   - Working directory: `$ProjectFileDir$`

### Git Hooks

Add to `.git/hooks/pre-commit`:

```bash
#!/bin/bash
make php-cs-fixer
make phpstan
make test c='--stop-on-failure'
```

## Contributing

When adding new commands to the Makefile:

1. Follow the existing naming conventions
2. Add proper documentation in the help section
3. Use consistent formatting and spacing
4. Test commands thoroughly
5. Update this documentation 