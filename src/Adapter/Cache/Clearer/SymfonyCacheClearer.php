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

use Hook;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Tools;

/**
 * Class SymfonyCacheClearer clears Symfony cache directly from filesystem.
 *
 * @internal
 */
final class SymfonyCacheClearer implements CacheClearerInterface
{
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        /*  @var KernelInterface */
        global $kernel;

        if (empty($kernel)) {
            Tools::clearSf2Cache();

            return;
        }

        register_shutdown_function(function () use ($kernel) {
            // The cache may have been removed by Tools::clearSf2Cache, it happens during install
            // process, in which case we don't run the cache:clear command because it is not only
            // useless it will simply fail as the container caches classes have been removed
            $cacheDir = _PS_ROOT_DIR_ . '/var/cache/' . _PS_ENV_ . '/';
            if (!file_exists($cacheDir)) {
                return;
            }

            $application = new Application($kernel);
            $application->setAutoExit(false);

            // Clear cache
            $input = new ArrayInput([
                'command' => 'cache:clear',
                '--no-warmup' => true,
                '--env' => _PS_ENV_,
            ]);

            $output = new NullOutput();
            $application->run($input, $output);

            // Reboot kernel
            $realCacheDir = $kernel->getContainer()->getParameter('kernel.cache_dir');
            $warmupDir = substr($realCacheDir, 0, -1) . ('_' === substr($realCacheDir, -1) ? '-' : '_');
            $kernel->reboot($warmupDir);

            Hook::exec('actionClearSf2Cache');
        });
    }
}
