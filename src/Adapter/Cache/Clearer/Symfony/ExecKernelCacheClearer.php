<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony;

use AppKernel;
use PrestaShop\Autoload\PrestashopAutoload;
use PrestaShop\PrestaShop\Adapter\Cache\CachingConfiguration;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SafeLoggerTrait;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * This clearer uses exec function to run the bin/console cache:clear command in a separate process,
 * so it reduces the risk of memory limits.
 *
 * It is the favored method to clear the cache so far.
 *
 * Note: we don't add too many try/catch because the SymfonyCacheClearer already wraps this service,
 * it allows keeping the code simpler in this service.
 */
#[AutoconfigureTag('prestashop.kernel.cache_clearer')]
#[AsTaggedItem(priority: 10)]
class ExecKernelCacheClearer implements DeferredKernelCacheClearerInterface
{
    use SafeLoggerTrait;

    /**
     * @var list<array{AppKernel, string}>
     */
    private array $pendingWarmUps = [];

    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly ShopConfigurationInterface $configuration,
    ) {
    }

    public function clearKernelCache(AppKernel $kernel, string $environment): bool
    {
        if ($this->isExecDisabled()) {
            $this->logWarning(sprintf(
                'ExecKernelCacheClearer: Could not clear cache for %s env %s because exec function is disabled',
                $kernel->getAppId(),
                $environment,
            ));

            return false;
        }

        if (!$this->clearCache($kernel, $environment)) {
            return false;
        }
        if (!$this->warmUpCache($kernel, $environment)) {
            return false;
        }

        return true;
    }

    protected function clearCache(AppKernel $kernel, string $environment): bool
    {
        return $this->execCommand(
            $kernel,
            'cache:clear --no-warmup --no-interaction --env=' . $environment . ' --app-id=' . $kernel->getAppId() . ' 2>&1',
            'ExecKernelCacheClearer: Successfully cleared cache for ' . $environment . ' env %s',
            'ExecKernelCacheClearer: Could not clear cache for %s env ' . $environment . ' result: %d output: %s',
        );
    }

    protected function warmUpCache(AppKernel $kernel, string $environment): bool
    {
        // We only warm up cache for prod environment
        if ($environment !== 'prod') {
            // No warmup needed so we can stop here
            return true;
        }

        $command = 'cache:warmup --no-optional-warmers --no-interaction --env=' . $environment . ' --app-id=' . $kernel->getAppId();
        if (!$this->isParallelWarmUpEnabled() || !function_exists('proc_open')) {
            return $this->execCommand(
                $kernel,
                $command . ' 2>&1',
                'ExecKernelCacheClearer: Successfully warmed up cache for %s env ' . $environment,
                'ExecKernelCacheClearer: Could not warm up cache for %s env ' . $environment . ' result: %d output: %s'
            );
        }

        $this->pendingWarmUps[] = [$kernel, $command];

        return true;
    }

    /**
     * The class index is rebuilt first, so that the parallel warmups only read it.
     */
    public function finishKernelCacheClear(): void
    {
        $pendingWarmUps = $this->pendingWarmUps;
        $this->pendingWarmUps = [];
        if (empty($pendingWarmUps)) {
            return;
        }

        try {
            PrestashopAutoload::getInstance()->generateIndex();
        } catch (Throwable $e) {
            $this->logWarning('ExecKernelCacheClearer: Could not rebuild the class index before warming up: ' . $e->getMessage());
        }

        $warmUps = array_map(fn (array $warmUp): array => $this->startWarmUp(...$warmUp), $pendingWarmUps);
        foreach ($warmUps as $warmUp) {
            $this->waitForWarmUp(...$warmUp);
        }
    }

    protected function execCommand(AppKernel $kernel, string $command, string $successMessage, $errorMessage): bool
    {
        $commandLine = $this->getCommandLine($kernel, $command);
        $output = [];
        $result = 0;
        exec($commandLine, $output, $result);

        if ($result !== 0) {
            $this->logError(sprintf($errorMessage, $kernel->getAppId(), $result, var_export($output, true)));

            return false;
        }

        $this->logInfo(sprintf($successMessage, $kernel->getAppId()));

        return true;
    }

    /**
     * @return array{AppKernel, ?Process}
     */
    private function startWarmUp(AppKernel $kernel, string $command): array
    {
        $process = Process::fromShellCommandline($this->getCommandLine($kernel, $command), timeout: null);
        try {
            $process->start();
        } catch (Throwable $e) {
            $this->onWarmUpFailure($kernel, $e->getMessage());

            return [$kernel, null];
        }

        return [$kernel, $process];
    }

    private function waitForWarmUp(AppKernel $kernel, ?Process $process): void
    {
        if ($process === null) {
            return;
        }

        $process->wait();
        if ($process->isSuccessful()) {
            $this->logInfo(sprintf('ExecKernelCacheClearer: Successfully warmed up cache for %s env %s', $kernel->getAppId(), $kernel->getEnvironment()));

            return;
        }

        $this->onWarmUpFailure($kernel, sprintf('result: %d output: %s', $process->getExitCode(), $process->getOutput() . $process->getErrorOutput()));
    }

    private function onWarmUpFailure(AppKernel $kernel, string $details): void
    {
        $this->logError(sprintf('ExecKernelCacheClearer: Could not warm up cache for %s env %s %s', $kernel->getAppId(), $kernel->getEnvironment(), $details));
        (new Filesystem())->remove($kernel->getCacheDir());
    }

    protected function isParallelWarmUpEnabled(): bool
    {
        return (bool) $this->configuration->get(CachingConfiguration::PARALLEL_WARMUP, false, ShopConstraint::allShops());
    }

    protected function getCommandLine(AppKernel $kernel, string $command): string
    {
        return 'php -d memory_limit=-1 ' . $kernel->getProjectDir() . '/bin/console ' . $command;
    }

    protected function isExecDisabled(): bool
    {
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        array_walk($disabledFunctions, fn ($disabledFunction) => trim($disabledFunction));

        return in_array('exec', $disabledFunctions);
    }
}
