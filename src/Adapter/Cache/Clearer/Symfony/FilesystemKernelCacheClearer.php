<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony;

use AppKernel;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SafeLoggerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

/**
 * This clearer uses a manual removal of files directly on the file system to clear
 * the kernel cache.
 *
 * It is the least favored method to clear because:
 *  - in the past this simple technique created some side effects
 *  - it doesn't initialize the future container like the cache:clear command does
 *
 * Note: we don't add too many try/catch because the SymfonyCacheClearer already wraps this service,
 * it allows keeping the code simpler in this service.
 */
#[AutoconfigureTag('prestashop.kernel.cache_clearer')]
#[AsTaggedItem(priority: -1)]
class FilesystemKernelCacheClearer implements KernelCacheClearerInterface
{
    use SafeLoggerTrait;

    public const MANUAL_REMOVAL_TRIALS = 5;

    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly Filesystem $filesystem,
    ) {
    }

    public function clearKernelCache(AppKernel $kernel, string $environment): bool
    {
        $cacheDir = $kernel->getCacheDir();
        for ($i = 0; $i < self::MANUAL_REMOVAL_TRIALS; ++$i) {
            try {
                $this->logDebug(sprintf(
                    'FilesystemKernelCacheClearer: Trying manual removal on trial %d/%d of cache folder %s',
                    $i + 1,
                    self::MANUAL_REMOVAL_TRIALS, $cacheDir
                ));
                $this->filesystem->remove($cacheDir);
                // The whole folder was removed
                if (!is_dir($cacheDir)) {
                    break;
                }
            } catch (Throwable $e) {
                $this->logError(sprintf(
                    'FilesystemKernelCacheClearer: Error while trying removing cache folder on trial %d/%d: %s',
                    $e->getMessage(),
                    $i + 1,
                    self::MANUAL_REMOVAL_TRIALS
                ));
            }
        }

        // The folder is still present
        if (is_dir($cacheDir)) {
            $this->logError(sprintf('FilesystemKernelCacheClearer: Folder cache %s still present even after %d manual removals', $cacheDir, self::MANUAL_REMOVAL_TRIALS));

            return false;
        }

        $this->logInfo(sprintf('FilesystemKernelCacheClearer: Cache folder %s successfully cleared manually', $cacheDir));

        return true;
    }
}
