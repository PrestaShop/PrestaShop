<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Linter;

use PrestaShopBundle\Routing\Linter\Exception\LinterException;
use Symfony\Component\Routing\Route;

/**
 * Interface for service that performs linting on route
 */
interface RouteLinterInterface
{
    /**
     * @param Route $route
     *
     * @throws LinterException when linting error occurs
     */
    public function lint($routeName, Route $route);
}
