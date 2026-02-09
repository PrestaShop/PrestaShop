# Quick Reference - Docker Volumes Fix

## What Was Changed?

### docker-compose.yml
Removed 4 named volumes that were preventing images from loading:
- ❌ `ps-var` - Was overlaying /var/www/html/var
- ❌ `ps-img` - Was overlaying /var/www/html/img  
- ❌ `ps-upload` - Was overlaying /var/www/html/upload
- ❌ `ps-download` - Was overlaying /var/www/html/download
- ✅ `db-data` - Kept for MySQL database persistence

## Quick Verification

### 1. Validate Configuration
```bash
docker compose config --quiet
```
Should complete without errors.

### 2. Clean Old Volumes (if you had containers running before)
```bash
docker compose down -v
docker volume rm ps-var ps-img ps-upload ps-download 2>/dev/null || true
```

### 3. Start Containers
```bash
docker compose up -d
```

### 4. Check Mounts
```bash
docker compose exec prestashop-git mount | grep /var/www/html
```
Should show only ONE mount: the bind mount from host to `/var/www/html`.
Should NOT show separate mounts for img, upload, download, or var subdirectories.

### 5. Verify Image Access
```bash
# Check that img directory is accessible
docker compose exec prestashop-git ls -la /var/www/html/img

# Compare with host
ls -la img/
```
Both should show the same files.

## What This Fixes

✅ Product images now load in front office  
✅ Category images display correctly  
✅ Logo images appear in admin panel  
✅ File uploads work and are visible on host  
✅ Downloads are accessible  
✅ Cache and logs in var/ are accessible from host

## What's Preserved

✅ Database data persists between restarts  
✅ PrestaShop core configuration untouched  
✅ All environment variables maintained  
✅ Port mappings unchanged  
✅ Network configuration preserved

## Rollback (if needed)

If you need to rollback these changes:
```bash
git revert HEAD
docker compose down
docker compose up -d
```

## Full Documentation

See `DOCKER_VOLUMES_FIX.md` for comprehensive technical documentation including:
- Detailed explanation of the issue
- Docker volume behavior details
- Complete troubleshooting guide
- Before/after code examples with line numbers
