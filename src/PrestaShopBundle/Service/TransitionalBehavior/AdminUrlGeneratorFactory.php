<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Service\TransitionalBehavior;

use PrestaShop\PrestaShop\Adapter\Admin\UrlGenerator as LegacyUrlGenerator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

/**
 * Factory to return a UrlGeneratorInterface.
 * Either the base generator from Symfony (the Router class instance)
 * Either an Adapter for Admin legacy controllers.
 */
class AdminUrlGeneratorFactory
{
    /**
     * @var Router
     */
    private $router;

    /**
     * Constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Gets the UrlGeneratorInterface subclass for Legacy Admin controllers.
     *
     * @param LegacyContext $legacyContext The legacy context needed by Legacy UrlGenerator
     *
     * @return UrlGeneratorInterface the UrlGenerator instance for Admin legacy controllers
     */
    public function forLegacy(LegacyContext $legacyContext)
    {
        return new LegacyUrlGenerator($legacyContext, $this->router);
    }

    /**
     * Gets the UrlGeneratorInterface subclass for Symfony routes.
     *
     * @return UrlGeneratorInterface the UrlGenerator instance for Admin Symfony routes
     */
    public function forSymfony()
    {
        return $this->router;
    }
}
