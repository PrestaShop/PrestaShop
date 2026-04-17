<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Linter;

use Symfony\Component\Routing\Route;

/**
 * Responsible for checking route _legacy_link configuration
 */
final class LegacyLinkLinter implements RouteLinterInterface
{
    /**
     * Checks if _legacy_link is configured to route.
     * Returns true if configured, false if not.
     *
     * {@inheritdoc}
     */
    public function lint($routeName, Route $route)
    {
        // Legacy link already configured
        if ($route->hasDefault('_legacy_link')) {
            return true;
        }

        // Route is not related to a legacy controller so legacy link is not relevant
        if (!$route->hasDefault('_legacy_controller')) {
            return true;
        }

        return false;
    }
}
