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

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony;

use AppKernel;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SafeLoggerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

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
class ExecKernelCacheClearer implements KernelCacheClearerInterface
{
    use SafeLoggerTrait;

    public function __construct(
        protected readonly LoggerInterface $logger,
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

        return $this->execCommand(
            $kernel,
            'cache:warmup --no-optional-warmers --no-interaction --env=' . $environment . ' --app-id=' . $kernel->getAppId() . ' 2>&1',
            'ExecKernelCacheClearer: Successfully warmed up cache for %s env ' . $environment,
            'ExecKernelCacheClearer: Could not warm up cache for %s env ' . $environment . ' result: %d output: %s'
        );
    }

    protected function execCommand(AppKernel $kernel, string $command, string $successMessage, $errorMessage): bool
    {
        $commandLine = 'php -d memory_limit=-1 ' . $kernel->getProjectDir() . '/bin/console ' . $command;
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

    protected function isExecDisabled(): bool
    {
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        array_walk($disabledFunctions, fn ($disabledFunction) => trim($disabledFunction));

        return in_array('exec', $disabledFunctions);
    }
}
