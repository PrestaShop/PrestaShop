# Windows Batch Scripts for PrestaShop Docker Management

## Overview

This document describes the Windows batch scripts created to manage the PrestaShop Docker development environment. These scripts provide a convenient way for Windows users to start, stop, and restart the Docker Compose services.

## Purpose

The batch scripts were created to:
1. Provide Windows-native commands for Docker Compose management
2. Replace outdated scripts that referenced the old "admin-fast" folder
3. Properly reference the current "admin-dev" folder for the back office
4. Simplify Docker Compose operations for Windows developers

## Available Scripts

### start.bat
**Location**: `/start.bat`  
**Purpose**: Build and start the PrestaShop Docker development environment

**What it does:**
1. Builds Docker images from scratch with `docker compose build --pull --no-cache`
2. Starts containers in detached mode with `docker compose up --detach --force-recreate --remove-orphans`
3. Displays access URLs and admin credentials
4. Provides helpful next-step instructions

**Usage:**
```batch
start.bat
```

**Expected Output:**
- Docker images build successfully
- Containers start in the background
- Message displays access URLs:
  - Frontend: http://localhost:8001
  - Backend: http://localhost:8001/admin-dev
  - MailDev: http://localhost:1080

### stop.bat
**Location**: `/stop.bat`  
**Purpose**: Stop the PrestaShop Docker development environment

**What it does:**
1. Stops all running containers with `docker compose down --remove-orphans`
2. Removes orphaned containers
3. Preserves database volumes (data is not lost)

**Usage:**
```batch
stop.bat
```

**Expected Output:**
- All containers stop gracefully
- Confirmation message displayed
- Database data preserved in Docker volumes

### restart.bat
**Location**: `/restart.bat`  
**Purpose**: Restart the PrestaShop Docker development environment

**What it does:**
1. Stops all running containers
2. Rebuilds Docker images from scratch
3. Starts containers with fresh configuration
4. Displays access URLs and admin credentials

**Usage:**
```batch
restart.bat
```

**Expected Output:**
- Containers stop
- Images rebuild
- Containers restart
- Access URLs displayed

## Key Corrections from Previous Setup

### Admin Folder Reference
The scripts now correctly reference the **admin-dev** folder, not "admin-fast":
- ✅ Backend URL: `http://localhost:8001/admin-dev`
- ❌ Old (incorrect): `http://localhost:8001/admin-fast`

### Docker Compose Configuration
The scripts use the current `docker-compose.yml` configuration which includes:
- **prestashop-git**: Main PrestaShop container (PHP 8.1 + Apache + Node.js)
- **mysql**: MySQL 8 database
- **maildev**: Email testing service

### Environment Variables
Scripts work with the default environment variables from docker-compose.yml:
- `PS_FOLDER_ADMIN=admin-dev` (correct admin folder)
- `PS_DOMAIN=localhost:8001` (default domain)
- `DB_NAME=prestashop` (database name)
- `ADMIN_MAIL=demo@prestashop.com` (admin email)
- `ADMIN_PASSWD=Correct Horse Battery Staple` (admin password)

## Comparison with Makefile Commands

For users familiar with Linux/Mac Makefile commands, here's the equivalent mapping:

| Batch Script | Makefile Command | Description |
|-------------|------------------|-------------|
| `start.bat` | `make docker-start` | Build and start containers |
| `stop.bat` | `make docker-down` | Stop containers |
| `restart.bat` | `make docker-restart` | Restart containers |

## Additional Docker Commands

While the batch scripts handle the basic operations, you may need these Docker commands for advanced operations:

### View Container Logs
```batch
docker compose logs --follow
```

### View Logs for Specific Service
```batch
docker compose logs --follow prestashop-git
docker compose logs --follow mysql
docker compose logs --follow maildev
```

### Access Container Shell
```batch
docker compose exec prestashop-git bash
```

### Check Container Status
```batch
docker compose ps
```

### Rebuild Without Cache
```batch
docker compose build --no-cache
```

### Remove Volumes (⚠️ Deletes Database Data)
```batch
docker compose down -v
```

## Troubleshooting

### Port Already in Use

**Problem**: Port 8001 or 3306 is already in use by another service.

**Solution:**
```batch
REM Check what's using the port
netstat -ano | findstr :8001

REM Stop the conflicting process or modify docker-compose.yml ports
```

### Docker Not Running

**Problem**: Script fails with "Cannot connect to Docker daemon"

**Solution:**
1. Ensure Docker Desktop is running
2. Check Docker Desktop system tray icon
3. Restart Docker Desktop if needed

### Build Failures

**Problem**: `docker compose build` fails

**Solution:**
```batch
REM Clear Docker cache and rebuild
docker system prune -af
docker volume prune -f
start.bat
```

### Permission Errors

**Problem**: Container cannot write files

**Solution:**
1. Ensure your user has proper permissions
2. Run Docker Desktop as administrator
3. Check volume mount permissions in docker-compose.yml

### Database Connection Errors

**Problem**: PrestaShop cannot connect to database

