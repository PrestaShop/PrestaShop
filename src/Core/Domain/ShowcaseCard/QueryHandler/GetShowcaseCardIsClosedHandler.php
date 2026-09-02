<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ConfigurationMap;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Exception\ShowcaseCardException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;

/**
 * Finds out if a showcase card has been closed
 */
#[AsQueryHandler]
final class GetShowcaseCardIsClosedHandler implements GetShowcaseCardIsClosedHandlerInterface
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
     * @param ConfigurationInterface $configuration
     * @param ConfigurationMap $configurationMap
     */
    public function __construct(ConfigurationInterface $configuration, ConfigurationMap $configurationMap)
    {
        $this->configuration = $configuration;
        $this->configurationMap = $configurationMap;
    }

    /**
     * @param GetShowcaseCardIsClosed $query
     *
     * @return bool
     *
     * @throws ShowcaseCardException
     */
    public function handle(GetShowcaseCardIsClosed $query)
    {
        $configurationName = $this->configurationMap->getConfigurationNameForClosedStatus($query->getShowcaseCard());

        return (bool) $this->configuration->get($configurationName);
    }
}
