<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Position;

use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * This is a service locator that allows fetching position definitions via their index.
 */
class PositionDefinitionProvider
{
    public function __construct(
        #[AutowireLocator('core.grid_position_definition')]
        protected ServiceProviderInterface $positionDefinitions
    ) {
    }

    public function getPositionDefinition(string $name): PositionDefinitionInterface
    {
        return $this->positionDefinitions->get($name);
    }
}
