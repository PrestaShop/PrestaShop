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
namespace PrestaShop\PrestaShop\Adapter;

use Symfony\Component\Routing\Route;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * This class contains specific code for legacy URLs generation from new Architecture Router system.
 */
class UrlGenerator
{
    /**
     * Generates a link in 'legacy style' to link to old Admin pages.
     *
     * Called by Router.
     * Do not use this directly, but prefers to use *Router->generateUrl() and *Controller->generateUrl().
     *
     * @param Route $routeParams
     * @param array $defaultParams
     * @param array $parameters
     * @param Container $container
     * @param \Link $link
     * @return string
     */
    final public function generateAdminLegacyUrlForNewArchitecture(Route $routeParams, array $defaultParams, array $parameters, Container $container, \Link $link)
    {
        $legacyPath = $defaultParams['_legacy_path'];

        $legacyContext = $container->make('Adapter_LegacyContext');
        $basePath = $legacyContext->getAdminBaseUrl();

        if ($routeParams->hasOption('legacy_param_mapper_class') && $routeParams->hasOption('legacy_param_mapper_method')) {
            $class = $routeParams->getOption('legacy_param_mapper_class');
            $method = $routeParams->getOption('legacy_param_mapper_method');
            $class = new \ReflectionClass('\\'.$class);
            $method = $class->getMethod($method);
            $legacyParameters = $method->invoke(($method->isStatic())?null:$method->getDeclaringClass()->newInstance(), $parameters);
        } else {
            $legacyParameters = $parameters;
        }

//         switch ($referenceType) {
//             case UrlGeneratorInterface::ABSOLUTE_URL:
//                 return $basePath.$link->getAdminLink($legacyPath);
//             case UrlGeneratorInterface::ABSOLUTE_PATH:
//             default:
           return $basePath.$legacyContext->getAdminLink($legacyPath, true, $legacyParameters);
//         }
    }

    /**
     * Generates a link in 'legacy style' to link to old Front pages.
     * TODO: pretty URLs should not be supported. To add!
     *
     * Called by Router.
     * Do not use this directly, but prefers to use *Router->generateUrl() and *Controller->generateUrl().
     *
     * @param array $defaultParams
     * @param Container $container
     * @return string
     */
    final public function generateFrontLegacyUrlForNewArchitecture(array $defaultParams, Container $container)
    {
        $legacyPath = $defaultParams['_legacy_path'];
        $legacyContext = $container->make('Adapter_LegacyContext');
        return $legacyContext->getFrontUrl($legacyPath);
    }
}
