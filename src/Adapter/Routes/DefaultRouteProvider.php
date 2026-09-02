<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