**Solution:**
```batch
REM Reset database
docker compose down -v
start.bat
```

## Why .bat Files Are Appropriate

While the Makefile is the primary way to manage the development environment on Linux/Mac, `.bat` files are appropriate for Windows users because:

1. **Native Windows Support**: Batch files run natively on Windows without additional tools
2. **No Make Required**: Many Windows users don't have `make` installed
3. **Simple Execution**: Double-click to run, no command-line knowledge needed
4. **Cross-Platform Project**: Supports both Windows and Unix-like systems

## Alternative: Using Make on Windows

If you prefer using Make on Windows, you can:

1. **Install Make for Windows**:
   - Via Chocolatey: `choco install make`
   - Via Git Bash (includes make)
   - Via WSL (Windows Subsystem for Linux)

2. **Use Makefile commands**:
   ```bash
   make docker-start
   make docker-down
   make docker-restart
   ```

## Security Considerations

### Default Credentials
The scripts display default admin credentials:
- Email: `demo@prestashop.com`
- Password: `Correct Horse Battery Staple`

⚠️ **Important**: These are development credentials only. Never use these in production!

To change credentials, set environment variables before running the scripts:
```batch
set ADMIN_MAIL=your-email@example.com
set ADMIN_PASSWD=Your-Secure-Password
start.bat
```

### Database Password
Default database password is `prestashop`. Change it for production:
```batch
set DB_PASSWD=your-secure-password
start.bat
```

## Integration with Existing Workflow

These batch scripts integrate seamlessly with the existing development workflow:

1. **Start Development**:
   ```batch
   start.bat
   ```

2. **Access Backend**: http://localhost:8001/admin-dev

3. **Access Frontend**: http://localhost:8001

4. **Make Code Changes**: Edit files in your IDE

5. **View Changes**: Refresh browser (volume mount reflects changes immediately)

6. **Stop When Done**:
   ```batch
   stop.bat
   ```

## Files Modified/Created

### Created Files
- `/start.bat` - Startup script
- `/stop.bat` - Stop script
- `/restart.bat` - Restart script
- `/COPILOT DOCUMENTATION/WINDOWS_BATCH_SCRIPTS.md` - This documentation

### Referenced Files
- `/docker-compose.yml` - Main Docker configuration (lines 1-73)
- `/docker-compose.mariadb.yml` - Alternative MariaDB configuration (lines 1-77)
- `/Makefile` - Linux/Mac equivalent commands (lines 1-126)

## Verification Steps

### 1. Verify Scripts Exist
```batch
dir *.bat
```
Should show:
- start.bat
- stop.bat
- restart.bat

### 2. Test Start Script
```batch
start.bat
```
Expected:
- ✅ Docker images build successfully
- ✅ Containers start
- ✅ URLs displayed correctly with admin-dev folder

### 3. Test Access
Open browser and navigate to:
- Frontend: http://localhost:8001
- Backend: http://localhost:8001/admin-dev

Expected:
- ✅ PrestaShop loads successfully
- ✅ Admin login page accessible at correct URL
- ✅ No "admin-fast" references anywhere

### 4. Test Stop Script
```batch
stop.bat
```
Expected:
- ✅ Containers stop gracefully
- ✅ No error messages

### 5. Test Restart Script
```batch
restart.bat
```
Expected:
- ✅ Containers stop
- ✅ Images rebuild
- ✅ Containers start
- ✅ Services accessible

## Support and Resources

### Official Documentation
- [PrestaShop DevDocs](https://devdocs.prestashop-project.org/)
- [Development Guide](/docs/DEVELOPMENT.md)
- [Main README](/README.md)

### Community Support
- [PrestaShop Slack](https://www.prestashop-project.org/slack/)
- [GitHub Discussions](https://github.com/PrestaShop/PrestaShop/discussions)
- [PrestaShop Forums](https://www.prestashop.com/forums/)

### Related Documentation
- [DOCKER_VOLUMES_FIX.md](./DOCKER_VOLUMES_FIX.md) - Docker volume configuration
- [QUICK_REFERENCE.md](./QUICK_REFERENCE.md) - Quick verification guide

## Version History

**Created**: 2026-02-09  
**Purpose**: Replace outdated batch scripts referencing "admin-fast" folder  
**Changes**: 
- Corrected admin folder reference from "admin-fast" to "admin-dev"
- Updated to work with current docker-compose.yml configuration
- Added comprehensive error handling and user feedback
- Documented proper usage and troubleshooting

## Contributing

If you need to modify these scripts:

1. **Test Changes**: Always test on Windows before committing
2. **Update Documentation**: Update this file if behavior changes
3. **Maintain Compatibility**: Ensure scripts work with docker-compose.yml
4. **Error Handling**: Always check errorlevel and provide clear messages
5. **User Feedback**: Scripts should be verbose and helpful

## Conclusion

These batch scripts provide a simple, Windows-native way to manage the PrestaShop Docker development environment. They correctly reference the "admin-dev" folder and integrate with the existing Docker Compose configuration, making it easy for Windows developers to start, stop, and restart their development environment.
