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

namespace PrestaShop\PrestaShop\Adapter\Bundle;

use PrestaShopBundle\Console\PrestaShopApplication;
use PrestaShopException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AssetsInstaller aim to install bundle assets into filesystem.
 *
 * @internal
 */
final class AssetsInstaller
{
    public function installAssets(string $adminFolder): void
    {
        // We retrieve the kernel from the global scope
        global $kernel;
        if (!$kernel instanceof KernelInterface) {
            throw new PrestaShopException('Kernel is not initialized. Cannot install assets.');
        }

        // We need to use the PrestaShopApplication to run the command
        $application = new PrestaShopApplication($kernel);
        $application->setAutoExit(false);

        // Run the command to install bundles assets
        // (for now! maybe we should use another way to install assets in the future)
        $output = new BufferedOutput();
        $errorCode = $application->run(new ArrayInput([
            'command' => 'assets:install',
            'target' => $adminFolder,
        ]), $output);

        // If the command failed (!= 0), we throw an exception with the output of the command
        if (0 !== $errorCode) {
            throw new PrestaShopException(sprintf(
                'Failed to install bundle assets: %s',
                $output->fetch()
            ));
        }
    }
}
