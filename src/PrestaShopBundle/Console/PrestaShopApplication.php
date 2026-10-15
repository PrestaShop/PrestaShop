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
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dedicated application for Symfony console that addition adds default parameter app-id
 * to all the commands to allow switching from one kernel to another.
 */
class PrestaShopApplication extends Application
{
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        // Multishop options must be registered before input parsing, but the
        // kernel must be booted first to check whether multishop is enabled.
        $kernel = $this->getKernel();
        $kernel->boot();

        // Some CLI commands can run without a database configuration,
        // so avoid checking the multishop feature when it is unavailable.
        if (defined('_DB_PREFIX_') && $kernel->getContainer()->get(MultistoreFeature::class)->isUsed()) {
            $definition = $this->getDefinition();

            $definition->addOption(new InputOption(
                'id_shop',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify shop context.'
            ));

            $definition->addOption(new InputOption(
                'id_shop_group',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify shop group context.'
            ));
        }

        return parent::doRun($input, $output);
    }

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
