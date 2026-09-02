<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Routes;

use Dispatcher;
use PrestaShopException;
use Validate;

/**
 * Class RouteValidator is responsible for validating routes.
 */
class RouteValidator
{
    /**
     * Check for a route pattern validity.
     *
     * @param string $pattern to validate
     *
     * @return bool Validity is ok or not
     */
    public function isRoutePattern($pattern)
    {
        return Validate::isRoutePattern($pattern);
    }

    /**
     * @deprecated since 9.0.1, use isRouteValid instead.
     */
    public function doesRouteContainsRequiredKeywords($routeId, $rule)
    {
        $errors = $this->isRouteValid($routeId, $rule);

        return $errors['missing'] ?? [];
    }

    /**
     * Check if a route rule is valid.
     *
     * @param string $routeId
     * @param string $rule Rule to verify
     *
     * @return array - returns list of missing or unknown keywords
     *
     * @throws PrestaShopException
     */
    public function isRouteValid(string $routeId, string $rule): array
    {
        $errors = [];
        $validationResult = Dispatcher::getInstance()->validateRoute($routeId, $rule, $errors);

        return $validationResult ? [] : $errors;
    }
}
