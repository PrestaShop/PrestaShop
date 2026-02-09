# Docker Volumes Configuration Fix

## Overview
This document describes the fix applied to resolve image loading issues in PrestaShop caused by incorrectly configured Docker named volumes.

## Issue Description

### Problem
The `docker-compose.yml` file contained named volumes that were overlaying the bind mount and preventing PrestaShop from serving product images and logos in both front and back office.

### Root Cause
Named volumes (`ps-var`, `ps-img`, `ps-upload`, `ps-download`) were mounted on top of the main bind mount (`./:/var/www/html`). When Docker mounts a named volume on top of a bind mount, the named volume takes precedence and isolates that specific directory from the host filesystem.

This meant that:
1. The `img`, `upload`, `download`, and `var` directories in the container were isolated from the corresponding directories on the host
2. Any images or files created in PrestaShop were stored only in the Docker named volumes
3. PrestaShop could not access existing images/files from the host filesystem
4. Development workflow was broken as changes to these directories on the host were not reflected in the container

## Files Modified

### File: `docker-compose.yml`
**Location**: `/docker-compose.yml` (root of repository)

#### Changes Made

**Lines 1-6** (Volume Definitions Section):
```yaml
# BEFORE:
volumes:
  db-data:
  ps-var:
  ps-img:
  ps-upload:
  ps-download:

# AFTER:
volumes:
  db-data:
```

**Lines 60-65** (PrestaShop Service Volumes):
```yaml
# BEFORE:
    volumes:
      - ./:/var/www/html
      - ps-var:/var/www/html/var
      - ps-img:/var/www/html/img
      - ps-upload:/var/www/html/upload
      - ps-download:/var/www/html/download

# AFTER:
    volumes:
      - ./:/var/www/html
```

## Rationale

### Why Remove These Named Volumes?

1. **Bind Mount Isolation**: Named volumes mounted on subdirectories of a bind mount create isolated spaces that prevent the bind mount from accessing those directories
2. **Development Workflow**: PrestaShop development requires direct access to `img`, `upload`, `download`, and `var` directories from the host filesystem
3. **Image Serving**: PrestaShop needs to serve images and files that exist in the repository and on the host filesystem
4. **Unnecessary Persistence**: For a development environment, these directories don't need separate persistence - they should be part of the main bind mount

### Why Keep db-data Volume?

The `db-data` volume is retained because:
1. **Database Persistence**: MySQL data needs to persist between container restarts
2. **No Bind Mount Conflict**: It's mounted to `/var/lib/mysql` which is not part of the PrestaShop bind mount
3. **Best Practice**: Database files should not be directly accessible from the host filesystem for performance and integrity reasons

## Impact

### What Changed
- **Removed**: Four named volumes (`ps-var`, `ps-img`, `ps-upload`, `ps-download`)
- **Kept**: Database persistence volume (`db-data`)
- **Result**: All PrestaShop directories are now accessible via the bind mount

### Expected Behavior After Fix
1. Images in the `img` directory are now visible to PrestaShop
2. Uploaded files in the `upload` directory are accessible
3. Downloads in the `download` directory work correctly
4. The `var` directory (cache, logs, etc.) is accessible from the host
5. Changes to these directories on the host filesystem are immediately reflected in the container
6. Database data continues to persist between container restarts

## Technical Details

### Docker Volume Behavior
When Docker encounters volume configurations like:
```yaml
volumes:
  - ./:/var/www/html
  - named-volume:/var/www/html/subdirectory
```

The behavior is:
1. First, the bind mount `./` is mounted to `/var/www/html`
2. Then, the named volume is mounted on top at `/var/www/html/subdirectory`
3. The named volume "shadows" the subdirectory from the bind mount
4. The container sees the named volume content, not the bind mount content at that path

### PrestaShop Requirements
PrestaShop expects:
- `img/`: Product images, category images, manufacturer logos, etc.
- `upload/`: File uploads from admin panel
- `download/`: Downloadable products
- `var/`: Cache, logs, and temporary files

All of these need to be accessible from both the host and container for proper development workflow.

## History

### When Was This Introduced?
The problematic configuration was introduced with the commit message "docker: persist var/img/upload/download using named volumes" (commit bf6561dc1147bb598f2003201e74986c5b7ef876 at the time of this documentation - note that this SHA may change if the repository history is rebased or modified).

### Why Was It Added?
The original intent was likely to persist these directories between container rebuilds. However, this approach is unnecessary and counterproductive for a development environment using bind mounts.

## Verification Steps

After applying this fix, verify the following:

1. **Start the containers**:
   ```bash
   docker compose up -d
   ```

2. **Check volume mounts**:
   ```bash
   docker compose exec prestashop-git df -h | grep /var/www/html
   ```
   You should see only the main bind mount, not separate mounts for subdirectories.

3. **Verify image access**:
   - Navigate to PrestaShop front office
   - Check that product images load correctly
   - Verify logo displays in both front and back office

4. **Test file upload**:
   - Go to admin panel
   - Upload a new product image
   - Verify the image appears in the host filesystem at `./img/`

5. **Check database persistence**:
   ```bash
   docker compose down
   docker compose up -d
   ```
   Verify that your data persists after restart.

## Related Configuration

### Other Docker Compose Files
- `docker-compose.mariadb.yml`: Alternative configuration using MariaDB instead of MySQL
- `docker-compose.override.yml.dist`: Template for local overrides

If you're using any of these files, ensure they don't reintroduce the named volumes.

## Best Practices

### For Development Environments
- Use bind mounts for code and assets that need to be edited on the host
- Use named volumes only for data that should be isolated (like databases)
- Avoid mounting named volumes on top of bind mount subdirectories

### For Production Environments
Production deployments might have different requirements:
- Consider using named volumes for all persistent data
- Avoid bind mounts in production for security and performance
- Use proper backup strategies for named volumes

## PrestaShop Core Configurations

### No Core Files Modified
This fix only modifies the Docker configuration and does not alter any PrestaShop core files:
- No changes to PrestaShop PHP code
- No changes to PrestaShop configuration files
- No changes to PrestaShop themes or modules
- No changes to database schema or data

### PrestaShop Compatibility
This fix is compatible with all PrestaShop versions as it only affects the Docker development environment setup, not PrestaShop itself.

## Troubleshooting

### If Images Still Don't Load

1. **Clear named volumes**:
   If you had containers running with the old configuration, old named volumes might still exist:
   ```bash
   docker compose down -v
   docker volume ls | grep ps-
   docker volume rm ps-var ps-img ps-upload ps-download
   ```

2. **Rebuild containers**:
   ```bash
   docker compose build --no-cache
   docker compose up -d
   ```

3. **Check file permissions**:
   Ensure the directories have correct permissions:
   ```bash
   ls -la img/ upload/ download/ var/
   ```

4. **Verify bind mount**:
   ```bash
   docker compose exec prestashop-git ls -la /var/www/html/img
   ```
   Compare the content with your host filesystem.

## Additional Resources

- [Docker Volumes Documentation](https://docs.docker.com/storage/volumes/)
- [Docker Compose Volumes](https://docs.docker.com/compose/compose-file/compose-file-v3/#volumes)
- [PrestaShop Development Setup](https://devdocs.prestashop-project.org/)

## Support

If you encounter issues after applying this fix:
1. Check the Troubleshooting section above
2. Verify all steps in the Verification Steps section
3. Review Docker and container logs: `docker compose logs prestashop-git`
4. Ensure no local overrides are reintroducing the named volumes
