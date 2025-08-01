# PrestaShop Developer Experience Guide

This guide provides a comprehensive overview of the PrestaShop development experience, covering everything from initial setup to advanced development workflows.

## 🚀 Quick Start

Get up and running in 5 minutes:

```bash
# 1. Clone the repository
git clone https://github.com/PrestaShop/PrestaShop.git
cd PrestaShop

# 2. Start the development environment
make start

# 3. Access your PrestaShop installation
# Frontend: http://localhost:8001
# Backend: http://localhost:8001/admin-dev
# Email testing: http://localhost:1080
```

## 📋 Prerequisites

Before you begin, ensure you have:

- **Docker Desktop** (with Docker Compose)
- **Git** (for version control)
- **Make** (usually pre-installed on macOS/Linux)
- **4GB+ RAM** available for Docker
- **10GB+ free disk space**

## 🏗️ Architecture Overview

The PrestaShop development environment consists of:

```
┌─────────────────────────────────────────────────────────────┐
│                    Development Environment                  │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐     │
│  │   PHP 8.1+  │    │   MySQL 8   │    │   MailDev   │     │
│  │  + Apache   │◄──►│  Database   │    │  Email Test │     │
│  │  + Node.js  │    │             │    │             │     │
│  └─────────────┘    └─────────────┘    └─────────────┘     │
│           │                   │                   │         │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐     │
│  │  Composer   │    │  Symfony    │    │   Assets    │     │
│  │  Dependencies│    │   Console   │    │  Build Tools│     │
│  └─────────────┘    └─────────────┘    └─────────────┘     │
└─────────────────────────────────────────────────────────────┘
```

## 🛠️ Development Tools

### Core Tools

| Tool | Purpose | Command |
|------|---------|---------|
| **Docker** | Containerization | `make up/down` |
| **Make** | Task automation | `make help` |
| **Composer** | PHP dependencies | `make composer` |
| **Node.js** | Asset compilation | `make assets` |
| **Symfony Console** | Framework commands | `make sf` |

### Code Quality Tools

| Tool | Purpose | Command |
|------|---------|---------|
| **PHPUnit** | Testing | `make test` |
| **PHP-CS-Fixer** | Code style | `make php-cs-fixer` |
| **PHPStan** | Static analysis | `make phpstan` |
| **ESLint** | JavaScript linting | `make es-linter` |
| **Stylelint** | SCSS linting | `make scss-fixer` |

## 🔄 Development Workflows

### 1. Initial Setup Workflow

```bash
# Complete setup process
git clone https://github.com/PrestaShop/PrestaShop.git
cd PrestaShop
make start                    # Build and start containers
make vendor                   # Install PHP dependencies
make assets                   # Build all assets
make install-prestashop       # Install PrestaShop
```

### 2. Daily Development Workflow

```bash
# Start your day
make up                       # Start containers
make assets-dev               # Start asset watchers (separate terminal)

# During development
make test                     # Run tests
make php-cs-fixer            # Fix code style
make cc                      # Clear cache when needed

# End your day
make down                     # Stop containers
```

### 3. Feature Development Workflow

```bash
# 1. Create feature branch
git checkout -b feature/your-feature

# 2. Start development environment
make up

# 3. Make your changes...

# 4. Test your changes
make test
make phpstan
make php-cs-fixer

# 5. Build assets
make assets

# 6. Commit your changes
git add .
git commit -m "feat: add your feature"

# 7. Push and create PR
git push origin feature/your-feature
```

### 4. Bug Fix Workflow

```bash
# 1. Reproduce the bug
make up
# Navigate to the issue...

# 2. Debug the issue
make sh                      # Access container
make logs                    # Check logs
make cc                      # Clear cache

# 3. Fix the bug
# Make your changes...

# 4. Test the fix
make test
make phpstan

# 5. Commit the fix
git commit -m "fix: resolve the bug"
```

## 🎨 Asset Development

### Frontend Assets

The project includes multiple themes that can be developed simultaneously:

```bash
# Build all frontend assets
make front

# Build specific themes
make front-classic      # Classic theme
make front-hummingbird  # Hummingbird theme

# Development mode (watch for changes)
make assets-dev
```

### Admin Assets

```bash
# Build all admin assets
make admin

# Build specific admin themes
make admin-default      # Default admin theme
make admin-new-theme    # New admin theme
```

### Asset Development Tips

1. **Use development mode** for live reloading:
   ```bash
   make assets-dev
   ```

2. **Build for production** before committing:
   ```bash
   make assets
   ```

3. **Check for linting errors**:
   ```bash
   make scss-fixer
   make es-linter
   ```

## 🧪 Testing

### Running Tests

```bash
# Run all tests
make test

# Run specific test groups
make test c='--group unit'
make test c='--group integration'
make test c='--group e2e'

# Run with coverage
make test c='--coverage-html coverage'

# Run specific test file
make test c='--filter=testSpecificFunction'
```

### Test Best Practices

1. **Write tests for new features**
2. **Run tests before committing**
3. **Use descriptive test names**
4. **Keep tests fast and focused**
5. **Mock external dependencies**

## 🔍 Debugging

### Debugging Tools

1. **Container Access**:
   ```bash
   make sh              # Shell access
   make bash            # Bash with history
   ```

2. **Logs**:
   ```bash
   make logs            # All container logs
   docker logs prestashop-php-1    # PHP container logs
   ```

