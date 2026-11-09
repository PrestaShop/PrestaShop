<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer;

use AdminAPIKernel;
use AdminKernel;
use AppKernel;
use FrontKernel;
use Hook;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony\KernelCacheClearerInterface;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Util\CacheClearLocker;
use PrestaShopBundle\Cache\LegacyCacheClearer;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Throwable;

/**
 * This service is responsible for clearing the Symfony cache, it needs to clear all the
 * caches for the different Symfony applications and environments. But the biggest challenge
 * is that it also needs to clear its own cache while still being executed.
 *
 * This topic gave us a lot of trouble throughout the years and caused many different unexpected
 * side effects, over the years some strategies were set to overcome these side effects:
 *
 *  - the first one is that you CANNOT remove the cache right at the moment you ask for it (for
 *    example right after a module installation), because the process still needs to handle many
 *    other steps, if the cache is cleared in the middle of the process if will create bugs for the
 *    remaining code in the process To overcome this we use register_shutdown_function which means
 *    the cache clearing will be executed at the very end of the process when all the rest has been
 *    done
 *  - the second issue is that while you are clearing the cache some other processes (concurrent ajax
 *    requests, or other opened tabs) may start and start booting the kernel while its cache is being
 *    built, and it creates bugs with multiple cache folders being used. To avoid this we use a custom
 *    lock system At the very moment the SymfonyCacheClear::clear method is called we lock the current
 *    application and all the other applications/environments, so all the following requests will be stalled
 *    and will remain in a waiting state until the lock is released. They must all be locked right away, before
 *    the response is sent: a request booting before its lock exists (the redirection that follows a form
 *    submit, for example) would not wait and would load a container while it is being removed. The other
 *    applications/environments are unlocked once they are cleared, at the end of the clear operations (when
 *    all environments have been cleared) we finally release the current process which initiated the global
 *    clearing
 *  - not all environments are similar and some have strong restrictions (unable to use exec function
 *    php binary not accessible in the cache, ...) so no solution is guaranteed to work everywhere, which
 *    is why we implemented multiple KernelCacheClearerInterface that can adapt to the environment, this
 *    service will execute them in an order defined by us (based on their potential to work correctly) and
 *    if one solution fails it will fallback to the next one
 *
 * @internal
 */
final class SymfonyCacheClearer implements CacheClearerInterface
{
    use SafeLoggerTrait;

    private const ENVIRONMENTS = ['prod', 'dev'];

    private const APPLICATION_KERNEL_CLASSES = [AdminKernel::class, AdminAPIKernel::class, FrontKernel::class];

    private bool $clearCacheRequested = false;

