<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Converter;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class RouterProvider.
 */
class RouterProvider extends AbstractLegacyRouteProvider
{
    public const LEGACY_LINK_ROUTE_ATTRIBUTE = '_legacy_link';
    public const FEATURE_FLAG_NAME = '_legacy_feature_flag';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LegacyRouteFactory
     */
    private $factory;

    /**
     * @var array|null
     */
    private $legacyRoutes;

    public function __construct(RouterInterface $router, LegacyRouteFactory $factory)
    {
        $this->router = $router;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getLegacyRoutes()
    {
        if (null !== $this->legacyRoutes) {
            return $this->legacyRoutes;
        }

        $this->legacyRoutes = $this->factory->buildFromCollection(
            $this->router->getRouteCollection()
        );

        return $this->legacyRoutes;
    }
}
