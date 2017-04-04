<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Service\TransitionalBehavior;

use Symfony\Component\Routing\Router;
use PrestaShop\PrestaShop\Adapter\Admin\UrlGenerator as LegacyUrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Adapter\LegacyContext;

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
     * @return UrlGeneratorInterface The UrlGenerator instance for Admin legacy controllers.
     */
    public function forLegacy(LegacyContext $legacyContext)
    {
        return new LegacyUrlGenerator($legacyContext, $this->router);
    }

    /**
     * Gets the UrlGeneratorInterface subclass for Symfony routes.
     *
     * @return UrlGeneratorInterface The UrlGenerator instance for Admin Symfony routes.
     */
    public function forSymfony()
    {
        return $this->router;
    }
}
