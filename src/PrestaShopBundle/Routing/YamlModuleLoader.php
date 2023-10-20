<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Routing;

use RuntimeException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * This class is responsible of loading routes of enabled modules.
 */
class YamlModuleLoader extends Loader
{
    /**
     * @var array the list of activated modules
     */
    private $activeModulesPaths;

    /**
     * @var bool we load the route collection only once per request
     */
    private $isLoaded = false;

    public function __construct(array $activeModulesPaths)
    {
        $this->activeModulesPaths = $activeModulesPaths;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->isLoaded) {
            throw new RuntimeException('Do not add the "module" loader twice.');
        }

        $routes = new RouteCollection();

        foreach ($this->activeModulesPaths as $modulePath) {
            $routingFile = $modulePath . '/config/routes.yml';
            if (file_exists($routingFile)) {
                $loadedRoutes = $this->import($routingFile, 'yaml');
                $routes->addCollection($loadedRoutes);
            }
        }

        $this->isLoaded = true;

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'module' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function import($resource, $type = null)
    {
        $loadedRoutes = parent::import($resource, $type);

        return $this->modifyRoutes($loadedRoutes);
    }

    /**
     * @param RouteCollection $routes
     *
     * @return RouteCollection
     */
    private function modifyRoutes(RouteCollection $routes)
    {
        foreach ($routes->getIterator() as $route) {
            if ($route->hasDefault('_disable_module_prefix') && $route->getDefault('_disable_module_prefix') === true) {
                continue;
            }

            $route->setPath('/modules' . $route->getPath());
        }

        return $routes;
    }
}
