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

namespace PrestaShop\PrestaShop\Twig\Extension;

use PrestaShop\PrestaShop\Core\Business\Context;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RoutingExtension extends \Twig_Extension
{
    private $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('url', array($this, 'getUrl')),
            new \Twig_SimpleFunction('url_legacy', array($this, 'getLegacyUrl')),
        );
    }

    public function getUrl($name, array $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        return $this->router->generateUrl($name, $parameters, false, $referenceType);
    }

    public function getLegacyUrl($name, array $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        $context = Context::getInstance();
        return $context->getRouter()->generateUrl($name, $parameters, true, $referenceType);
    }

    public function getName()
    {
        return 'twig_routing_extension';
    }
}
