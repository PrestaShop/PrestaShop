<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\GridView;

use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewConstraintException;
use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewLimitReachedException;
use PrestaShop\PrestaShop\Core\Domain\GridView\GridViewSettings;
use PrestaShopBundle\Entity\AdminGridConfiguration;
use Symfony\Component\Routing\RouterInterface;

/**
 * Validates entity-level business rules of grid views.
 */
class GridViewValidator
{
    /**
     * @param RouterInterface $router
     */
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @param string $routeName
     *
     * @return void
     *
     * @throws GridViewConstraintException
     */
    public function assertRouteExists(string $routeName): void
    {
        if (null === $this->router->getRouteCollection()->get($routeName)) {
            throw new GridViewConstraintException(
                sprintf('Unknown route "%s"', $routeName),
                GridViewConstraintException::UNKNOWN_ROUTE
            );
        }
    }

    /**
     * @param AdminGridConfiguration $configuration
     *
     * @return void
     *
     * @throws GridViewLimitReachedException
     */
    public function assertViewLimitIsNotReached(AdminGridConfiguration $configuration): void
    {
        if ($configuration->getViews()->count() >= GridViewSettings::MAX_VIEWS_PER_CONFIGURATION) {
            throw new GridViewLimitReachedException(
                sprintf('A grid configuration cannot hold more than %d views', GridViewSettings::MAX_VIEWS_PER_CONFIGURATION)
            );
        }
    }
}
