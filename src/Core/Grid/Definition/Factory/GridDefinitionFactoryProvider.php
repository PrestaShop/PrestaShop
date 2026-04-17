<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * This is a service locator that allows fetching grid definition factories via their index.
 */
class GridDefinitionFactoryProvider
{
    public function __construct(
        #[AutowireLocator('core.grid_definition_factory')]
        protected ServiceProviderInterface $factories
    ) {
    }

    public function getFactory(string $name): GridDefinitionFactoryInterface
    {
        return $this->factories->get($name);
    }
}
