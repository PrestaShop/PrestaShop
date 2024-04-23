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
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Class SymfonyCacheClearer clears Symfony cache directly from filesystem.
 *
 * @internal
 */
final class SymfonyCacheClearer implements CacheClearerInterface
{
    private bool $clearCacheRequested = false;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        /* @var AdminKernel */
        global $kernel;
        if (!$kernel) {
            return;
        }

        if ($this->clearCacheRequested) {
            return;
        }
        $this->clearCacheRequested = true;

        $cacheClearLocked = $kernel->locksCacheClear();
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

                        try {
                            // Clear cache without warmup so it's faster to execute
                            $commandLine = $baseCommandLine . 'cache:clear --no-warmup --no-interaction --env=' . $environment . ' --app-id=' . $applicationKernel->getAppId();
                            $output = [];
                            $result = 0;
                            exec($commandLine, $output, $result);

                            if ($result !== 0) {
                                $this->logger->error('SymfonyCacheClearer: Could not clear cache for ' . $applicationKernel->getAppId() . ' env ' . $environment . 'output: ' . var_export($output, true));
                            } else {
                                $this->logger->info('SymfonyCacheClearer: Successfully cleared cache for ' . $applicationKernel->getAppId() . ' env ' . $environment);
                            }
                        } catch (Throwable $e) {
                            // Leave this loop instance since cache warmup is likely to fail as well
                            $this->logger->error('SymfonyCacheClearer: Error while clearing cache for ' . $applicationKernel->getAppId() . ' env ' . $environment . ': ' . $e->getMessage());
                            continue;
                        }

                        // We only warmup cache for prod environment
                        if ($environment !== 'prod') {
                            continue;
                        }

                        try {
                            // Warmup is needed for prod environment, or it will fail (proxy classes for doctrine need to be generated for example), we skip optional warmers though
                            $commandLine = $baseCommandLine . 'cache:warmup --no-optional-warmers --no-interaction --env=' . $environment . ' --app-id=' . $applicationKernel->getAppId();
                            $output = [];
                            $result = 0;
                            exec($commandLine, $output, $result);

                            if ($result !== 0) {
                                $this->logger->error('SymfonyCacheClearer: Could not warm up cache for ' . $applicationKernel->getAppId() . ' env ' . $environment . 'output: ' . var_export($output, true));
                            } else {
                                $this->logger->info('SymfonyCacheClearer: Successfully warmed up cache for ' . $applicationKernel->getAppId() . ' env ' . $environment);
                            }
                        } catch (Throwable $e) {
                            $this->logger->error('SymfonyCacheClearer: Error while warming up cache for ' . $applicationKernel->getAppId() . ' env ' . $environment . ': ' . $e->getMessage());
                        }
                    }
                }
            } catch (Throwable $e) {
                $this->logger->error('SymfonyCacheClearer: Something went wrong while clearing cache: ' . $e->getMessage());
            } finally {
                Hook::exec('actionClearSf2Cache');
                $kernel->unlocksCacheClear();
            }
        });
    }
}
