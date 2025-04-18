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

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer;

use AdminAPIKernel;
use AdminKernel;
use AppKernel;
use FrontKernel;
use Hook;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Util\CacheClearLocker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

/**
 * Class SymfonyCacheClearer clears Symfony cache directly from filesystem.
 *
 * @internal
 */
final class SymfonyCacheClearer implements CacheClearerInterface
{
    public const MANUAL_REMOVAL_TRIALS = 5;

    private bool $clearCacheRequested = false;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Filesystem $filesystem,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        /* @var AppKernel */
        global $kernel;
        if (!$kernel) {
            return;
        }

        if ($this->clearCacheRequested) {
            return;
        }
        $this->clearCacheRequested = true;

        $cacheClearLocked = CacheClearLocker::lock($kernel->getEnvironment(), $kernel->getAppId());
        if (false === $cacheClearLocked) {
            // The lock was not possible for some reason we should exit
            return;
        }

        // If we reach here it means the clear lock file is locked, we register a shutdown function that will clear the cache once
        // the current process is over.
        register_shutdown_function(function () use ($kernel) {
            try {
                // Remove time limit to make sure the cache has the time to be cleared
                set_time_limit(0);

                $environments = ['prod', 'dev'];
                $applicationKernelClasses = [AdminKernel::class, AdminAPIKernel::class, FrontKernel::class];
                $baseCommandLine = 'php -d memory_limit=-1 ' . $kernel->getProjectDir() . '/bin/console ';
                foreach ($applicationKernelClasses as $applicationKernelClass) {
                    foreach ($environments as $environment) {
                        /** @var AppKernel $applicationKernel */
                        $applicationKernel = new $applicationKernelClass($environment, false);
                        $cacheDir = $applicationKernel->getCacheDir();

                        if (!file_exists($cacheDir)) {
                            $this->logger->info('SymfonyCacheClearer: No cache to clear for ' . $applicationKernel->getAppId() . ' env ' . $environment);
                            continue;
                        }

                        // Lock the cache for this particular environment and app
                        CacheClearLocker::lock($applicationKernel->getEnvironment(), $applicationKernel->getAppId());

                        try {
                            // Clear cache without warmup so it's faster to execute
                            $commandLine = $baseCommandLine . 'cache:clear --no-warmup --no-interaction --env=' . $environment . ' --app-id=' . $applicationKernel->getAppId();
                            $output = [];
                            $result = 0;
                            exec($commandLine, $output, $result);

                            if ($result !== 0) {
                                $this->logger->error('SymfonyCacheClearer: Could not clear cache for ' . $applicationKernel->getAppId() . ' env ' . $environment . ' result: ' . $result . ' output: ' . var_export($output, true));
                                $this->manualClearCache($cacheDir);
                                $this->unlockOtherCache($kernel, $applicationKernel->getEnvironment(), $applicationKernel->getAppId());
                                continue;
                            } else {
                                $this->logger->info('SymfonyCacheClearer: Successfully cleared cache for ' . $applicationKernel->getAppId() . ' env ' . $environment);
                            }
                        } catch (Throwable $e) {
                            // Leave this loop instance since cache warmup is likely to fail as well
                            $this->logger->error('SymfonyCacheClearer: Error while clearing cache for ' . $applicationKernel->getAppId() . ' env ' . $environment . ': ' . $e->getMessage());
                            $this->manualClearCache($cacheDir);
                            $this->unlockOtherCache($kernel, $applicationKernel->getEnvironment(), $applicationKernel->getAppId());
                            continue;
                        }

                        // We only warmup cache for prod environment
                        if ($environment !== 'prod') {
                            // No warmup needed so we can unlock the cache
                            $this->unlockOtherCache($kernel, $applicationKernel->getEnvironment(), $applicationKernel->getAppId());
                            continue;
                        }

                        try {
                            // Warmup is needed for prod environment, or it will fail (proxy classes for doctrine need to be generated for example), we skip optional warmers though
                            $commandLine = $baseCommandLine . 'cache:warmup --no-optional-warmers --no-interaction --env=' . $environment . ' --app-id=' . $applicationKernel->getAppId();
                            $output = [];
                            $result = 0;
                            exec($commandLine, $output, $result);

                            if ($result !== 0) {
                                $this->logger->error('SymfonyCacheClearer: Could not warm up cache for ' . $applicationKernel->getAppId() . ' env ' . $environment . ' result: ' . $result . ' output: ' . var_export($output, true));
                            } else {
                                $this->logger->info('SymfonyCacheClearer: Successfully warmed up cache for ' . $applicationKernel->getAppId() . ' env ' . $environment);
                            }
                        } catch (Throwable $e) {
                            $this->logger->error('SymfonyCacheClearer: Error while warming up cache for ' . $applicationKernel->getAppId() . ' env ' . $environment . ': ' . $e->getMessage());
                        }

                        $this->unlockOtherCache($kernel, $applicationKernel->getEnvironment(), $applicationKernel->getAppId());
                    }
                }
            } catch (Throwable $e) {
                $this->logger->error('SymfonyCacheClearer: Something went wrong while clearing cache: ' . $e->getMessage());
            } finally {
                Hook::exec('actionClearSf2Cache');
                CacheClearLocker::unlock($kernel->getEnvironment(), $kernel->getAppId());
            }
        });
    }

    protected function manualClearCache(string $cacheDir): void
    {
        for ($i = 0; $i < self::MANUAL_REMOVAL_TRIALS; ++$i) {
            try {
                $this->logger->info(sprintf('SymfonyCacheClearer: Trying manual removal %d/%d of cache folder %s', $i + 1, self::MANUAL_REMOVAL_TRIALS, $cacheDir));
                $this->filesystem->remove($cacheDir);
                if (!is_dir($cacheDir)) {
                    break;
                }
            } catch (Throwable $e) {
                $this->logger->error(sprintf('Error while removing cache folder: %s', $e->getMessage()));
            }
        }

        if (is_dir($cacheDir)) {
            $this->logger->error(sprintf('Folder cache %s still present even after %d manual removals', $cacheDir, self::MANUAL_REMOVAL_TRIALS));
        } else {
            $this->logger->info(sprintf('Cache folder %s successfully cleared manually', $cacheDir));
        }
    }

    protected function unlockOtherCache(AppKernel $currentKernel, string $otherEnvironment, string $otherAppId): void
    {
        // We don't unlock the current process during the loop, this will be done in the "finally" block at the end of the loop
        if ($otherEnvironment === $currentKernel->getEnvironment() && $otherAppId === $currentKernel->getAppId()) {
            return;
        }

        CacheClearLocker::unlock($otherEnvironment, $otherAppId);
    }
}
