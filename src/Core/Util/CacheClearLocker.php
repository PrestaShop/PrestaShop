<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Util;

/**
 * This class handles a lock file that is used between processes to warn them that the Symfony cache is being cleared and other
 * processes should wait until it is done to continue their action. It allows being sure the container is up-to-date after some actions
 * that modify it are done (feature flag switching, module actions, ...). We use the flock function inner blocking system to temporarily
 * stop the processes. While the file is locked all other process wait until the lock is freed.
 *
 * When the initial process is done and releases the lock file all the locked processes will resume their job.
 */
class CacheClearLocker
{
    /**
     * Lock stream is saved as static field, this way if multiple services try to lock the same file (this can happen in
     * test environment), they will be able to detect that a lock has already been made by the current process.
     *
     * @var array<string, resource>
     */
    protected static array $lockStream = [];

    /**
     * Perform a lock on a file, this lock will be unlocked once the unlockFile method is called.
     * Until then any other process will have to wait until the file is unlocked.
     *
     * @param string $environment Kernel environment (prod, dev, test)
     * @param string $appId Kernel application ID (admin, admin-api, front)
     *
     * @return bool returns boolean indicating if the lock file was successfully locked
     */
    public static function lock(string $environment, string $appId): bool
    {
        $lockPath = self::getClearCacheLockPath($environment, $appId);
        $lockStream = fopen($lockPath, 'w');
        if (false === $lockStream) {
            // Could not open writable lock for some reason
            return false;
        }

        // Non-blocking flock, if false is returned it means the file is already locked (meaning the cache is being cleared by another process)
        $fileLocked = flock($lockStream, LOCK_EX | LOCK_NB);
        if (false === $fileLocked) {
            // Clear cache is already locked by another process, so we simply return
            fclose($lockStream);

            return false;
        }

        // Save the locked stream so that we can close it later and most importantly, the process doesn't block it self
        // during the cache clear operation which reboots the app
        self::$lockStream[$lockPath] = $lockStream;

        return true;
    }

    /**
     * Release the lock on the file, this will unblock processes that were waiting for it.
     *
     * @param string $environment Kernel environment (prod, dev, test)
     * @param string $appId Kernel application ID (admin, admin-api, front)
     *
     * @return void
     */
    public static function unlock(string $environment, string $appId): void
    {
        $lockPath = self::getClearCacheLockPath($environment, $appId);
        if (!isset(self::$lockStream[$lockPath])) {
            return;
        }

        self::unlockCacheStream(self::$lockStream[$lockPath], $lockPath);
        unset(self::$lockStream[$lockPath]);
    }

    /**
     * This method is blocking, it means no further code will be executed after this method is called
     * until the locked file has been released by another process. If no process locked the file it
     * executes instantaneously.
     *
     * @param string $environment Kernel environment (prod, dev, test)
     * @param string $appId Kernel application ID (admin, admin-api, front)
     *
     * @return void
     */
    public static function waitUntilUnlocked(string $environment, string $appId): void
    {
        $lockPath = self::getClearCacheLockPath($environment, $appId);

        // CLI environment shouldn't be blocked, this allows for example clearing the cache even when the kernel is blocked for HTTP requests
        // which is exactly what the SymfonyCacheClearer does.
        if (PHPCli::isPHPCli()) {
            return;
        }

        if (isset(self::$lockStream[$lockPath])) {
            // If lockStream is not null it means we are actually in the process that locked it, we don't wait for anything
            // or the cache clear will never happen
            return;
        }

        // No lock file no need to wait for its unlock
        if (!file_exists($lockPath)) {
            return;
        }

        $lockStream = fopen($lockPath, 'w');
        if (false === $lockStream) {
            // Could not open writable lock for some reason
            return;
        }

        // Check if the lock file is currently locked (see locksCacheClear responsible for locking this file), this
        // function call is blocking until the lock has been released.
        flock($lockStream, LOCK_SH);

        // Now that the file is unlocked it means the cache has been cleared we can safely continue the process as the container
        // has been rebuilt and is good to go.
        self::unlockCacheStream($lockStream, $lockPath);
    }

    /**
     * @param resource $lockStream
     */
    protected static function unlockCacheStream($lockStream, string $lockPath): void
    {
        flock($lockStream, LOCK_UN);
        fclose($lockStream);

        // Also remove the lock file so that the lock check is ignored right away
        if (file_exists($lockPath)) {
            @unlink($lockPath);
        }
    }

    /**
     * The lock file path must reflect the kernel it is linked to, but it's important that it's not in the kernel
     * cache folder itself because the whole folder could be removed during cache clearing, and we don't want the lock
     * to be removed when that happens, or the lock file to prevent the removal either.
     *
     * @param string $environment Kernel environment (prod, dev, test)
     * @param string $appId Kernel application ID (admin, admin-api, front)
     *
     * @return string
     */
    protected static function getClearCacheLockPath(string $environment, string $appId): string
    {
        $cacheDir = self::getCacheDir();

        return sprintf('%s/%s_%s_cache_clear.lock', $cacheDir, $appId, $environment);
    }

    protected static function getCacheDir(): string
    {
        return static::getProjectDir() . '/var/cache';
    }

    protected static function getProjectDir(): string
    {
        return realpath(__DIR__ . '/../../..');
    }
}
