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
namespace PrestaShop\PrestaShop\Core\Business\Routing;

use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Business\Context;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FrontRouter extends Router
{
    /**
     * Constructor. Instantiated from index.php files only.
     *
     * @param \Core_Foundation_IoC_Container $container The Service Container
     */
    final public function __construct(\Core_Foundation_IoC_Container &$container = null)
    {
        parent::__construct($container, 'admin_routes(_(.*))?\.yml');
        $container->make('Context')->set('app_entry_point', 'front');
    }

    final protected function checkControllerAuthority(\ReflectionClass $class)
    {
        if ((!$class->isSubclassOf('PrestaShop\\PrestaShop\\Core\\Business\\Controller\\FrontController')
                && (!$class->getName() == 'PrestaShop\PrestaShop\Core\Business\Controller\FrontController'))
            || $class->isSubclassOf('PrestaShop\\PrestaShop\\Core\\Business\\Controller\\AdminController')) {
            throw new DevelopmentErrorException('Front router tried to call a non-front controller ('.$class->name.'). Please verify your routes Settings, and controllers.', null, 1004);
        }
    }

    /**
     * Generates a URL or path for a specific Front route based on the given parameters.
     *
     * This is a Wrapper for the Symfony method:
     * @see \Symfony\Component\Routing\Generator\UrlGeneratorInterface::generate()
     * but also adds a legacy URL generation support for Front interface.
     *
     * @param string      $name             The name of the route
     * @param mixed       $parameters       An array of parameters (to use in route matching, or to add as GET values if $forceLegacyUrl is True)
     * @param bool        $forceLegacyUrl   True to use alternative URL to reach legacy dispatcher.
     * @param bool|string $referenceType The type of reference to be generated (one of the constants)
     *
     * @return string The generated URL
     *
     * @throws RouteNotFoundException              If the named route doesn't exist
     * @throws MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * @throws InvalidParameterException           When a parameter value for a placeholder is not correct because
     *                                             it does not match the requirement
     */
    final public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        if (($routeParams = $this->getRouteParameters($name)) &&
            ($defaultParams = $routeParams->getDefaults()) &&
            ($forceLegacyUrl == true || (isset($defaultParams['_legacy_force']) && $defaultParams['_legacy_force'] === true)) &&
            isset($defaultParams['_legacy_path']) &&
            ($link = $this->container->make('Context')->link)) { // For legacy case!
            // get it from Adapter
            return (new UrlGenerator())->generateFrontLegacyUrlForNewArchitecture(
                $defaultParams,
                $this->container);
        }
        return parent::generateUrl($name, $parameters, false, $referenceType);
    }
}
