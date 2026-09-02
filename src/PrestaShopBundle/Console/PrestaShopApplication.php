<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Console;

use AdminAPIKernel;
use AdminKernel;
use FrontKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * Dedicated application for Symfony console that addition adds default parameter app-id
 * to all the commands to allow switching from one kernel to another.
 */
class PrestaShopApplication extends Application
{
    protected function getDefaultInputDefinition(): InputDefinition
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption(
            'app-id',
            null,
            InputOption::VALUE_REQUIRED,
            'Specify a PrestaShop Application ID.',
            'admin',
            [AdminKernel::APP_ID, AdminAPIKernel::APP_ID, FrontKernel::APP_ID]
        ));

        return $definition;
    }

    public function getLongVersion(): string
    {
        return sprintf(
            'PrestaShop %s with %s',
            _PS_VERSION_,
            parent::getLongVersion()
        );
    }
}
