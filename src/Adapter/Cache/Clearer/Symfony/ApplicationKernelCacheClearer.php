<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony;

use AppKernel;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SafeLoggerTrait;
use PrestaShopBundle\Console\PrestaShopApplication;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpKernel\Kernel;

/**
 * This clearer uses an Application function to run the cache:clear command, it is based on the
 * recommended way, by Symfony, of executing command from controllers
 * https://symfony.com/doc/6.4/console/command_in_controller.html
 *
 * It is only the second favored method because all the cache clear are done in the single same
 * initial process, thus it has more risk to cause conflicts or to reach a memory limit than the
 * exec method.
 *
 * Note: we don't add too many try/catch because the SymfonyCacheClearer already wraps this service,
 * it allows keeping the code simpler in this service.
 */
#[AutoconfigureTag('prestashop.kernel.cache_clearer')]
#[AsTaggedItem(priority: 5)]
class ApplicationKernelCacheClearer implements KernelCacheClearerInterface
{
    use SafeLoggerTrait;

    public function __construct(
        protected readonly LoggerInterface $logger,
    ) {
    }

    public function clearKernelCache(AppKernel $kernel, string $environment): bool
    {
        // Clearing the cache from the initiator process is quite unstable, to avoid any side effects we need
        // to clear the environment with and without debug mode enable, or else the generated containers are
        // not always up-to-date So we create two kernels (one for debug mode on, the other for debug mode off)
        // and we need to do each clear steps on both kernels
        $debugKernel = $this->buildKernel($kernel, $environment, true);
        $noDebugKernel = $this->buildKernel($kernel, $environment, false);

        if (!$this->clearCache($debugKernel)) {
            return false;
        }
        if (!$this->clearCache($noDebugKernel)) {
            return false;
        }

        if (!$this->warmUpCache($debugKernel)) {
            return false;
        }
        if (!$this->warmUpCache($noDebugKernel)) {
            return false;
        }

        return true;
    }

    protected function clearCache(AppKernel $kernel): bool
    {
        return $this->runCommand(
            $kernel,
            new ArrayInput([
                'command' => 'cache:clear',
                '--no-warmup' => true,
                '--no-interaction' => true,
                '--env' => $kernel->getEnvironment(),
                '--app-id' => $kernel->getAppId(),
            ]),
            'ApplicationKernelCacheClearer: Successfully cleared cache for %s env %s debug %s',
            'ApplicationKernelCacheClearer: Could not clear cache for %s env %s debug %s result: %d output: %s',
        );
    }

    protected function warmUpCache(AppKernel $kernel): bool
    {
        // We only warm up cache for prod environment
        if ($kernel->getEnvironment() !== 'prod') {
            // No warmup needed so we can stop here
            return true;
        }

        return $this->runCommand(
            $kernel,
            new ArrayInput([
                'command' => 'cache:warmup',
                '--no-optional-warmers' => true,
                '--no-interaction' => true,
                '--env' => $kernel->getEnvironment(),
                '--app-id' => $kernel->getAppId(),
            ]),
            'ApplicationKernelCacheClearer: Successfully warmed up cache for %s env %s debug %s',
            'ApplicationKernelCacheClearer: Could not warmup cache for %s env debug %s %s result: %d output: %s',
        );
    }

    protected function runCommand(AppKernel $kernel, ArrayInput $input, string $successMessage, $errorMessage): bool
    {
        $application = new PrestaShopApplication($kernel);
        $application->setAutoExit(false);

        $output = new BufferedOutput();
        $result = $application->doRun($input, $output);
        if ($result !== 0) {
            $this->logError(sprintf($errorMessage, $kernel->getAppId(), $kernel->getEnvironment(), $kernel->isDebug() ? 'on' : 'off', $result, $output->fetch()));

            return false;
        }

        $this->logInfo(sprintf($successMessage, $kernel->getAppId(), $kernel->getEnvironment(), $kernel->isDebug() ? 'on' : 'off'));

        return true;
    }

    /**
     * We need to create a new kernel object since it will influence the internal default values for environment and debug mode
     */
    protected function buildKernel(AppKernel $kernel, string $environment, bool $debugMode): AppKernel
    {
        $kernelClass = get_class($kernel);

        return new $kernelClass($environment, $debugMode);
    }
}
