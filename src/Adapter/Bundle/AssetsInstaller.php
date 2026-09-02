<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            '--symlink' => true,
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
