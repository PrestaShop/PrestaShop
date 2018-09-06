<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use RuntimeException;

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
}
