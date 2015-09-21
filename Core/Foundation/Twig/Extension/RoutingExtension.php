<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Core\Foundation\Routing\RoutingService;

/**
 * This class is used by Twig_Environment and provide some methods callable from a twig template
 */
class RoutingExtension extends \Twig_Extension
{
    private $routing;

    /**
     * Constructor : Inject Routing service
     *
     * @param RoutingService $routing
     */
    public function __construct(RoutingService $routing)
    {
        $this->routing = $routing;
    }

    /**
     * Define available functions
     *
     * @return array Twig_SimpleFunction
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('url', array($this, 'getUrl')),
            new \Twig_SimpleFunction('url_legacy', array($this, 'getLegacyUrl')),
        );
    }

    /**
     * This method wrap the routing generateUrl method for new urls
     *
     * @param string $name
     * @param array $parameters
     * @param bool|string $referenceType The type of reference to be generated (one of the constants)
     *
     * @return string The generated URL
     */
    public function getUrl($name, array $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        return $this->routing->generateUrl($name, $parameters, false, $referenceType);
    }

    /**
     * This method wrap the routing generateUrl method for legacy urls
     *
     * @param string $name
     * @param array $parameters
     * @param bool|string $referenceType The type of reference to be generated (one of the constants)
     *
     * @return string The generated URL
     */
    public function getLegacyUrl($name, array $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        return $this->routing->generateUrl($name, $parameters, true, $referenceType);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_routing_extension';
    }
}