    public function __construct(
        #[TaggedIterator('prestashop.kernel.cache_clearer')]
        protected readonly iterable $kernelCacheClearers,
        protected readonly LoggerInterface $logger,
        protected readonly LegacyCacheClearer $legacyCacheClearer,
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

        // Lock current App right away
        $cacheClearLocked = CacheClearLocker::lock($kernel->getEnvironment(), $kernel->getAppId());
        if (false === $cacheClearLocked) {
            // The lock was not possible for some reason we should exit
            return;
        }

        // Lock all the other applications and environments right away too, the lock must exist before the response is sent
        $this->lockOtherApps($kernel);

        // If we reach here it means the clear lock files are locked, we register a shutdown function that will clear the cache once
        // the current process is over.
        register_shutdown_function(function () use ($kernel) {
            try {
                // Remove time and memory limits to make sure the cache has enough time and memory to be fully cleared
                @set_time_limit(0);
                @ini_set('memory_limit', '-1');

                foreach (self::ENVIRONMENTS as $environment) {
                    /** @var AppKernel[] $applicationKernels */
                    $applicationKernels = [];

                    // All the applications are already locked (see lockOtherApps), since the legacy cache is only bound
                    // to an environment, not a particular App, it is potentially shared by these applications. So we clear
                    // them all, clear the common legacy cache And only then can we unlock them all
                    foreach (self::APPLICATION_KERNEL_CLASSES as $applicationKernelClass) {
                        // Store each kernel to avoid creating them again in the other loops
                        $applicationKernels[] = new $applicationKernelClass($environment, false);
                    }

                    // Now clear the kernels' cache
                    foreach ($applicationKernels as $applicationKernel) {
                        $cacheDir = $applicationKernel->getCacheDir();

                        // If there is no cache folder for this symfony app no need to run a cache clear, it would only take longer
                        if (!file_exists($cacheDir)) {
                            $this->logDebug('SymfonyCacheClearer: No symfony cache to clear for ' . $applicationKernel->getAppId() . ' env ' . $environment);
                            continue;
                        }

                        $kernelCacheCleared = false;
                        /** @var KernelCacheClearerInterface $cacheClearer */
                        foreach ($this->kernelCacheClearers as $cacheClearer) {
                            try {
                                // If one clearer succeeds it is enough we can stop the loop
                                if ($kernelCacheCleared = $cacheClearer->clearKernelCache($applicationKernel, $environment)) {
                                    break;
                                }
                            } catch (Throwable $e) {
                                $this->logError(sprintf(
                                    'SymfonyCacheClearer: Error while clearing cache for %s env %s using %s: %s',
                                    $applicationKernel->getAppId(),
                                    $environment,
                                    get_class($cacheClearer),
                                    $e->getMessage(),
                                ));
                            }
                        }

                        if (!$kernelCacheCleared) {
                            $this->logError('SymfonyCacheClearer: No clearers were able to clear cache for ' . $applicationKernel->getAppId() . ' env ' . $environment);
                        }
                    }

                    // The legacy cache is independent of the Symfony one, we integrated the LegacyCacheClearer in the list of
                    // cache clearer that the cache:clear command uses, but in case the Symfony cache:clear failed we perform an
                    // addition clear here for safety
                    // Note: if the first available clearers failed, and the FilesystemKernelCacheClearer fallback was used then
                    // this is necessary, or if no symfony apps have a cache folder but the legacy one still has some, then it is
                    // also needed independently
                    $this->clearLegacyCache($kernel, $environment);

                    // Finally unlock the kernels
                    foreach ($applicationKernels as $applicationKernel) {
                        // Unlock the app ONLY IF it is not the current one
                        $this->unlockOtherApp($kernel, $applicationKernel->getEnvironment(), $applicationKernel->getAppId());
                    }
                }
            } catch (Throwable $e) {
                $this->logError('SymfonyCacheClearer: Something went wrong while clearing cache: ' . $e->getMessage());
            } finally {
                Hook::exec('actionClearSf2Cache');
                // If something went wrong some other applications may still be locked, unlock them before the current one
                foreach (self::ENVIRONMENTS as $environment) {
                    foreach (self::APPLICATION_KERNEL_CLASSES as $applicationKernelClass) {
                        $this->unlockOtherApp($kernel, $environment, $applicationKernelClass::APP_ID);
                    }
                }
                // Finally unlock the current App
                CacheClearLocker::unlock($kernel->getEnvironment(), $kernel->getAppId());
            }
        });
    }

    protected function lockOtherApps(AppKernel $currentKernel): void
    {
        foreach (self::ENVIRONMENTS as $environment) {
            foreach (self::APPLICATION_KERNEL_CLASSES as $applicationKernelClass) {
                // The current App is already locked
                if ($environment === $currentKernel->getEnvironment() && $applicationKernelClass::APP_ID === $currentKernel->getAppId()) {
                    continue;
                }

                // If another process is already clearing this App its lock fails, the App remains locked by that process
                CacheClearLocker::lock($environment, $applicationKernelClass::APP_ID);
            }
        }
    }

    protected function unlockOtherApp(AppKernel $currentKernel, string $otherEnvironment, string $otherAppId): void
    {
        // We don't unlock the current process during the loop, this will be done in the "finally" block at the end of the loop
        if ($otherEnvironment === $currentKernel->getEnvironment() && $otherAppId === $currentKernel->getAppId()) {
            return;
        }

        CacheClearLocker::unlock($otherEnvironment, $otherAppId);
    }

    protected function clearLegacyCache(AppKernel $currentKernel, string $environment): void
    {
        // Now remove the legacy cache, because so far only the kernel symfony cache folder has been cleared
        // (see LegacyCacheClearer for more details)
        $environmentCacheFolder = $currentKernel->getProjectDir() . '/var/cache/' . $environment;
        // We create an instance for each environment because the relevant cache path is the one passed in the
        // constructor not the one in the method
        $legacyCacheClearer = new LegacyCacheClearer($environmentCacheFolder);

        try {
            $legacyCacheClearer->clear($environmentCacheFolder);
        } catch (Throwable $e) {
            $this->logError(sprintf(
                'SymfonyCacheClearer: Error while trying removing legacy cache folder: %s',
                $e->getMessage()
            ));
        }
    }
}
