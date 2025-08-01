# PrestaShop Developer Installation Guide

This guide will help you set up a complete PrestaShop development environment using Docker and the provided Makefile.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **Docker Desktop** (with Docker Compose)
- **Git**
- **Make** (usually pre-installed on macOS/Linux)

## Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/PrestaShop/PrestaShop.git
cd PrestaShop
```

### 2. Start the Development Environment

The easiest way to get started is using the provided Makefile:

```bash
# Build and start all containers
make start

# Or step by step:
make build  # Build Docker images
make up     # Start containers
```

### 3. Access Your PrestaShop Installation

Once the containers are running, you can access:

- **Frontend**: http://localhost:8001
- **Backend**: http://localhost:8001/admin-dev
- **MailDev**: http://localhost:1080 (for email testing)
- **Database**: localhost:3306

**Default Admin Credentials:**
- Email: `demo@prestashop.com`
- Password: `Correct Horse Battery Staple`

## Docker Setup

### Container Architecture

The Docker setup consists of three main services:

1. **PHP Container** (`php`): Apache web server with PHP 8.1+ and Node.js 20
2. **Database Container** (`database`): MySQL 8 with persistent storage
3. **MailDev Container** (`maildev`): Email testing service

### Configuration Files

- `compose.yaml`: Base Docker Compose configuration
- `compose.override.yaml`: Development-specific overrides
- `Dockerfile`: PHP container definition
- `.docker/`: Docker-specific configuration files

### Environment Variables

You can customize the setup using environment variables:

```bash
# Database configuration
export DB_USER=prestashop
export DB_PASSWORD=prestashop
export DB_DATABASE=prestashop

# PrestaShop configuration
export PS_LANGUAGE=en
export PS_COUNTRY=US
export PS_INSTALL_DEMO_PRODUCTS=1

# Admin credentials
export ADMIN_MAIL=your-email@example.com
export ADMIN_PASSWORD=Your-Secure-Password

# HTTP port (default: 8001)
export HTTP_PORT=8001
```

## Makefile Commands

The project includes a comprehensive Makefile for common development tasks:

### Docker Management

```bash
make build          # Build Docker images
make up             # Start containers in detached mode
make start          # Build and start containers
make down           # Stop and remove containers
make logs           # Show container logs
make sh             # Access PHP container shell
make bash           # Access PHP container with bash
```

### Asset Management

```bash
make assets         # Build all assets (admin + frontend)
make assets-dev     # Start asset development servers
make admin          # Build admin assets only
make front          # Build frontend assets only
```

### Development Tools

```bash
make composer c='install'     # Run composer commands
make vendor                   # Install dependencies
make sf c=about              # Run Symfony console commands
make cc                      # Clear cache
```

### Code Quality

```bash
make test                    # Run PHPUnit tests
make php-cs-fixer           # Fix code style
make phpstan                # Run static analysis
make scss-fixer             # Fix SCSS code style
make es-linter              # Fix JavaScript code style
```

## Development Workflow

### 1. Initial Setup

```bash
# Clone and start
git clone https://github.com/PrestaShop/PrestaShop.git
cd PrestaShop
make start

# Install dependencies
make vendor

# Build assets
make assets
```

### 2. Daily Development

```bash
# Start the environment
make up

# Start asset development servers (in a separate terminal)
make assets-dev

# Run tests
make test

# Check code quality
make php-cs-fixer
make phpstan
```

### 3. Database Management

The database is automatically created and configured. If you need to reset it:

```bash
# Stop containers
make down

# Remove database volume
docker volume rm prestashop_database_data

# Restart
make start
```

## Troubleshooting

### Common Issues

1. **Port Already in Use**
   ```bash
   # Change the HTTP port
   export HTTP_PORT=8002
   make up
   ```

2. **Permission Issues**
   ```bash
   # Ensure proper file permissions
   sudo chown -R $USER:$USER .
   ```

3. **Container Won't Start**
   ```bash
   # Clean rebuild
   make down
   docker system prune -f
   make build
   make up
   ```

### Reset Everything

If you need a completely fresh start:

```bash
# Clean everything
git fetch origin
git reset --hard origin/develop
git clean -dfx

# Remove all Docker data
make down
docker system prune -af
docker volume prune -f

# Rebuild and start
make start
```

## Advanced Configuration

### Custom PHP Extensions

To add custom PHP extensions, modify the `Dockerfile`:

```dockerfile
RUN install-php-extensions \
    your-extension \
    another-extension
```

### Custom Apache Configuration

Edit `.docker/conf.d/vhost.conf` to modify Apache settings.

### Development Tools

The setup includes several development tools:

- **Xdebug**: Available for debugging (disabled by default)
- **MailDev**: Email testing interface
- **Composer**: PHP dependency management
- **Node.js**: For asset compilation

## Next Steps

After setting up your development environment:

1. Read the [Contributing Guide](../CONTRIBUTING.md)
2. Check out the [Developer Documentation](https://devdocs.prestashop-project.org/)
3. Join the [PrestaShop Community](https://www.prestashop-project.org/support/)

## Support

If you encounter issues:

1. Check the [Troubleshooting Guide](./TROUBLESHOOT.md)
2. Search existing [GitHub Issues](https://github.com/PrestaShop/PrestaShop/issues)
3. Ask for help on [PrestaShop Slack](https://www.prestashop-project.org/slack/) 