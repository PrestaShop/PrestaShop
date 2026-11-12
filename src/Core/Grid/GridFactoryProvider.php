<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

class GridFactoryProvider
{
    /**
     * @param ContainerInterface $gridFactoriesLocator
     */
    public function __construct(
        private readonly ContainerInterface $gridFactoriesLocator,
    ) {
    }

    /**
     * @param string $gridId
     *
     * @return GridFactoryInterface|null
     */
    public function getFactory(string $gridId): ?GridFactoryInterface
    {
        if (!$this->gridFactoriesLocator->has($gridId)) {
            return null;
        }

        return $this->gridFactoriesLocator->get($gridId);
    }

    /**
     * @return string[] the grid ids a factory is registered for
     */
    public function getGridIds(): array
    {
        if (!$this->gridFactoriesLocator instanceof ServiceProviderInterface) {
            return [];
        }

        return array_keys($this->gridFactoriesLocator->getProvidedServices());
    }
}
