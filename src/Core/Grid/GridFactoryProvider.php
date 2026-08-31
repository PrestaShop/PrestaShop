<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid;

use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * This is a service locator that allows fetching grid factories via their service id.
 *
 * It mirrors GridDefinitionFactoryProvider and is used by the authorized SQL export
 * endpoint to rebuild any opted-in grid server-side.
 */
class GridFactoryProvider
{
    public function __construct(
        #[AutowireLocator('core.grid_factory')]
        protected ServiceProviderInterface $factories
    ) {
    }

    public function getFactory(string $name): GridFactoryInterface
    {
        return $this->factories->get($name);
    }
}
