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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Routes;

use Dispatcher;
use PrestaShopException;

/**
 * Class DefaultRouteProvider is responsible for retrieving data from dispatcher entity.
 */
class DefaultRouteProvider
{
    /**
     * Gets keywords used in generating different routes.
     *
     * @return array - the key is the route id  - product_rule, category_rule etc... and the values are keyword array
     *               used to generate the route. If param field exists in keywords array then it is mandatory field to use.
     *
     * @throws PrestaShopException
     */
    public function getKeywords()
    {
        $routes = $this->getDefaultRoutes();

        $result = [];
        foreach ($routes as $routeId => $value) {
            $result[$routeId] = $value['keywords'];
        }

        return $result;
    }

    /**
     * Gets rules which are used for routes generation.
     *
     * @return array - he key is the route id  - product_rule, category_rule etc... and the value is rule itself.
     *
     * @throws PrestaShopException
     */
    public function getRules()
    {
        $routes = $this->getDefaultRoutes();

        $result = [];
        foreach ($routes as $routeId => $value) {
            $result[$routeId] = $value['rule'];
        }

        return $result;
    }

    /**
     * Gets default routes which contains data such as keywords, rule etc.
     *
     * @return array
     *
     * @throws PrestaShopException
     */
    private function getDefaultRoutes()
    {
        return Dispatcher::getInstance()->default_routes;
    }
}
