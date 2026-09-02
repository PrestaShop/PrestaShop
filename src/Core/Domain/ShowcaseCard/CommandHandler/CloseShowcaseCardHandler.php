<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Command\CloseShowcaseCardCommand;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ConfigurationMap;

/**
 * Saves the showcase card status to keep it closed
 */
#[AsCommandHandler]
final class CloseShowcaseCardHandler implements CloseShowcaseCardHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var ConfigurationMap
     */
    private $configurationMap;

    /**
     * CloseShowcaseCardHandler constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param ConfigurationMap $configurationMap
     */
    public function __construct(ConfigurationInterface $configuration, ConfigurationMap $configurationMap)
    {
        $this->configuration = $configuration;
        $this->configurationMap = $configurationMap;
    }

    /**
     * @param CloseShowcaseCardCommand $command
     */
    public function handle(CloseShowcaseCardCommand $command)
    {
        $configurationName = $this->configurationMap->getConfigurationNameForClosedStatus($command->getShowcaseCard());
        $this->configuration->set($configurationName, '1');
    }
}