3. **Xdebug** (when enabled):
   ```bash
   export XDEBUG_MODE=debug
   make up
   ```

### Common Debugging Scenarios

#### Database Issues
```bash
# Check database connection
make sh -c 'php bin/console doctrine:database:create --if-not-exists'

# Reset database
make down
docker volume rm prestashop_database_data
make up
```

#### Cache Issues
```bash
# Clear all caches
make cc

# Clear specific caches
make sf c='cache:clear --env=dev'
make sf c='cache:warmup'
```

#### Asset Issues
```bash
# Rebuild assets
make assets

# Clear asset caches
make sh -c 'rm -rf var/cache/*'
```

## 📊 Performance Optimization

### Development Performance

1. **Exclude vendor directory** from bind mounts:
   ```yaml
   # In compose.override.yaml
   volumes:
     - ./:/app
     - /app/vendor
   ```

2. **Use volume mounts** for cache directories:
   ```yaml
   volumes:
     - /app/var/cache
     - /app/var/logs
   ```

3. **Limit container resources**:
   ```yaml
   services:
     php:
       deploy:
         resources:
           limits:
             memory: 2G
   ```

### Production Preparation

1. **Optimize assets**:
   ```bash
   make assets
   ```

2. **Clear development caches**:
   ```bash
   make cc
   ```

3. **Run security checks**:
   ```bash
   make phpstan
   make test
   ```

## 🔧 Configuration Management

### Environment Variables

Create a `.env` file for local configuration:

```bash
# Database
DB_USER=prestashop
DB_PASSWORD=your-secure-password
DB_DATABASE=prestashop

# PrestaShop
PS_LANGUAGE=en
PS_COUNTRY=US
PS_INSTALL_DEMO_PRODUCTS=1

# Admin
ADMIN_MAIL=your-email@example.com
ADMIN_PASSWORD=Your-Secure-Password

# Development
HTTP_PORT=8001
XDEBUG_MODE=off
```

### Custom Configurations

1. **PHP Configuration**:
   Edit `.docker/conf.d/10-app.ini`

2. **Apache Configuration**:
   Edit `.docker/conf.d/vhost.conf`

3. **Docker Configuration**:
   Edit `compose.override.yaml`

## 🚀 Deployment Preparation

### Pre-deployment Checklist

- [ ] All tests pass (`make test`)
- [ ] Code style is fixed (`make php-cs-fixer`)
- [ ] Static analysis passes (`make phpstan`)
- [ ] Assets are built (`make assets`)
- [ ] Cache is cleared (`make cc`)
- [ ] Documentation is updated

### Production Build

```bash
# Build production assets
make assets

# Run production tests
make test c='--env=prod'

# Clear all caches
make cc
```

## 🤝 Collaboration

### Code Review Process

1. **Before submitting PR**:
   ```bash
   make php-cs-fixer
   make phpstan
   make test
   make assets
   ```

2. **Review checklist**:
   - [ ] Code follows style guidelines
   - [ ] Tests are included
   - [ ] Documentation is updated
   - [ ] No security issues
   - [ ] Performance impact considered

### Team Development

1. **Use consistent environment**:
   - Same Docker setup
   - Same PHP/Node.js versions
   - Same development tools

2. **Share configurations**:
   - Commit `.env.example`
   - Document custom setups
   - Use consistent naming

3. **Regular sync**:
   - Update dependencies
   - Sync with main branch
   - Review and merge PRs

## 📚 Learning Resources

### Official Documentation

- [PrestaShop Developer Docs](https://devdocs.prestashop-project.org/)
- [PrestaShop User Docs](https://docs.prestashop-project.org/)
- [Contributing Guide](../CONTRIBUTING.md)

### Community Resources

- [PrestaShop Slack](https://www.prestashop-project.org/slack/)
- [GitHub Discussions](https://github.com/PrestaShop/PrestaShop/discussions)
- [PrestaShop Forums](https://www.prestashop.com/forums/)

### Development Tools

- [Docker Documentation](https://docs.docker.com/)
- [Make Documentation](https://www.gnu.org/software/make/)
- [Composer Documentation](https://getcomposer.org/doc/)

## 🆘 Getting Help

### When You're Stuck

1. **Check the troubleshooting guide**: [TROUBLESHOOT.md](./TROUBLESHOOT.md)
2. **Search existing issues**: [GitHub Issues](https://github.com/PrestaShop/PrestaShop/issues)
3. **Ask the community**: [PrestaShop Slack](https://www.prestashop-project.org/slack/)
4. **Create a detailed issue** with:
   - Your operating system
   - Docker version
   - Complete error messages
   - Steps to reproduce

### Useful Commands

```bash
# Get help
make help

# Check system status
docker ps
docker system df

# View logs
make logs

# Reset everything
make down
docker system prune -af
make start
```

## 🎯 Best Practices Summary

1. **Always use the Makefile** for consistency
2. **Run tests before committing** code
3. **Keep your environment updated** with latest changes
4. **Document your custom configurations**
5. **Use meaningful commit messages**
6. **Regular backups** of your development data
7. **Monitor resource usage** to prevent issues
8. **Follow the established workflows** for consistency

Remember: The goal is to make development as smooth and efficient as possible. If something feels cumbersome, there's likely a better way - ask the community! 